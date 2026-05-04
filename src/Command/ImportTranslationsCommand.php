<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Translation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Import translations from Nuxt-style nested locale JSON files into the Translation table.
 * Usage: app:translations:import <hr=path> <en=path>
 *        app:translations:import --dir=/path/to/locales (expects {locale}.json files)
 */
#[AsCommand(
    name: 'app:translations:import',
    description: 'Import translations from locale JSON files (one row per key + locale)',
)]
class ImportTranslationsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('files', InputArgument::IS_ARRAY, 'locale=path pairs, e.g. hr=/abs/hr.json en=/abs/en.json')
            ->addOption('dir', null, InputOption::VALUE_REQUIRED, 'Directory containing {locale}.json files')
            ->addOption('locales', null, InputOption::VALUE_REQUIRED, 'Comma-separated locales when using --dir', 'hr,en')
            ->addOption('overwrite', null, InputOption::VALUE_NONE, 'Update value when a (key, locale) row already exists');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $sources = [];
        $files = $input->getArgument('files');
        $dir = $input->getOption('dir');

        if ($dir) {
            $locales = array_filter(array_map('trim', explode(',', (string) $input->getOption('locales'))));
            foreach ($locales as $locale) {
                $sources[$locale] = rtrim($dir, '/').'/'.$locale.'.json';
            }
        }

        foreach ($files as $pair) {
            if (!str_contains($pair, '=')) {
                $io->error(sprintf('Argument "%s" must be in form locale=path', $pair));

                return Command::INVALID;
            }
            [$locale, $path] = explode('=', $pair, 2);
            $sources[$locale] = $path;
        }

        if (!$sources) {
            $io->error('No sources provided. Pass locale=path pairs or use --dir.');

            return Command::INVALID;
        }

        $overwrite = (bool) $input->getOption('overwrite');
        $repo = $this->em->getRepository(Translation::class);
        $totalCreated = 0;
        $totalUpdated = 0;
        $totalSkipped = 0;

        foreach ($sources as $locale => $path) {
            if (!is_file($path)) {
                $io->error(sprintf('File not found for locale "%s": %s', $locale, $path));

                return Command::FAILURE;
            }

            $raw = file_get_contents($path);
            $data = json_decode($raw, true);
            if (!is_array($data)) {
                $io->error(sprintf('Invalid JSON in %s', $path));

                return Command::FAILURE;
            }

            $flat = [];
            $this->flatten($data, '', $flat);
            $io->section(sprintf('Locale "%s" — %d keys', $locale, count($flat)));

            $created = 0;
            $updated = 0;
            $skipped = 0;
            foreach ($flat as $key => $value) {
                $existing = $repo->findOneBy(['key' => $key, 'locale' => $locale]);
                if ($existing) {
                    if ($overwrite && $existing->getValue() !== $value) {
                        $existing->setValue($value);
                        ++$updated;
                    } else {
                        ++$skipped;
                    }
                    continue;
                }

                $row = new Translation();
                $row->setKey($key);
                $row->setLocale($locale);
                $row->setValue($value);
                $this->em->persist($row);
                ++$created;
            }

            $this->em->flush();
            $io->writeln(sprintf('  created: %d  updated: %d  skipped: %d', $created, $updated, $skipped));
            $totalCreated += $created;
            $totalUpdated += $updated;
            $totalSkipped += $skipped;
        }

        $io->success(sprintf('Done. created=%d updated=%d skipped=%d', $totalCreated, $totalUpdated, $totalSkipped));

        return Command::SUCCESS;
    }

    /**
     * Flatten nested locale JSON into dotted keys. Arrays become numeric segments
     * (e.g. specs.0.label) so the messages endpoint can rebuild the original shape.
     */
    private function flatten(array $node, string $prefix, array &$out): void
    {
        foreach ($node as $key => $value) {
            $path = '' === $prefix ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                if ([] === $value) {
                    $out[$path] = '';
                    continue;
                }
                $this->flatten($value, $path, $out);
                continue;
            }

            if (is_scalar($value) || null === $value) {
                $out[$path] = (string) $value;
                continue;
            }
        }
    }
}
