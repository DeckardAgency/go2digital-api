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
    name: 'app:seed-typography',
    description: 'Seed typography fonts and presets into settings (skip existing keys)',
)]
class SeedTypographyCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $fonts = [
            [
                'slug' => 'pp-neue-montreal',
                'name' => 'PP Neue Montreal',
                'stack' => "'PP Neue Montreal', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif",
                'weights' => [
                    ['weight' => 400, 'src' => '/fonts/PPNeueMontreal-Regular.woff2', 'format' => 'woff2'],
                ],
            ],
        ];

        $presets = [
            [
                // old .home-hero__title (pages/_homepage.page.scss:115):
                //   3.125rem / 400 / line-height 1 / letter-spacing -0.0625rem
                //   laptop: $font-size-3xl (2rem); mobile: $font-size-2xl (1.5rem)
                // NOTE: old site's fluid root font (clamp 1rem-1.5rem) makes this
                //   scale up on wide viewports (e.g. 58px@1920, 75px@2900).
                'slug' => 'hero-title', 'label' => 'Hero — Main Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.0625rem',
                'sizes' => ['mobile' => '1.5rem', 'tablet' => '2rem', 'desktop' => '3.125rem'],
            ],
            [
                // old .home-hero__kicker (pages/_homepage.page.scss:169):
                //   $font-size-sm (0.875rem) / 400 / text-transform: capitalize
                'slug' => 'hero-kicker', 'label' => 'Hero — Kicker / Badge', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.875rem', 'tablet' => '0.875rem', 'desktop' => '0.875rem'],
            ],
            [
                'slug' => 'hero-heading', 'label' => 'Hero — Secondary Heading', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1rem', 'tablet' => '1rem', 'desktop' => '1rem'],
            ],
            [
                // old .home-hero__text (pages/_homepage.page.scss:202):
                //   $font-size-base (1rem) / 400 / $line-height-normal (1.3) / opacity 0.4
                'slug' => 'hero-description', 'label' => 'Hero — Description', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1rem', 'tablet' => '1rem', 'desktop' => '1rem'],
            ],
            [
                'slug' => 'hero-scroll', 'label' => 'Hero — Scroll Label', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.75rem', 'tablet' => '0.75rem', 'desktop' => '0.75rem'],
            ],
            [
                // old .esg-page__vision-title: 3.125rem/400/1/-0.02em desktop,
                //   2rem tablet, 1.5rem mobile.
                'slug' => 'section-title', 'label' => 'Section — Title (default)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '1.5rem', 'tablet' => '2rem', 'desktop' => '3.125rem'],
            ],
            [
                // old site: p/body base weight 400, line-height-relaxed (1.625)
                'slug' => 'body-lg', 'label' => 'Body — Large', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.6, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1rem', 'tablet' => '1rem', 'desktop' => '1.125rem'],
            ],
            [
                // old site: UI body text uses $line-height-normal = 1.3 across pages.
                // Readable prose uses body-lg (1.6) instead.
                'slug' => 'body', 'label' => 'Body — Default', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1rem', 'tablet' => '1rem', 'desktop' => '1rem'],
            ],
            [
                'slug' => 'body-sm', 'label' => 'Body — Small', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.875rem', 'tablet' => '0.875rem', 'desktop' => '0.875rem'],
            ],
            [
                'slug' => 'eyebrow', 'label' => 'Eyebrow / Small Label', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.75rem', 'tablet' => '0.75rem', 'desktop' => '0.75rem'],
            ],
            [
                'slug' => 'billboard-title', 'label' => 'Billboard — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.1, 'letterSpacing' => '-0.03rem',
                'sizes' => [
                    'mobile' => 'clamp(1.5rem, 2.5vw, 2.5rem)',
                    'tablet' => 'clamp(1.5rem, 2.5vw, 2.5rem)',
                    'desktop' => 'clamp(1.5rem, 2.5vw, 2.5rem)',
                ],
            ],
            [
                'slug' => 'billboard-subtitle', 'label' => 'Billboard — Subtitle', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.15, 'letterSpacing' => '-0.02em',
                'sizes' => [
                    'mobile' => 'clamp(1.25rem, 2.5vw, 2rem)',
                    'tablet' => 'clamp(1.25rem, 2.5vw, 2rem)',
                    'desktop' => 'clamp(1.25rem, 2.5vw, 2rem)',
                ],
            ],
            [
                'slug' => 'block-heading', 'label' => 'Block — Heading', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.2, 'letterSpacing' => '-0.04rem',
                'sizes' => [
                    'mobile' => 'clamp(1.5rem, 3vw, 2.125rem)',
                    'tablet' => 'clamp(1.5rem, 3vw, 2.125rem)',
                    'desktop' => 'clamp(1.5rem, 3vw, 2.125rem)',
                ],
            ],
            [
                // old site: fluid section titles use weight 400 ($font-weight-regular), not 300
                'slug' => 'section-title-fluid', 'label' => 'Section — Title (Fluid)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.05, 'letterSpacing' => '-0.02em',
                'sizes' => [
                    'mobile' => 'clamp(2.5rem, 5vw, 4rem)',
                    'tablet' => 'clamp(2.5rem, 5vw, 4rem)',
                    'desktop' => 'clamp(2.5rem, 5vw, 4rem)',
                ],
            ],
            [
                // old site .single-lab__intro-text: 2.125rem/400/110%/-0.34px (tablet 1.75rem)
                'slug' => 'body-lg-static', 'label' => 'Body — Large (Static)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.1, 'letterSpacing' => '-0.02125rem',
                'sizes' => ['mobile' => '2.125rem', 'tablet' => '1.75rem', 'desktop' => '2.125rem'],
            ],
            [
                'slug' => 'label-micro', 'label' => 'Label — Micro (axes)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.5625rem', 'tablet' => '0.5625rem', 'desktop' => '0.5625rem'],
            ],
            [
                'slug' => 'display-outline', 'label' => 'Display — Outline / Overlay', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.2, 'letterSpacing' => '-0.04em',
                'sizes' => [
                    'mobile' => 'clamp(4rem, 15vw, 15rem)',
                    'tablet' => 'clamp(4rem, 15vw, 15rem)',
                    'desktop' => 'clamp(4rem, 15vw, 15rem)',
                ],
            ],
            [
                'slug' => 'display-xl', 'label' => 'Display — XL Headline', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.1, 'letterSpacing' => '-0.03em',
                'sizes' => ['mobile' => '2.625rem', 'tablet' => '3.5rem', 'desktop' => '5.375rem'],
            ],
            [
                'slug' => 'eyebrow-tight', 'label' => 'Eyebrow — Tight Line', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 0.9, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.6875rem', 'tablet' => '0.6875rem', 'desktop' => '0.75rem'],
            ],
            [
                // old .header-info__time/__date: 5rem/300/1/-0.1875rem
                //   desktop 3.75rem; tablet 3rem; mobile 3.125rem (-0.0625rem).
                // Used exclusively in 'contact' blockMap.
                'slug' => 'display-stat', 'label' => 'Display — Big Stat / Number', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 300, 'lineHeight' => 1, 'letterSpacing' => '-0.1875rem',
                'sizes' => ['mobile' => '3.125rem', 'tablet' => '3rem', 'desktop' => '5rem'],
            ],
            [
                // Used by Why Section slide numbers (homepage). Letter-spacing in em
                //   so it scales with size at smaller breakpoints.
                'slug' => 'display-stat-xl', 'label' => 'Display — Big Stat / Number XL', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 0.8, 'letterSpacing' => '-0.06em',
                'sizes' => ['mobile' => '6rem', 'tablet' => '10rem', 'desktop' => '17.5rem'],
            ],
            [
                'slug' => 'display-md', 'label' => 'Display — Medium (Slide Title)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '2rem', 'tablet' => '2rem', 'desktop' => '3.125rem'],
            ],
            [
                // old .lab-page__title / .esg-page__display-title: lh 0.8 desktop, 0.85 mobile.
                'slug' => 'display-huge', 'label' => 'Display — Huge Animated Word', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 0.85, 'letterSpacing' => '-0.06em',
                'sizes' => [
                    'mobile' => 'clamp(3rem, 10vw, 7rem)',
                    'tablet' => 'clamp(4rem, 14vw, 12rem)',
                    'desktop' => 'clamp(4.5rem, 17.5vw, 17.5rem)',
                ],
            ],
            [
                'slug' => 'panel-title', 'label' => 'Panel — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.1, 'letterSpacing' => '-0.01em',
                'sizes' => ['mobile' => '1.5rem', 'tablet' => '1.75rem', 'desktop' => '2.125rem'],
            ],
            [
                'slug' => 'panel-stat', 'label' => 'Panel — Big Stat Value', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.06em',
                'sizes' => ['mobile' => '7.5rem', 'tablet' => '11.25rem', 'desktop' => '17.5rem'],
            ],
            [
                'slug' => 'featured-labs-title', 'label' => 'Featured Labs — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.03em',
                'sizes' => ['mobile' => '2.5rem', 'tablet' => '4rem', 'desktop' => '5.375rem'],
            ],
            [
                // old .lab-item__title, .article-card-v2__title, .cube-section__specs-title:
                //   2.125rem / 400 / lh 1.2 / ls -0.02em (mobile 1.5rem).
                'slug' => 'card-title', 'label' => 'Card — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.2, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '1.5rem', 'tablet' => '2.125rem', 'desktop' => '2.125rem'],
            ],
            [
                // old .lab-item__category: desktop 1rem / mobile 0.875rem / 400 / lh 1.3.
                'slug' => 'card-tag', 'label' => 'Card — Tag', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.875rem', 'tablet' => '1rem', 'desktop' => '1rem'],
            ],
            [
                'slug' => 'tracking-title', 'label' => 'Tracking — Section Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.0625rem',
                'sizes' => [
                    'mobile' => 'clamp(1.5rem, 3vw, 3.125rem)',
                    'tablet' => 'clamp(1.5rem, 3vw, 3.125rem)',
                    'desktop' => 'clamp(1.5rem, 3vw, 3.125rem)',
                ],
            ],
            [
                'slug' => 'feature-title', 'label' => 'Feature — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1.25rem', 'tablet' => '1.25rem', 'desktop' => '1.5rem'],
            ],
            [
                'slug' => 'number-lg', 'label' => 'Number — Large (static)', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => null,
                'sizes' => ['mobile' => '1.25rem', 'tablet' => '1.25rem', 'desktop' => '1.25rem'],
            ],
            [
                'slug' => 'number-responsive', 'label' => 'Number — Responsive', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.875rem', 'tablet' => '0.875rem', 'desktop' => '1.25rem'],
            ],
            [
                'slug' => 'possibilities-item', 'label' => 'Possibilities — List Item', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.35, 'letterSpacing' => null,
                'sizes' => [
                    'mobile' => 'clamp(1.125rem, 4vw, 1.5rem)',
                    'tablet' => 'clamp(1.125rem, 4vw, 1.5rem)',
                    'desktop' => 'clamp(1.5rem, 2.8vw, 2.25rem)',
                ],
            ],
            [
                // old $font-size-xs (0.75rem) across meta/date fields uses lh 1.3.
                'slug' => 'body-xs', 'label' => 'Body — Extra Small', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.3, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.75rem', 'tablet' => '0.75rem', 'desktop' => '0.75rem'],
            ],
            [
                'slug' => 'cube-title', 'label' => 'Cube / Display — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.16rem',
                'sizes' => ['mobile' => '3.125rem', 'tablet' => '4rem', 'desktop' => '5.375rem'],
            ],
            [
                'slug' => 'cube-number', 'label' => 'Cube / Display — Big Number', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => null,
                'sizes' => ['mobile' => '5.375rem', 'tablet' => '8rem', 'desktop' => '12.0625rem'],
            ],
            [
                // old .esg-page__intro-large: 1.375rem mobile / 1.75rem base / 3.125rem desktop
                //   / 400 / lh 1 / ls -0.02em.
                'slug' => 'intro-large', 'label' => 'Intro — Large', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '1.375rem', 'tablet' => '1.75rem', 'desktop' => '3.125rem'],
            ],
            [
                // old .esg-page__pillar-number: 1.125rem mobile / 1.5rem base / 2rem desktop
                //   / 400 / lh 1.2 / ls -0.02em.
                'slug' => 'pillar-number', 'label' => 'Pillar — Number', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.2, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '1.125rem', 'tablet' => '1.5rem', 'desktop' => '2rem'],
            ],
            [
                // old .esg-page__pillar-title: 1.25rem mobile / 1.5rem base / 2rem desktop
                //   / 400 / lh 1.2 / ls -0.02em.
                'slug' => 'pillar-title', 'label' => 'Pillar — Title', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.2, 'letterSpacing' => '-0.02em',
                'sizes' => ['mobile' => '1.25rem', 'tablet' => '1.5rem', 'desktop' => '2rem'],
            ],
            [
                // old .weather__temperature: 2rem mobile / 2.5rem tablet / 3.5rem desktop
                //   / 300 / lh 1 / ls -0.0625rem.
                'slug' => 'weather-temp', 'label' => 'Weather — Temperature', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 300, 'lineHeight' => 1, 'letterSpacing' => '-0.0625rem',
                'sizes' => ['mobile' => '2rem', 'tablet' => '2.5rem', 'desktop' => '3.5rem'],
            ],
            [
                // old .lab-page__intro (_lab.page.scss:82): desktop 3.125rem/400/lh 1/ls -0.0625rem;
                //   tablet 2rem; mobile 1.125rem / lh 1.2 / ls -0.0425rem.
                // NOTE: single lineHeight/letterSpacing limitation — we use desktop values
                //   (lh 1, ls -0.0625rem); mobile's tighter lh 1.2 / -0.0425rem is minor drift.
                'slug' => 'lab-intro', 'label' => 'Lab — Intro Text', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1, 'letterSpacing' => '-0.0625rem',
                'sizes' => ['mobile' => '1.125rem', 'tablet' => '2rem', 'desktop' => '3.125rem'],
            ],
            [
                // old .battery-status__text + .weather__description (_contact.page.scss:159,180):
                //   desktop 0.9375rem / 400 / lh 1.5; mobile 0.8125rem.
                'slug' => 'contact-text', 'label' => 'Contact — Meta Text', 'fontSlug' => 'pp-neue-montreal',
                'weight' => 400, 'lineHeight' => 1.5, 'letterSpacing' => null,
                'sizes' => ['mobile' => '0.8125rem', 'tablet' => '0.8125rem', 'desktop' => '0.9375rem'],
            ],
        ];

        $blockMaps = [
            'panels' => [
                'title' => 'panel-title',
                'tag' => 'eyebrow',
                'description' => 'body',
                'statValue' => 'panel-stat',
                'scrollLabel' => 'eyebrow',
            ],
            'tracking' => [
                'title' => 'tracking-title',
                'number' => 'number-lg',
                'feature' => 'feature-title',
                'description' => 'body',
            ],
            'featured-labs' => [
                'label' => 'eyebrow-tight',
                'title' => 'featured-labs-title',
                'count' => 'number-responsive',
                'text' => 'body',                      // old .featured-labs__text: 1rem
                'cardTitle' => 'card-title',
                'cardDescription' => 'body',           // old .lab-item__description: 1rem
                'cardTag' => 'card-tag',
            ],
            'possibilities' => [
                'label' => 'eyebrow',
                'subtitle' => 'eyebrow',
                'counter' => 'display-md',
                'item' => 'possibilities-item',
                'description' => 'body-sm',
            ],
            'products' => [
                'title' => 'cube-title',
                'number' => 'cube-number',
                'badge' => 'eyebrow-tight',
                'specsTitle' => 'card-title',
                'specLabel' => 'body',                 // old .cube-section__spec-label: 1rem
                'specValue' => 'body',                 // old .cube-section__spec-value: 1rem
                'descIndicator' => 'eyebrow-tight',
                'descTitle' => 'display-md',
            ],
            'interactive-display' => [
                'title' => 'cube-title',
                'number' => 'cube-number',
                'badge' => 'eyebrow-tight',
                'specsTitle' => 'card-title',
                'specLabel' => 'body',                 // old .interactive-display__spec-label: 1rem
                'specValue' => 'body',                 // old .interactive-display__spec-value: 1rem
            ],
            'interactive-description' => [
                'indicator' => 'eyebrow-tight',
                'title' => 'display-md',
            ],
            'blog-list' => [
                'pageTitle' => 'display-xl',          // old .blog-v2__title: 5.375rem
                'count' => 'body',                     // old .blog-v2__count: 1rem
                'filter' => 'eyebrow',                 // old .blog-v2__filter-text: 0.75rem
                'cardTitle' => 'card-title',           // old .article-card-v2__title
                'cardMeta' => 'body-xs',               // old .article-card-v2__date: 0.75rem
                'cardCategory' => 'eyebrow',           // old .article-card-v2__category-text: 0.75rem
                'emptyTitle' => 'block-heading',       // old no-posts h2: 1.5rem
                'emptyText' => 'body',
            ],
            'blog-detail' => [
                'heroTitle' => 'body-lg-static',       // old .single-blog__title: 2.125rem/1.1/-0.02125rem
                'specLabel' => 'eyebrow-tight',        // old .single-blog__section-label: 0.75rem/0.9
                'specValue' => 'eyebrow',              // old .single-blog__info: 0.75rem
                'actionButton' => 'body',              // old .single-blog__back: 1rem
                'contentHeading' => 'body',            // old blog has no distinct content heading
                'contentBody' => 'body',
            ],
            'lab-list' => [
                'label' => 'eyebrow',
                'intro' => 'lab-intro',                // old .lab-page__intro
                'filter' => 'eyebrow',                 // old .lab-page__filter-label: 0.75rem
                'pageTitle' => 'display-huge',
                'count' => 'body',                     // old .lab-page__count: 1rem
                'cardTitle' => 'card-title',
                'cardDescription' => 'body',
                'cardTag' => 'card-tag',               // old .lab-item__category: 1rem
                'emptyText' => 'body',
            ],
            'lab-detail' => [
                'heroTitle' => 'hero-title',           // old .single-lab__title: 3.125rem/1/-0.0625rem
                'specLabel' => 'eyebrow-tight',        // old .single-lab__section-label: 0.875rem/0.9
                'specValue' => 'body',
                'actionButton' => 'body',              // old .single-lab__back: 1rem
                'introLabel' => 'eyebrow-tight',
                'introBody' => 'body-lg-static',
                'sectionLabel' => 'eyebrow-tight',
                'sectionTitle' => 'body',              // old .single-lab__section-title: 1rem
                'sectionContent' => 'body',
            ],
            'esg' => [
                'displayTitle' => 'display-huge',
                'label' => 'eyebrow-tight',            // old .esg-page__label-text: 0.75rem/0.9
                'introSmall' => 'body',
                'introLarge' => 'intro-large',         // new preset
                'actionLink' => 'body',
                'pillarNumber' => 'pillar-number',     // new preset
                'pillarTitle' => 'pillar-title',       // new preset
                'pillarDesc' => 'body',
                'cardText' => 'body',
                'visionTitle' => 'section-title',
                'diagramBadge' => 'eyebrow',           // old .esg-page__diagram-badge: 0.75rem
                'diagramTitle' => 'body',
                'diagramDesc' => 'body',               // old .esg-page__diagram-desc: 1rem
                'scrollHint' => 'eyebrow',             // old scroll hint: 0.75rem/1.3 capitalize
            ],
            'team' => [
                'heroTitle' => 'display-xl',
            ],
            'contact' => [
                'headerTime' => 'display-stat',
                'headerDate' => 'display-stat',
                'temperature' => 'weather-temp',       // old .weather__temperature
                'weatherDesc' => 'contact-text',       // old .weather__description
                'batteryText' => 'contact-text',       // old .battery-status__text
                'batteryPercent' => 'body-sm',         // old .battery-status__percentage: 0.8125rem (≈0.875)
            ],
        ];

        $settings = [
            ['key' => 'typography.fonts', 'group' => 'typography', 'value' => $fonts],
        ];

        foreach ($presets as $preset) {
            $settings[] = [
                'key' => 'typography.presets.'.$preset['slug'],
                'group' => 'typography',
                'value' => $preset,
            ];
        }

        foreach ($blockMaps as $blockId => $map) {
            $settings[] = [
                'key' => 'typography.blockMaps.'.$blockId,
                'group' => 'typography',
                'value' => $map,
            ];
        }

        $repo = $this->em->getRepository(Setting::class);
        $created = 0;
        $skipped = 0;

        foreach ($settings as $s) {
            $existing = $repo->findOneBy(['key' => $s['key']]);
            if ($existing) {
                $io->text("  skip  {$s['key']} (already exists)");
                $skipped++;
                continue;
            }

            $setting = new Setting();
            $setting->setKey($s['key']);
            $setting->setGroup($s['group']);
            $setting->setValue($s['value']);
            $this->em->persist($setting);
            $io->text("  ✓ {$s['key']}");
            $created++;
        }

        $this->em->flush();
        $io->success("Done: {$created} created, {$skipped} skipped (already existed)");

        return Command::SUCCESS;
    }
}
