<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Setting;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-translations',
    description: 'Seed translation settings into the database (skip existing keys)',
)]
class SeedTranslationsCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $translations = [
            // Location
            ['key' => 'location.title', 'group' => 'location', 'value' => ['hr' => 'Lokacije', 'en' => 'Locations']],
            ['key' => 'location.collectionView', 'group' => 'location', 'value' => ['hr' => 'Prikaz kolekcije', 'en' => 'Collection view']],
            ['key' => 'location.search.placeholder', 'group' => 'location', 'value' => ['hr' => 'Pretraži lokacije...', 'en' => 'Search locations...']],
            ['key' => 'location.search.button', 'group' => 'location', 'value' => ['hr' => 'Traži', 'en' => 'Search']],
            ['key' => 'location.search.filterPlaceholder', 'group' => 'location', 'value' => ['hr' => 'Pretraži...', 'en' => 'Search...']],
            ['key' => 'location.search.label', 'group' => 'location', 'value' => ['hr' => 'Pretraga', 'en' => 'Search']],
            ['key' => 'location.filters.title', 'group' => 'location', 'value' => ['hr' => 'Filteri', 'en' => 'Filters']],
            ['key' => 'location.filters.cities', 'group' => 'location', 'value' => ['hr' => 'Gradovi', 'en' => 'Cities']],
            ['key' => 'location.filters.environments', 'group' => 'location', 'value' => ['hr' => 'Okruženja', 'en' => 'Environments']],
            ['key' => 'location.filters.clearAll', 'group' => 'location', 'value' => ['hr' => 'Očisti sve', 'en' => 'Clear all']],
            ['key' => 'location.filters.apply', 'group' => 'location', 'value' => ['hr' => 'Primijeni', 'en' => 'Apply']],
            ['key' => 'location.clearAll', 'group' => 'location', 'value' => ['hr' => 'Očisti sve', 'en' => 'Clear all']],
            ['key' => 'location.noResults', 'group' => 'location', 'value' => ['hr' => 'Nema rezultata za odabrane filtere.', 'en' => 'No results for selected filters.']],
            ['key' => 'location.collection', 'group' => 'location', 'value' => ['hr' => 'Kolekcija', 'en' => 'Collection']],
            ['key' => 'location.shareUrl', 'group' => 'location', 'value' => ['hr' => 'Podijeli URL', 'en' => 'Share URL']],
            ['key' => 'location.downloadPdf', 'group' => 'location', 'value' => ['hr' => 'Preuzmi PDF', 'en' => 'Download PDF']],
            ['key' => 'location.emptyCollection', 'group' => 'location', 'value' => ['hr' => 'Vaša kolekcija je prazna', 'en' => 'Your collection is empty']],
            ['key' => 'location.emptyCollectionHint', 'group' => 'location', 'value' => ['hr' => 'Dodajte lokacije klikom na kvačicu', 'en' => 'Add locations by clicking the checkbox']],
            ['key' => 'location.viewSwitcher.grid', 'group' => 'location', 'value' => ['hr' => 'Lista', 'en' => 'Grid']],
            ['key' => 'location.viewSwitcher.map', 'group' => 'location', 'value' => ['hr' => 'Mapa', 'en' => 'Map']],
            ['key' => 'location.mapStyle.light', 'group' => 'location', 'value' => ['hr' => 'Svijetla', 'en' => 'Light']],
            ['key' => 'location.mapStyle.dark', 'group' => 'location', 'value' => ['hr' => 'Tamna', 'en' => 'Dark']],

            // Lab
            ['key' => 'lab.aboutProject', 'group' => 'lab', 'value' => ['hr' => 'O projektu', 'en' => 'About The Project']],
            ['key' => 'lab.viewAll', 'group' => 'lab', 'value' => ['hr' => 'Prikaži sve', 'en' => 'View all']],
            ['key' => 'lab.viewProject', 'group' => 'lab', 'value' => ['hr' => 'Pogledaj projekt', 'en' => 'View project']],
            ['key' => 'lab.pageTitle', 'group' => 'lab', 'value' => ['hr' => 'Go2Labs - Go2Digital', 'en' => 'Go2Labs - Go2Digital']],
            ['key' => 'lab.title', 'group' => 'lab', 'value' => ['hr' => 'Labs', 'en' => 'Labs']],
            ['key' => 'lab.filterAll', 'group' => 'lab', 'value' => ['hr' => 'Sve', 'en' => 'All']],
            ['key' => 'lab.noResults', 'group' => 'lab', 'value' => ['hr' => 'Nema rezultata za odabranu kategoriju.', 'en' => 'No results for selected category.']],

            // Blog
            ['key' => 'blog.viewAll', 'group' => 'blog', 'value' => ['hr' => 'Prikaži sve članke', 'en' => 'View all articles']],
            ['key' => 'blog.pageTitle', 'group' => 'blog', 'value' => ['hr' => 'Blog - Go2Digital', 'en' => 'Blog - Go2Digital']],
            ['key' => 'blog.filterAll', 'group' => 'blog', 'value' => ['hr' => 'Sve', 'en' => 'All']],
            ['key' => 'blog.noResults', 'group' => 'blog', 'value' => ['hr' => 'Nema rezultata.', 'en' => 'No results.']],

            // Navigation
            ['key' => 'nav.home', 'group' => 'nav', 'value' => ['hr' => 'Početna', 'en' => 'Home']],
            ['key' => 'nav.lab', 'group' => 'nav', 'value' => ['hr' => 'Lab', 'en' => 'Lab']],
            ['key' => 'nav.blog', 'group' => 'nav', 'value' => ['hr' => 'Blog', 'en' => 'Blog']],
            ['key' => 'nav.about', 'group' => 'nav', 'value' => ['hr' => 'O nama', 'en' => 'About']],
            ['key' => 'nav.contact', 'group' => 'nav', 'value' => ['hr' => 'Kontakt', 'en' => 'Contact']],

            // Location toasts & confirm
            ['key' => 'location.toast.added', 'group' => 'location', 'value' => ['hr' => 'Dodano "{name}" u kolekciju', 'en' => 'Added "{name}" to collection']],
            ['key' => 'location.toast.removed', 'group' => 'location', 'value' => ['hr' => 'Uklonjeno "{name}" iz kolekcije', 'en' => 'Removed "{name}" from collection']],
            ['key' => 'location.confirmClear', 'group' => 'location', 'value' => ['hr' => 'Jeste li sigurni da želite očistiti sve odabrane lokacije?', 'en' => 'Are you sure you want to clear all selected locations?']],
        ];

        $repo = $this->em->getRepository(Setting::class);
        $created = 0;
        $skipped = 0;

        foreach ($translations as $t) {
            $existing = $repo->findOneBy(['key' => $t['key']]);
            if ($existing) {
                $io->text("  skip  {$t['key']} (already exists)");
                $skipped++;
                continue;
            }

            $setting = new Setting();
            $setting->setKey($t['key']);
            $setting->setGroup($t['group']);
            $setting->setValue($t['value']);
            $this->em->persist($setting);
            $io->text("  ✓ {$t['key']}");
            $created++;
        }

        $this->em->flush();
        $io->success("Done: {$created} created, {$skipped} skipped (already existed)");

        return Command::SUCCESS;
    }
}
