<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\BlogCategory;
use App\Entity\BlogPost;
use App\Entity\Media;
use App\Entity\Translation\BlogCategoryTranslation;
use App\Entity\Translation\BlogPostTranslation;
use App\Enum\ContentStatus;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Uid\Uuid;

#[AsCommand(
    name: 'app:import-old-blog',
    description: 'Wipe existing blog data and import posts/categories/images from the old go2digital MySQL DB',
)]
class ImportOldBlogCommand extends Command
{
    // Merge HR/EN tag variants into a single HR-first category.
    // Old tag slug → new category slug, hr name, en name
    private const CATEGORY_MAP = [
        'projects'   => ['projekti',    'Projekti',         'Projects'],
        'projekti'   => ['projekti',    'Projekti',         'Projects'],
        'dooh'       => ['dooh',        'DOOH',             'DOOH'],
        'case-study' => ['case-study',  'Studija slučaja',  'Case study'],
        'vijesti'    => ['vijesti',     'Vijesti',          'News'],
    ];

    public function __construct(
        private EntityManagerInterface $em,
        #[Autowire('%app.media_storage_path%')] private string $mediaStoragePath,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('old-host', null, InputOption::VALUE_REQUIRED, 'Old DB host', '127.0.0.1')
            ->addOption('old-port', null, InputOption::VALUE_REQUIRED, 'Old DB port', '3306')
            ->addOption('old-db',   null, InputOption::VALUE_REQUIRED, 'Old DB name', 'go2digital')
            ->addOption('old-user', null, InputOption::VALUE_REQUIRED, 'Old DB user', 'root')
            ->addOption('old-pass', null, InputOption::VALUE_REQUIRED, 'Old DB password', '')
            ->addOption('old-uploads', null, InputOption::VALUE_REQUIRED, 'Old uploads directory',
                '/Users/nikolagrdanjski/Code/www/go2Digital/go2digital/public/uploads')
            ->addOption('author', null, InputOption::VALUE_REQUIRED, 'Default author for imported posts', 'Go2Digital')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'Skip the confirmation prompt');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $uploadsDir = rtrim((string) $input->getOption('old-uploads'), '/');
        $author     = (string) $input->getOption('author');

        if (!is_dir($uploadsDir)) {
            $io->error("Old uploads directory not found: $uploadsDir");
            return Command::FAILURE;
        }

        $io->title('Import old blog → new API');
        $io->writeln("  Old DB:       {$input->getOption('old-host')}:{$input->getOption('old-port')}/{$input->getOption('old-db')} (user: {$input->getOption('old-user')})");
        $io->writeln("  Old uploads:  $uploadsDir");
        $io->writeln("  New storage:  {$this->mediaStoragePath}/originals");
        $io->writeln("  Author:       $author");
        $io->newLine();

        if (!$input->getOption('force')) {
            $helper = $this->getHelper('question');
            $q = new ConfirmationQuestion(
                '<fg=yellow>This will DELETE all existing blog posts, categories, translations, and blog-collection media from the new DB.</> Continue? [y/N] ',
                false
            );
            if (!$helper->ask($input, $output, $q)) {
                $io->warning('Aborted.');
                return Command::SUCCESS;
            }
        }

        // 1. Open old DB connection via DBAL (parallel to the EM's default connection).
        // DBAL 4 lazy-connects on first query — we run a tiny probe so failures surface here.
        try {
            $oldConn = DriverManager::getConnection([
                'driver'   => 'pdo_mysql',
                'host'     => $input->getOption('old-host'),
                'port'     => (int) $input->getOption('old-port'),
                'dbname'   => $input->getOption('old-db'),
                'user'     => $input->getOption('old-user'),
                'password' => $input->getOption('old-pass'),
                'charset'  => 'utf8mb4',
            ]);
            $oldConn->executeQuery('SELECT 1');
        } catch (\Throwable $e) {
            $io->error('Failed to connect to old DB: '.$e->getMessage());
            return Command::FAILURE;
        }
        $io->success('Connected to old DB.');

        // 2. Wipe existing blog data + blog-collection media files
        $wiped = $this->wipeExistingBlog($io, $fs);
        $io->writeln(sprintf(
            '  Wiped: %d posts, %d categories, %d media files',
            $wiped['posts'], $wiped['categories'], $wiped['mediaFiles']
        ));
        $this->em->flush();
        $this->em->clear();

        // 3. Create categories from old tags (deduped via CATEGORY_MAP)
        $categoryEntities = $this->createCategories($io);
        $this->em->flush();
        $io->success(sprintf('Created %d categories.', count($categoryEntities)));

        // 4. Load old posts + each post's first tag
        $rows = $this->fetchOldPosts($oldConn);
        $io->writeln(sprintf('Found %d old posts.', count($rows)));

        // 5. Import each post: copy image → create Media → create BlogPost + HR translation
        $imported = 0;
        $skipped = 0;
        $rankIndex = 0; // 0 = newest, preserves old created_at DESC ordering as sortOrder ASC
        $io->progressStart(count($rows));

        foreach ($rows as $row) {
            try {
                $this->importPost($row, $uploadsDir, $categoryEntities, $author, $rankIndex);
                $imported++;
                $rankIndex++;
            } catch (\Throwable $e) {
                $io->newLine();
                $io->warning(sprintf('Skipped "%s": %s', $row['slug'] ?? '?', $e->getMessage()));
                $skipped++;
            }
            $io->progressAdvance();

            // Flush every 25 to keep memory under control
            if ($imported > 0 && $imported % 25 === 0) {
                $this->em->flush();
                $this->em->clear();
                // Re-resolve managed category entities after clear()
                $categoryEntities = $this->reloadCategories();
            }
        }

        $io->progressFinish();
        $this->em->flush();

        $io->success(sprintf('Imported %d posts. Skipped %d.', $imported, $skipped));
        return Command::SUCCESS;
    }

    /** @return array{posts:int,categories:int,mediaFiles:int} */
    private function wipeExistingBlog(SymfonyStyle $io, Filesystem $fs): array
    {
        $conn = $this->em->getConnection();

        // Delete blog-collection media files first (while rows still exist)
        $mediaRows = $conn->fetchAllAssociative(
            "SELECT HEX(id) AS id, path FROM media WHERE collection = 'blog'"
        );
        $fileCount = 0;
        foreach ($mediaRows as $m) {
            $fullPath = $this->mediaStoragePath.'/'.$m['path'];
            if (is_file($fullPath)) {
                $fs->remove($fullPath);
                $fileCount++;
            }
        }

        // FKs: blog_posts.image → media (SET NULL onDelete), so order is safe.
        // But to be clean, null out image_id first, then delete translations, posts, media, categories.
        $conn->executeStatement("UPDATE blog_posts SET image_id = NULL");
        $postCount = (int) $conn->fetchOne("SELECT COUNT(*) FROM blog_posts");
        $catCount  = (int) $conn->fetchOne("SELECT COUNT(*) FROM blog_categories");

        $conn->executeStatement("DELETE FROM blog_post_translations");
        $conn->executeStatement("DELETE FROM blog_posts");
        $conn->executeStatement("DELETE FROM media WHERE collection = 'blog'");
        $conn->executeStatement("DELETE FROM blog_category_translations");
        $conn->executeStatement("DELETE FROM blog_categories");

        return ['posts' => $postCount, 'categories' => $catCount, 'mediaFiles' => $fileCount];
    }

    /** @return array<string, BlogCategory> keyed by new-category slug */
    private function createCategories(SymfonyStyle $io): array
    {
        // Dedupe the map to unique target categories
        $uniqueTargets = [];
        foreach (self::CATEGORY_MAP as $oldSlug => [$newSlug, $hrName, $enName]) {
            $uniqueTargets[$newSlug] ??= [$hrName, $enName];
        }

        $categories = [];
        $order = 0;
        foreach ($uniqueTargets as $slug => [$hrName, $enName]) {
            $cat = new BlogCategory();
            $cat->setSlug($slug);
            $cat->setSortOrder($order++);
            $cat->setIsActive(true);

            $trHr = new BlogCategoryTranslation();
            $trHr->setLocale('hr');
            $trHr->setName($hrName);
            $cat->addTranslation($trHr);

            $trEn = new BlogCategoryTranslation();
            $trEn->setLocale('en');
            $trEn->setName($enName);
            $cat->addTranslation($trEn);

            $this->em->persist($cat);
            $categories[$slug] = $cat;
        }
        return $categories;
    }

    /** @return array<string, BlogCategory> */
    private function reloadCategories(): array
    {
        $out = [];
        foreach ($this->em->getRepository(BlogCategory::class)->findAll() as $c) {
            $out[$c->getSlug()] = $c;
        }
        return $out;
    }

    /** @return list<array<string,mixed>> */
    private function fetchOldPosts(Connection $oldConn): array
    {
        // Pick the first tag per post (by tag.created_at, then slug) — one-to-one mapping.
        $sql = <<<SQL
            SELECT
                HEX(p.id) AS id,
                p.title,
                p.slug,
                p.subtitle,
                p.content,
                p.feature_image,
                p.seo_title,
                p.seo_meta_description,
                p.is_publish,
                p.is_featured,
                p.created_at,
                (
                    SELECT t.slug
                    FROM post_tag pt
                    INNER JOIN tag t ON t.id = pt.tag_id
                    WHERE pt.post_id = p.id
                    ORDER BY t.created_at ASC, t.slug ASC
                    LIMIT 1
                ) AS tag_slug
            FROM post p
            ORDER BY p.created_at DESC
            SQL;

        return $oldConn->fetchAllAssociative($sql);
    }

    /**
     * @param array<string,mixed>        $row
     * @param array<string, BlogCategory> $categoryEntities
     */
    private function importPost(array $row, string $uploadsDir, array $categoryEntities, string $author, int $sortOrder): void
    {
        $slug = (string) $row['slug'];
        $title = (string) $row['title'];
        $featureImage = (string) ($row['feature_image'] ?? '');

        if ($featureImage === '') {
            throw new \RuntimeException('Missing feature_image');
        }

        $sourcePath = $uploadsDir.'/'.$featureImage;
        if (!is_file($sourcePath)) {
            throw new \RuntimeException("Image file missing on disk: $sourcePath");
        }

        // Copy the image into the media storage under a fresh UUID
        $ext = strtolower(pathinfo($featureImage, PATHINFO_EXTENSION)) ?: 'bin';
        $newUuid = Uuid::v7()->toRfc4122();
        $newFilename = "$newUuid.$ext";
        $relPath = "originals/$newFilename";
        $destPath = $this->mediaStoragePath.'/'.$relPath;

        if (!is_dir(dirname($destPath))) {
            mkdir(dirname($destPath), 0775, true);
        }
        if (!copy($sourcePath, $destPath)) {
            throw new \RuntimeException("Failed to copy image to $destPath");
        }

        $mimeType = mime_content_type($destPath) ?: 'application/octet-stream';
        $size = (string) filesize($destPath);
        [$width, $height] = $this->readImageSize($destPath);

        $media = new Media();
        $media->setFilename($newFilename);
        $media->setOriginalFilename($featureImage);
        $media->setMimeType($mimeType);
        $media->setSize($size);
        if ($width !== null) { $media->setWidth($width); }
        if ($height !== null) { $media->setHeight($height); }
        $media->setCollection('blog');
        $media->setDisk('local');
        $media->setPath($relPath);
        $this->em->persist($media);

        // Resolve category via dedupe map
        $category = null;
        $oldTag = $row['tag_slug'] ?? null;
        if (is_string($oldTag) && isset(self::CATEGORY_MAP[$oldTag])) {
            $targetSlug = self::CATEGORY_MAP[$oldTag][0];
            $category = $categoryEntities[$targetSlug] ?? null;
        }

        // Parse date from old created_at
        $date = new \DateTime((string) ($row['created_at'] ?? 'now'));

        $post = new BlogPost();
        $post->setSlug($slug);
        $post->setImage($media);
        $post->setDate($date);
        $post->setAuthor($author);
        if ($category !== null) {
            $post->setCategory($category);
        }
        $post->setFeatured((bool) ($row['is_featured'] ?? false));
        $post->setSortOrder($sortOrder);
        $post->setStatus(((int) $row['is_publish'] === 1) ? ContentStatus::Published : ContentStatus::Draft);

        $tr = new BlogPostTranslation();
        $tr->setLocale('hr');
        $tr->setTitle($title);
        $tr->setExcerpt($row['subtitle'] !== null ? (string) $row['subtitle'] : null);
        $tr->setBody($row['content'] !== null ? (string) $row['content'] : null);
        $post->addTranslation($tr);

        $this->em->persist($post);
    }

    /** @return array{0:?int,1:?int} */
    private function readImageSize(string $path): array
    {
        $info = @getimagesize($path);
        if ($info === false) { return [null, null]; }
        return [$info[0] ?? null, $info[1] ?? null];
    }
}
