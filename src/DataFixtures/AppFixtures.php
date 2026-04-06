<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\BlogCategory;
use App\Entity\BlogPageContent;
use App\Entity\BlogPost;
use App\Entity\ContactInfo;
use App\Entity\ContactPageContent;
use App\Entity\EsgCard;
use App\Entity\EsgPageContent;
use App\Entity\EsgPillar;
use App\Entity\EsgVisionBadge;
use App\Entity\HomepageAnalytics;
use App\Entity\HomepageBillboard;
use App\Entity\HomepageCustomImage;
use App\Entity\HomepageCustomSolution;
use App\Entity\HomepageFeaturedLabItem;
use App\Entity\HomepageHero;
use App\Entity\HomepageHumanFocused;
use App\Entity\HomepagePanel;
use App\Entity\HomepageProduct;
use App\Entity\HomepageProductFeature;
use App\Entity\HomepageRentalsImage;
use App\Entity\HomepageTextAnimation;
use App\Entity\HomepageTrackingFeature;
use App\Entity\HomepageWhyCard;
use App\Entity\HomepageWhySection;
use App\Entity\LabCategory;
use App\Entity\LabPageContent;
use App\Entity\LabProject;
use App\Entity\NavigationItem;
use App\Entity\Page;
use App\Entity\Setting;
use App\Entity\SocialLink;
use App\Entity\TeamPageContent;
use App\Entity\Translation\BlogCategoryTranslation;
use App\Entity\Translation\BlogPageContentTranslation;
use App\Entity\Translation\BlogPostTranslation;
use App\Entity\Translation\ContactPageContentTranslation;
use App\Entity\Translation\EsgCardTranslation;
use App\Entity\Translation\EsgPageContentTranslation;
use App\Entity\Translation\EsgPillarTranslation;
use App\Entity\Translation\EsgVisionBadgeTranslation;
use App\Entity\Translation\HomepageAnalyticsTranslation;
use App\Entity\Translation\HomepageBillboardTranslation;
use App\Entity\Translation\HomepageCustomImageTranslation;
use App\Entity\Translation\HomepageCustomSolutionTranslation;
use App\Entity\Translation\HomepageFeaturedLabItemTranslation;
use App\Entity\Translation\HomepageHeroTranslation;
use App\Entity\Translation\HomepageHumanFocusedTranslation;
use App\Entity\Translation\HomepagePanelTranslation;
use App\Entity\Translation\HomepageProductFeatureTranslation;
use App\Entity\Translation\HomepageProductTranslation;
use App\Entity\Translation\HomepageRentalsImageTranslation;
use App\Entity\Translation\HomepageTextAnimationTranslation;
use App\Entity\Translation\HomepageTrackingFeatureTranslation;
use App\Entity\Translation\HomepageWhyCardTranslation;
use App\Entity\Translation\HomepageWhySectionTranslation;
use App\Entity\Translation\LabCategoryTranslation;
use App\Entity\Translation\LabPageContentTranslation;
use App\Entity\Translation\LabProjectTranslation;
use App\Entity\Translation\NavigationItemTranslation;
use App\Entity\Translation\PageTranslation;
use App\Entity\Translation\TeamPageContentTranslation;
use App\Entity\User;
use App\Enum\ContentStatus;
use App\Enum\ProductType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadUsers($manager);
        $this->loadHomepageHero($manager);
        $this->loadHomepagePanels($manager);
        $this->loadHomepageWhySection($manager);
        $this->loadHomepageWhyCards($manager);
        $this->loadHomepageCustomImage($manager);
        $this->loadHomepageCustomSolution($manager);
        $this->loadHomepageFeaturedLabItems($manager);
        $this->loadHomepageHumanFocused($manager);
        $this->loadHomepageTextAnimation($manager);
        $this->loadHomepageBillboard($manager);
        $this->loadHomepageAnalytics($manager);
        $this->loadHomepageTrackingFeatures($manager);
        $this->loadHomepageRentalsImage($manager);
        $this->loadHomepageProducts($manager);
        $this->loadBlogCategories($manager);
        $this->loadBlogPosts($manager);
        $this->loadLabCategories($manager);
        $this->loadLabProjects($manager);
        $this->loadEsgContent($manager);
        $this->loadNavigation($manager);
        $this->loadContactInfo($manager);
        $this->loadSocialLinks($manager);
        $this->loadPageContent($manager);
        $this->loadSettings($manager);
        $this->loadPrivacyPolicyPage($manager);

        $manager->flush();
    }

    // ─── USERS ───────────────────────────────────────────────

    private function loadUsers(ObjectManager $manager): void
    {
        foreach ([
            ['nikola@ngrdanjski.com', 'LaniakeaSC1988', 'Nikola', 'Grdanjski', ['ROLE_SUPER_ADMIN']],
            ['admin@go2digital.hr', 'admin123', 'Admin', 'Go2Digital', ['ROLE_SUPER_ADMIN']],
            ['editor@go2digital.hr', 'editor123', 'Editor', 'Go2Digital', ['ROLE_EDITOR']],
        ] as [$email, $pass, $first, $last, $roles]) {
            $user = new User();
            $user->setEmail($email);
            $user->setFirstName($first);
            $user->setLastName($last);
            $user->setPassword($this->hasher->hashPassword($user, $pass));
            $user->setRoles($roles);
            $manager->persist($user);
        }
    }

    // ─── HOMEPAGE HERO ───────────────────────────────────────

    private function loadHomepageHero(ObjectManager $manager): void
    {
        $hero = new HomepageHero();
        $this->addTranslation($hero, new HomepageHeroTranslation(), 'hr', [
            'titleLine1' => 'Brendovi', 'titleLine2' => 'sutrašnjice.',
            'kicker' => 'Dostupni za suradnju',
            'heading' => 'Pomažemo brendovima rasti i graditi ljudsku povezanost.',
            'description' => 'Digitalna agencija iz Zagreba za ambiciozne brendove spremne na uspjeh.',
            'scrollDownLabel' => 'Skrolaj dolje',
        ]);
        $this->addTranslation($hero, new HomepageHeroTranslation(), 'en', [
            'titleLine1' => 'Brands', 'titleLine2' => 'of tomorrow.',
            'kicker' => 'Available for work',
            'heading' => 'We help brands grow and build a human connection.',
            'description' => 'A Zagreb-based digital agency for ambitious brands ready to succeed.',
            'scrollDownLabel' => 'Scroll Down',
        ]);
        $manager->persist($hero);
    }

    // ─── HOMEPAGE PANELS (Horizontal Scroll) ─────────────────

    private function loadHomepagePanels(ObjectManager $manager): void
    {
        $panels = [
            ['2.5M', ['hr' => ['U Hrvatskoj, DOOH oglasi dosežu više od 2,5 milijuna ljudi, što predstavlja gotovo cijelu urbanu populaciju.', 'Doseg', 'Naša mreža digitalnih ekrana oblikuje način na koji brendovi komuniciraju s publikom. Svaki prikaz postaje trenutak pažnje, poruka u pokretu i prilika da se istakne ono što zaista vrijedi vidjeti.'], 'en' => ['In Croatia, DOOH ads reach more than 2.5 million people, representing almost the entire urban population.', 'Reach', 'Our network of digital screens shapes the way brands communicate with audiences. Each display becomes a moment of attention, a message in motion, and an opportunity to highlight what truly deserves to be seen.']]],
            ['460', ['hr' => ['Uz 460 digitalnih oglasnih površina na vrhunskim lokacijama, vaš brend ostvaruje izvanrednu vidljivost i snažan doseg.', 'Pokrivenost', 'Naša mreža digitalnih ekrana oblikuje način na koji brendovi komuniciraju s publikom. Svaki prikaz postaje trenutak pažnje, poruka u pokretu i prilika da se istakne ono što zaista vrijedi vidjeti.'], 'en' => ['With 460 digital advertising surfaces in premium locations, your brand achieves exceptional visibility and strong reach.', 'Coverage', 'Our network of digital screens shapes the way brands communicate with audiences. Each display becomes a moment of attention, a message in motion, and an opportunity to highlight what truly deserves to be seen.']]],
            ['50', ['hr' => ['S mrežom digitalnih ekrana u najvećim hrvatskim gradovima, vaš brend dobiva snažnu vidljivost i širok doseg u urbanim sredinama.', 'Doseg', 'Naša mreža digitalnih ekrana oblikuje način na koji brendovi komuniciraju s publikom. Svaki prikaz postaje trenutak pažnje, poruka u pokretu i prilika da se istakne ono što zaista vrijedi vidjeti.'], 'en' => ['With a network of digital screens in the largest Croatian cities, your brand gains strong visibility and wide reach in urban areas.', 'Reach', 'Our network of digital screens shapes the way brands communicate with audiences. Each display becomes a moment of attention, a message in motion, and an opportunity to highlight what truly deserves to be seen.']]],
            ['28', ['hr' => ['Kroz digitalne ekrane unutar vodećih shopping centara, vaš brend dopire do publike u trenutku kupnje.', 'Lokacije', 'Naša mreža digitalnih ekrana oblikuje način na koji brendovi komuniciraju s publikom. Svaki prikaz postaje trenutak pažnje, poruka u pokretu i prilika da se istakne ono što zaista vrijedi vidjeti.'], 'en' => ['Through digital screens inside leading shopping centers, your brand reaches audiences at the moment of purchase.', 'Locations', 'Our network of digital screens shapes the way brands communicate with audiences. Each display becomes a moment of attention, a message in motion, and an opportunity to highlight what truly deserves to be seen.']]],
        ];

        foreach ($panels as $i => [$stat, $trans]) {
            $panel = new HomepagePanel();
            $panel->setStatValue($stat);
            $panel->setSortOrder($i + 1);
            foreach ($trans as $locale => [$title, $tag, $desc]) {
                $t = new HomepagePanelTranslation();
                $t->setLocale($locale);
                $t->setTitle($title);
                $t->setTag($tag);
                $t->setDescription($desc);
                $panel->addTranslation($t);
            }
            $manager->persist($panel);
        }
    }

    // ─── HOMEPAGE WHY SECTION ────────────────────────────────

    private function loadHomepageWhySection(ObjectManager $manager): void
    {
        $section = new HomepageWhySection();
        $this->addTranslation($section, new HomepageWhySectionTranslation(), 'hr', [
            'label' => 'Zašto Go2Digital',
            'headline' => 'DOOH oglašavanje sljedeće razine, za brendove današnjice.',
        ]);
        $this->addTranslation($section, new HomepageWhySectionTranslation(), 'en', [
            'label' => 'Why Go2Digital',
            'headline' => 'Next-level DOOH advertising, built for modern brands.',
        ]);
        $manager->persist($section);
    }

    private function loadHomepageWhyCards(ObjectManager $manager): void
    {
        $cards = [
            [[0, 0, 1, 0, 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1], 'hr' => ['Mjerljiv učinak', 'Prati, analiziraj i optimiziraj svaku kampanju uz precizne podatke.'], 'en' => ['Measurable Impact', 'Track, analyze, and optimize every campaign with precise, data-driven performance insights.']],
            [[0, 0, 1, 0, 0, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0], 'hr' => ['Središte pažnje', 'Postavite svoj brand u središte pažnje na digitalnim ekranima u najfrekventnijim zonama.'], 'en' => ['Center Stage', 'Put your brand front and center on vibrant digital screens in the busiest, most visible locations.']],
            [[1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1], 'hr' => ['Doseg bez granica', 'Povežite se s milijunima ljudi putem nacionalne mreže premium digitalnih ekrana sa najvećim dosegom.'], 'en' => ['Limitless Reach', 'Connect with millions through a nationwide network of premium digital displays in high-traffic locations.']],
        ];

        foreach ($cards as $i => $data) {
            $card = new HomepageWhyCard();
            $card->setDotPattern($data[0]);
            $card->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new HomepageWhyCardTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $card->addTranslation($t);
            }
            $manager->persist($card);
        }
    }

    // ─── HOMEPAGE CUSTOM IMAGE ───────────────────────────────

    private function loadHomepageCustomImage(ObjectManager $manager): void
    {
        $img = new HomepageCustomImage();
        $this->addTranslation($img, new HomepageCustomImageTranslation(), 'hr', ['alt' => 'Digitalni billboard na Slavonskoj aveniji']);
        $this->addTranslation($img, new HomepageCustomImageTranslation(), 'en', ['alt' => 'Slavonska Avenue digital billboard']);
        $manager->persist($img);
    }

    // ─── HOMEPAGE CUSTOM SOLUTION ────────────────────────────

    private function loadHomepageCustomSolution(ObjectManager $manager): void
    {
        $sol = new HomepageCustomSolution();
        $this->addTranslation($sol, new HomepageCustomSolutionTranslation(), 'hr', [
            'indicator' => 'Prilagođena Rješenja',
            'title' => 'Ne vjerujemo u univerzalna rješenja. Svaki brend zaslužuje individualni pristup. Zato kreiramo personalizirana rješenja usklađena s vašim brendom, idejom i porukom – onako kako želite da vas publika zapamti.',
            'block1' => 'Svaki projekt razvijamo u bliskoj suradnji s klijentom, definirajući jasne ciljeve i prepoznajući specifičnosti brenda. Kroz pametan odabir lokacija, kreativne formate i inovativne sadržaje, osiguravamo kampanje koje donose mjerljive rezultate i jačaju prepoznatljivost.',
            'block2' => 'Od impozantnih digitalnih ekrana do suptilnih interaktivnih trenutaka, naši projekti spajaju formu, funkcionalnost i doživljaj. Tako stvaramo iskustva koja privlače pažnju, angažiraju publiku i ostavljaju trajan dojam.',
        ]);
        $this->addTranslation($sol, new HomepageCustomSolutionTranslation(), 'en', [
            'indicator' => 'Custom Solutions',
            'title' => "We don't believe in one-size-fits-all. Every brand deserves a space that feels made for them. That's why we build custom digital solutions designed to fit your idea, your message, and the way you want people to remember it.",
            'block1' => "Not every idea fits in the same frame. Some need to move, react, or adapt to the moment. That's why we create custom, interactive solutions shaped around the story you want to tell.",
            'block2' => 'From large digital displays to subtle interactive moments, every project is a mix of form, function, and experience designed to impact your audience and leave a lasting impression.',
        ]);
        $manager->persist($sol);
    }

    // ─── HOMEPAGE FEATURED LAB ITEMS ─────────────────────────

    private function loadHomepageFeaturedLabItems(ObjectManager $manager): void
    {
        $items = [
            ['interactive-billboard', ['Interactive', 'DOOH'], 'hr' => ['Interactive Billboard', 'Pretvorite statične displeje u angažirajuća interaktivna iskustva.'], 'en' => ['Interactive Billboard', 'Transform static displays into engaging interactive experiences.']],
            ['ar-experience', ['Augmented Reality', 'Mobile'], 'hr' => ['AR Experience', 'Spojite digitalni sadržaj sa stvarnim svijetom kroz proširenu stvarnost.'], 'en' => ['AR Experience', 'Blend digital content with the real world through augmented reality.']],
            ['dynamic-content', ['Real-time', 'Data-driven'], 'hr' => ['Dynamic Content', 'Sadržaj koji se prilagođava u stvarnom vremenu na temelju podataka i konteksta.'], 'en' => ['Dynamic Content', 'Content that adapts in real-time based on data and context.']],
        ];

        foreach ($items as $i => $data) {
            $item = new HomepageFeaturedLabItem();
            $item->setSlug($data[0]);
            $item->setCategories($data[1]);
            $item->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new HomepageFeaturedLabItemTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setSubtitle($data[$locale][1]);
                $item->addTranslation($t);
            }
            $manager->persist($item);
        }
    }

    // ─── HOMEPAGE HUMAN FOCUSED ──────────────────────────────

    private function loadHomepageHumanFocused(ObjectManager $manager): void
    {
        $hf = new HomepageHumanFocused();
        $this->addTranslation($hf, new HomepageHumanFocusedTranslation(), 'hr', [
            'indicator' => 'Fokus na ljude',
            'title' => 'Stvaramo kampanje koje povezuju brendove s ljudima na značajan način.',
            'blockLeft' => 'Svaki ekran je prilika za stvaranje trenutka koji rezonira s publikom.',
            'blockRight' => 'Naš pristup stavlja ljude u središte svakog projekta. Kombinirajući kreativnost s podacima, osiguravamo da svaka kampanja dopre do prave publike u pravom trenutku.',
        ]);
        $this->addTranslation($hf, new HomepageHumanFocusedTranslation(), 'en', [
            'indicator' => 'Human Focused',
            'title' => 'We create campaigns that connect brands with people in meaningful ways.',
            'blockLeft' => 'Every screen is an opportunity to create a moment that resonates with the audience.',
            'blockRight' => 'Our approach puts people at the center of every project. By combining creativity with data, we ensure that every campaign reaches the right audience at the right time.',
        ]);
        $manager->persist($hf);
    }

    // ─── HOMEPAGE TEXT ANIMATION ─────────────────────────────

    private function loadHomepageTextAnimation(ObjectManager $manager): void
    {
        $ta = new HomepageTextAnimation();
        $this->addTranslation($ta, new HomepageTextAnimationTranslation(), 'hr', ['word1' => 'Vidljivost', 'word2' => 'Inovacija', 'word3' => 'Rezultati']);
        $this->addTranslation($ta, new HomepageTextAnimationTranslation(), 'en', ['word1' => 'Visibility', 'word2' => 'Innovation', 'word3' => 'Results']);
        $manager->persist($ta);
    }

    // ─── HOMEPAGE BILLBOARD ──────────────────────────────────

    private function loadHomepageBillboard(ObjectManager $manager): void
    {
        $bb = new HomepageBillboard();
        $bb->setButtonUrl('/kontakt');
        $this->addTranslation($bb, new HomepageBillboardTranslation(), 'hr', [
            'title' => 'Vaš brend zaslužuje najbolju poziciju u gradu.',
            'subtitle' => 'Premium lokacije',
            'description' => 'Strateški pozicionirani digitalni ekrani na najfrekventnijim lokacijama osiguravaju maksimalnu vidljivost vašeg brenda.',
            'buttonText' => 'Zatraži ponudu',
            'imageAlt' => 'Go2Digital billboard na Radničkoj cesti',
        ]);
        $this->addTranslation($bb, new HomepageBillboardTranslation(), 'en', [
            'title' => 'Your brand deserves the best position in the city.',
            'subtitle' => 'Premium Locations',
            'description' => 'Strategically positioned digital screens at the most frequented locations ensure maximum visibility for your brand.',
            'buttonText' => 'Request a Quote',
            'imageAlt' => 'Go2Digital billboard on Radnička Road',
        ]);
        $manager->persist($bb);
    }

    // ─── HOMEPAGE ANALYTICS ────────────────────────────────

    private function loadHomepageAnalytics(ObjectManager $manager): void
    {
        $a = new HomepageAnalytics();
        $this->addTranslation($a, new HomepageAnalyticsTranslation(), 'hr', [
            'indicator' => 'Mjerite Uspjeh Kampanje',
            'title' => 'Impresije',
            'description' => 'Po završetku svake kampanje, šaljemo vam detaljan postbuy report.',
        ]);
        $this->addTranslation($a, new HomepageAnalyticsTranslation(), 'en', [
            'indicator' => 'Measure Campaign Success',
            'title' => 'Impressions',
            'description' => 'After every campaign, we send you a detailed postbuy report.',
        ]);
        $manager->persist($a);
    }

    // ─── HOMEPAGE TRACKING FEATURES ──────────────────────────

    private function loadHomepageTrackingFeatures(ObjectManager $manager): void
    {
        $features = [
            ['hr' => ['Prikazi u stvarnom vremenu', 'Pratite koliko puta je vaš oglas prikazan sa sekundnom preciznošću.'], 'en' => ['Real-time Displays', 'Track how many times your ad was shown with second-by-second precision.']],
            ['hr' => ['Dokaz prikazivanja', 'Automatski generirani izvještaji s fotografijama kao dokaz emitiranja.'], 'en' => ['Proof of Display', 'Automatically generated reports with photos as proof of broadcast.']],
            ['hr' => ['Analiza publike', 'Detaljni podaci o demografiji i ponašanju publike na svakoj lokaciji.'], 'en' => ['Audience Analysis', 'Detailed data on demographics and audience behavior at each location.']],
            ['hr' => ['Optimizacija kampanje', 'Prilagodite kampanju u hodu na temelju performansi i podataka.'], 'en' => ['Campaign Optimization', 'Adjust your campaign on the fly based on performance and data.']],
            ['hr' => ['ROI izvještaji', 'Mjerite povrat investicije s preciznim metrikama i benchmarkovima.'], 'en' => ['ROI Reports', 'Measure return on investment with precise metrics and benchmarks.']],
        ];

        foreach ($features as $i => $data) {
            $feat = new HomepageTrackingFeature();
            $feat->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new HomepageTrackingFeatureTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $feat->addTranslation($t);
            }
            $manager->persist($feat);
        }
    }

    // ─── HOMEPAGE RENTALS IMAGE ──────────────────────────────

    private function loadHomepageRentalsImage(ObjectManager $manager): void
    {
        $ri = new HomepageRentalsImage();
        $this->addTranslation($ri, new HomepageRentalsImageTranslation(), 'hr', ['text' => 'RENTALS']);
        $this->addTranslation($ri, new HomepageRentalsImageTranslation(), 'en', ['text' => 'RENTALS']);
        $manager->persist($ri);
    }

    // ─── HOMEPAGE PRODUCTS (Display + Cube) ──────────────────

    private function loadHomepageProducts(ObjectManager $manager): void
    {
        // --- Display product ---
        $display = new HomepageProduct();
        $display->setProductType(ProductType::Display);
        $display->setSpecs([
            ['label' => 'Rezolucija', 'value' => 'Full HD / 4K'],
            ['label' => 'Osvjetljenje', 'value' => '5000+ nita'],
            ['label' => 'Kut gledanja', 'value' => '178°'],
            ['label' => 'Životni vijek', 'value' => '100.000+ sati'],
            ['label' => 'Pokret', 'value' => 'Full Motion'],
            ['label' => 'Povezivost', 'value' => '4G / WiFi'],
        ]);
        $this->addTranslation($display, new HomepageProductTranslation(), 'hr', [
            'title' => "Digitalni\nekrani", 'badge' => 'Premium DOOH Mreža',
            'description' => 'Naši premium digitalni ekrani pružaju neusporedivu kvalitetu prikaza s visokom rezolucijom, punim pokretom i širokim kutom gledanja – osiguravajući da vaša poruka bude viđena jasno, dan i noć.',
            'specsTitle' => 'Specifikacije ekrana', 'downloadLabel' => 'Preuzmi specifikacije',
            'indicatorText' => 'O TEHNOLOGIJI',
        ]);
        $this->addTranslation($display, new HomepageProductTranslation(), 'en', [
            'title' => "Digital\nscreens", 'badge' => 'Premium DOOH Network',
            'description' => 'Our premium digital screens deliver unparalleled display quality with high resolution, full motion, and wide viewing angles – ensuring your message is seen clearly, day and night.',
            'specsTitle' => 'Screen Specifications', 'downloadLabel' => 'Download specifications',
            'indicatorText' => 'ABOUT THE TECHNOLOGY',
        ]);
        $manager->persist($display);

        // Display features
        $displayFeatures = [
            ['Ⓐ', 'hr' => ['Visoka vidljivost', 'Ekrani s visokim osvjetljenjem vidljivi i po danu, s automatskim podešavanjem osvjetljenja za optimalnu potrošnju energije.'], 'en' => ['High Visibility', 'High-brightness screens visible even in daylight, with automatic brightness adjustment for optimal energy consumption.']],
            ['Ⓑ', 'hr' => ['Dinamički sadržaj', 'Prilagodite poruku u stvarnom vremenu prema dobu dana, vremenskim uvjetima ili ciljnoj publici za maksimalan učinak.'], 'en' => ['Dynamic Content', 'Adapt your message in real-time based on time of day, weather conditions, or target audience for maximum impact.']],
            ['Ⓒ', 'hr' => ['Inteligentno praćenje', 'Napredna analitika i praćenje prikazivanja u stvarnom vremenu za potpunu transparentnost vaše kampanje.'], 'en' => ['Intelligent Tracking', 'Advanced analytics and real-time display monitoring for complete campaign transparency.']],
            ['Ⓓ', 'hr' => ['Održivo rješenje', 'Energetski učinkoviti LED ekrani s niskim ugljičnim otiskom i mogućnošću napajanja iz obnovljivih izvora energije.'], 'en' => ['Sustainable Solution', 'Energy-efficient LED screens with low carbon footprint and renewable energy powering options.']],
        ];
        foreach ($displayFeatures as $i => $data) {
            $feat = new HomepageProductFeature();
            $feat->setProduct($display);
            $feat->setIcon($data[0]);
            $feat->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new HomepageProductFeatureTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $feat->addTranslation($t);
            }
            $manager->persist($feat);
        }

        // --- Cube product ---
        $cube = new HomepageProduct();
        $cube->setProductType(ProductType::Cube);
        $cube->setSpecs([
            ['label' => 'Format', 'value' => 'Citylight / Totem'],
            ['label' => 'Rezolucija', 'value' => 'Full HD'],
            ['label' => 'Osvjetljenje', 'value' => '2500 nita'],
            ['label' => 'Pokret', 'value' => 'Full Motion'],
            ['label' => 'Ekran', 'value' => '55" / 75"'],
            ['label' => 'Zaštita', 'value' => 'Kaljeno staklo'],
        ]);
        $this->addTranslation($cube, new HomepageProductTranslation(), 'hr', [
            'title' => "Digitalni\ncitylight", 'badge' => 'Inovativni format',
            'description' => 'Citylight formati pružaju izvanrednu vidljivost na pješačkim zonama i u trgovačkim centrima, s punim pokretnim sadržajem koji privlači pažnju prolaznika.',
            'specsTitle' => 'Specifikacije citylight-a', 'downloadLabel' => 'Preuzmi specifikacije',
            'indicatorText' => '',
        ]);
        $this->addTranslation($cube, new HomepageProductTranslation(), 'en', [
            'title' => "Digital\ncitylight", 'badge' => 'Innovative Format',
            'description' => 'Citylight formats provide outstanding visibility in pedestrian zones and shopping centers, with full motion content that captures the attention of passersby.',
            'specsTitle' => 'Citylight Specifications', 'downloadLabel' => 'Download specifications',
            'indicatorText' => '',
        ]);
        $manager->persist($cube);

        // Cube features
        $cubeFeatures = [
            ['Ⓐ', 'hr' => ['Pješačke zone', 'Pozicionirani na najfrekventnijim pješačkim lokacijama za maksimalan kontakt s publikom.'], 'en' => ['Pedestrian Zones', 'Positioned at the most frequented pedestrian locations for maximum audience contact.']],
            ['Ⓑ', 'hr' => ['Indoor & Outdoor', 'Dostupni u indoor i outdoor varijantama, prilagođeni specifičnostima svake lokacije.'], 'en' => ['Indoor & Outdoor', 'Available in indoor and outdoor variants, adapted to the specifics of each location.']],
        ];
        foreach ($cubeFeatures as $i => $data) {
            $feat = new HomepageProductFeature();
            $feat->setProduct($cube);
            $feat->setIcon($data[0]);
            $feat->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new HomepageProductFeatureTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $feat->addTranslation($t);
            }
            $manager->persist($feat);
        }
    }

    // ─── BLOG ────────────────────────────────────────────────

    private function loadBlogCategories(ObjectManager $manager): void
    {
        $cats = ['marketing' => ['Marketing', 'Marketing'], 'design' => ['Dizajn', 'Design'], 'technology' => ['Tehnologija', 'Technology'], 'news' => ['Vijesti', 'News']];
        foreach ($cats as $slug => [$hr, $en]) {
            $cat = new BlogCategory();
            $cat->setSlug($slug);
            $cat->setSortOrder(array_search($slug, array_keys($cats)) + 1);
            $this->addTranslation($cat, new BlogCategoryTranslation(), 'hr', ['name' => $hr]);
            $this->addTranslation($cat, new BlogCategoryTranslation(), 'en', ['name' => $en]);
            $manager->persist($cat);
            $this->setReference('blog_cat_'.$slug, $cat);
        }
    }

    private function loadBlogPosts(ObjectManager $manager): void
    {
        $posts = [
            ['future-digital-marketing-2025', '2025-01-10', 'marketing', 'The Future of Digital Marketing in 2025', 'Budućnost digitalnog marketinga u 2025.'],
            ['ai-transforming-creative-design', '2025-01-08', 'design', 'How AI is Transforming Creative Design', 'Kako AI transformira kreativni dizajn'],
            ['sustainable-digital-campaigns', '2025-01-05', 'marketing', 'Building Sustainable Digital Campaigns', 'Izgradnja održivih digitalnih kampanja'],
            ['interactive-dooh-advertising', '2025-01-03', 'technology', 'The Rise of Interactive DOOH Advertising', 'Uspon interaktivnog DOOH oglašavanja'],
            ['brand-identity-trends', '2024-12-28', 'design', 'Brand Identity Trends for the New Year', 'Trendovi brendiranja za novu godinu'],
            ['maximizing-roi-programmatic', '2024-12-25', 'marketing', 'Maximizing ROI with Programmatic Advertising', 'Maksimiziranje ROI-a s programatskim oglašavanjem'],
            ['go2digital-excellence-award', '2024-12-20', 'news', 'Go2Digital Wins Excellence Award', 'Go2Digital osvaja nagradu za izvrsnost'],
            ['ux-design-best-practices', '2024-12-18', 'design', 'UX Design Best Practices for 2025', 'Najbolje prakse UX dizajna za 2025.'],
            ['data-driven-creativity', '2024-12-15', 'technology', 'The Power of Data-Driven Creativity', 'Moć kreativnosti vođene podacima'],
            ['new-partnership-announcement', '2024-12-12', 'news', 'New Partnership Announcement', 'Najava novog partnerstva'],
            ['memorable-brand-experiences', '2024-12-10', 'marketing', 'Creating Memorable Brand Experiences', 'Stvaranje nezaboravnih iskustava brenda'],
            ['motion-design-advertising', '2024-12-08', 'design', 'Motion Design in Digital Advertising', 'Motion dizajn u digitalnom oglašavanju'],
            ['smart-city-ooh-integration', '2024-12-05', 'technology', 'Smart City Integration for OOH Media', 'Integracija pametnog grada za OOH medije'],
        ];

        foreach ($posts as [$slug, $date, $catSlug, $enTitle, $hrTitle]) {
            $post = new BlogPost();
            $post->setSlug($slug);
            $post->setDate(new \DateTime($date));
            $post->setAuthor('Go2Digital');
            $post->setCategory($this->getReference('blog_cat_'.$catSlug, BlogCategory::class));
            $post->setStatus(ContentStatus::Published);
            $this->addTranslation($post, new BlogPostTranslation(), 'hr', ['title' => $hrTitle]);
            $this->addTranslation($post, new BlogPostTranslation(), 'en', ['title' => $enTitle]);
            $manager->persist($post);
        }
    }

    // ─── LAB ─────────────────────────────────────────────────

    private function loadLabCategories(ObjectManager $manager): void
    {
        $cats = ['web' => 'Web', 'mobile' => 'Mobile', 'branding' => 'Branding', 'ai' => 'AI'];
        foreach ($cats as $slug => $name) {
            $cat = new LabCategory();
            $cat->setSlug($slug);
            $cat->setSortOrder(array_search($slug, array_keys($cats)) + 1);
            $this->addTranslation($cat, new LabCategoryTranslation(), 'hr', ['name' => $name]);
            $this->addTranslation($cat, new LabCategoryTranslation(), 'en', ['name' => $name]);
            $manager->persist($cat);
            $this->setReference('lab_cat_'.$slug, $cat);
        }
    }

    private function loadLabProjects(ObjectManager $manager): void
    {
        $projectData = [
            ['ai-analytics-dashboard', ['ai', 'web'], ['AI-Powered Analytics Dashboard', 'AI Analytics', 'Real-time data visualization with machine learning insights'], ['AI analitička ploča', 'AI Analitika', 'Vizualizacija podataka u stvarnom vremenu s uvidima strojnog učenja']],
            ['mobile-shop', ['mobile'], ['E-Commerce Mobile Experience', 'Mobile Shop', 'Native iOS and Android shopping application with AR features'], ['Mobilno iskustvo kupovine', 'Mobile Shop', 'Nativna iOS i Android aplikacija za kupovinu s AR značajkama']],
            ['brand-identity', ['branding'], ['Brand Identity System', 'Brand System', 'Complete visual identity including logo, typography, and guidelines'], ['Sustav vizualnog identiteta', 'Brand System', 'Kompletan vizualni identitet uključujući logo, tipografiju i smjernice']],
            ['web-platform', ['web'], ['Interactive Web Platform', 'Web Platform', 'Modern web application with real-time collaboration features'], ['Interaktivna web platforma', 'Web platforma', 'Moderna web aplikacija sa značajkama suradnje u stvarnom vremenu']],
            ['iot-dashboard', ['ai', 'web'], ['Smart City IoT Dashboard', 'IoT Dashboard', 'Connected city infrastructure monitoring and management'], ['IoT nadzorna ploča pametnog grada', 'IoT Dashboard', 'Praćenje i upravljanje povezanom gradskom infrastrukturom']],
            ['fitness-app', ['mobile'], ['Fitness Tracking App', 'Fitness App', 'Health and wellness mobile application with wearable integration'], ['Aplikacija za praćenje fitnesa', 'Fitness App', 'Mobilna aplikacija za zdravlje i wellness s integracijom nosivih uređaja']],
        ];

        foreach ($projectData as [$slug, $catSlugs, $en, $hr]) {
            $project = new LabProject();
            $project->setSlug($slug);
            $project->setStatus(ContentStatus::Published);
            foreach ($catSlugs as $cs) {
                $project->addCategory($this->getReference('lab_cat_'.$cs, LabCategory::class));
            }
            $t1 = new LabProjectTranslation();
            $t1->setLocale('en');
            $t1->setTitle($en[0]);
            $t1->setShortTitle($en[1]);
            $t1->setSubtitle($en[2]);
            $project->addTranslation($t1);

            $t2 = new LabProjectTranslation();
            $t2->setLocale('hr');
            $t2->setTitle($hr[0]);
            $t2->setShortTitle($hr[1]);
            $t2->setSubtitle($hr[2]);
            $project->addTranslation($t2);

            $manager->persist($project);
        }
    }

    // ─── ESG ─────────────────────────────────────────────────

    private function loadEsgContent(ObjectManager $manager): void
    {
        // Page content
        $esg = new EsgPageContent();
        $this->addTranslation($esg, new EsgPageContentTranslation(), 'hr', [
            'heroLabel' => 'ESG Strategija',
            'introSmall' => 'Vodeća tvrtka za vanjsko oglašavanje u Hrvatskoj predstavlja novi strateški smjer',
            'introLarge' => 'Od 2021. godine ESG kao nova strateška odrednica obuhvaća tri smjera: Go2Green, Činimo dobro i Smart city kroz koje smo podržali brojne incijative.',
            'downloadReportLabel' => 'Preuzmi ESG izvještaj',
        ]);
        $this->addTranslation($esg, new EsgPageContentTranslation(), 'en', [
            'heroLabel' => 'Environmental, Social & Governance',
            'introSmall' => 'Our ESG Policy',
            'introLarge' => 'We believe that digital advertising can be sustainable. Through innovative technologies and socially responsible practices, we are building a future where brands can grow without compromising the environment and society.',
            'downloadReportLabel' => 'Download ESG Report',
        ]);
        $manager->persist($esg);

        // Pillars
        $pillars = [
            ['environment', 'hr' => ['Okoliš', 'Smanjujemo ugljični otisak korištenjem energetski učinkovitih digitalnih ekrana i obnovljivih izvora energije.'], 'en' => ['Environment', 'We reduce our carbon footprint by using energy-efficient digital screens and renewable energy sources.']],
            ['social', 'hr' => ['Društvo', 'Ulažemo u lokalnu zajednicu kroz edukativne programe, podršku neprofitnim organizacijama i promociju kulturnih događanja.'], 'en' => ['Social', 'We invest in the local community through educational programs, support for non-profit organizations, and promotion of cultural events.']],
            ['governance', 'hr' => ['Upravljanje', 'Provodimo transparentne poslovne prakse s visokim etičkim standardima i odgovornim korporativnim upravljanjem.'], 'en' => ['Governance', 'We implement transparent business practices with high ethical standards and responsible corporate governance.']],
        ];
        foreach ($pillars as $i => $data) {
            $pillar = new EsgPillar();
            $pillar->setIcon($data[0]);
            $pillar->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new EsgPillarTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $pillar->addTranslation($t);
            }
            $manager->persist($pillar);
        }

        // Cards
        $cards = [
            ['hr' => 'Digitalni ekrani omogućuju dinamičan sadržaj bez potrebe za tiskanim materijalima, značajno smanjujući otpad.', 'en' => 'Digital screens enable dynamic content without the need for printed materials, significantly reducing waste.'],
            ['hr' => 'Zeleni tornjevi s košnicama za pčele i autohtonim biljkama doprinose bioraznolikosti urbanih sredina.', 'en' => 'Green towers with beehives and native plants contribute to urban biodiversity.'],
            ['hr' => 'HEPA filteri u citylight ekranima pročišćavaju zrak od sitnih čestica i zagađenja iz prometa.', 'en' => 'HEPA filters in citylight screens purify the air from fine particles and traffic pollution.'],
        ];
        foreach ($cards as $i => $data) {
            $card = new EsgCard();
            $card->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new EsgCardTranslation();
                $t->setLocale($locale);
                $t->setText($data[$locale]);
                $card->addTranslation($t);
            }
            $manager->persist($card);
        }

        // Vision badges
        $badges = [
            ['hr' => ['Digitalni Ekrani', 'Sama srž našeg poslovanja je digitalna i time smanjujemo utjecaj na okoliš i gradimo održiviju budućnost oglašavanja.'], 'en' => ['Digital Screens', 'The very core of our business is digital, and thus we reduce our environmental impact and build a more sustainable future for advertising.']],
            ['hr' => ['Zeleni Tornjevi', ''], 'en' => ['Green Towers', '']],
            ['hr' => ['Čišćenje Zraka', ''], 'en' => ['Air Cleaning', '']],
        ];
        foreach ($badges as $i => $data) {
            $badge = new EsgVisionBadge();
            $badge->setSortOrder($i + 1);
            foreach (['hr', 'en'] as $locale) {
                $t = new EsgVisionBadgeTranslation();
                $t->setLocale($locale);
                $t->setTitle($data[$locale][0]);
                $t->setDescription($data[$locale][1]);
                $badge->addTranslation($t);
            }
            $manager->persist($badge);
        }
    }

    // ─── NAVIGATION ──────────────────────────────────────────

    private function loadNavigation(ObjectManager $manager): void
    {
        $mainNav = [
            ['/', 'Početna', 'Home'],
            ['/lab', 'Lab', 'Lab'],
            ['/blog', 'Blog', 'Blog'],
            ['/kontakt', 'Kontakt', 'Contact'],
            ['/esg', 'ESG', 'ESG'],
        ];

        foreach ($mainNav as $i => [$url, $hr, $en]) {
            $nav = new NavigationItem();
            $nav->setUrl($url);
            $nav->setSortOrder($i + 1);
            $nav->setGroup('main');
            $this->addTranslation($nav, new NavigationItemTranslation(), 'hr', ['label' => $hr]);
            $this->addTranslation($nav, new NavigationItemTranslation(), 'en', ['label' => $en]);
            $manager->persist($nav);
        }

        $footerNav = [
            ['/', 'Početna', 'Home'],
            ['/lab', 'Lab', 'Lab'],
            ['/blog', 'Blog', 'Blog'],
            ['/tim', 'O nama', 'About'],
            ['/kontakt', 'Kontakt', 'Contact'],
        ];

        foreach ($footerNav as $i => [$url, $hr, $en]) {
            $nav = new NavigationItem();
            $nav->setUrl($url);
            $nav->setSortOrder($i + 1);
            $nav->setGroup('footer');
            $this->addTranslation($nav, new NavigationItemTranslation(), 'hr', ['label' => $hr]);
            $this->addTranslation($nav, new NavigationItemTranslation(), 'en', ['label' => $en]);
            $manager->persist($nav);
        }
    }

    // ─── CONTACT INFO ────────────────────────────────────────

    private function loadContactInfo(ObjectManager $manager): void
    {
        $contacts = [
            ['email', 'sales@go2digital.com', 'mailto:sales@go2digital.com', false],
            ['phone', '+385 1 483 9192', 'tel:+38514839192', false],
            ['address', 'Radnička cesta 52, 10 000 Zagreb', 'https://www.google.com/maps/dir/?api=1&destination=Radnička+cesta+52,+10000+Zagreb,+Croatia', true],
        ];

        foreach ($contacts as $i => [$key, $value, $href, $external]) {
            $contact = new ContactInfo();
            $contact->setKey($key);
            $contact->setValue($value);
            $contact->setHref($href);
            $contact->setIsExternal($external);
            $contact->setSortOrder($i + 1);
            $manager->persist($contact);
        }
    }

    // ─── SOCIAL LINKS ────────────────────────────────────────

    private function loadSocialLinks(ObjectManager $manager): void
    {
        $links = [
            ['linkedin', 'https://linkedin.com/company/go2digital'],
            ['instagram', 'https://instagram.com/go2digital'],
            ['facebook', 'https://facebook.com/go2digital'],
            ['tiktok', 'https://tiktok.com/@go2digital'],
        ];

        foreach ($links as $i => [$platform, $url]) {
            $link = new SocialLink();
            $link->setPlatform($platform);
            $link->setUrl($url);
            $link->setSortOrder($i + 1);
            $manager->persist($link);
        }
    }

    // ─── PAGE CONTENT SINGLETONS ─────────────────────────────

    private function loadPageContent(ObjectManager $manager): void
    {
        // Blog page content
        $blog = new BlogPageContent();
        $this->addTranslation($blog, new BlogPageContentTranslation(), 'hr', [
            'pageTitle' => 'Blog - Go2Digital', 'title' => 'Blog', 'filterAllLabel' => 'Sve',
            'readMoreLabel' => 'Pročitaj više', 'noResultsTitle' => 'Nema pronađenih članaka',
            'noResultsText' => 'Pokušajte odabrati drugu kategoriju ili pogledajte sve članke.',
            'viewAllLabel' => 'Prikaži sve članke', 'allLoadedText' => 'Svi članci učitani',
        ]);
        $this->addTranslation($blog, new BlogPageContentTranslation(), 'en', [
            'pageTitle' => 'Blog - Go2Digital', 'title' => 'Blog', 'filterAllLabel' => 'All',
            'readMoreLabel' => 'Read more', 'noResultsTitle' => 'No articles found',
            'noResultsText' => 'Try selecting a different category or view all articles.',
            'viewAllLabel' => 'View all articles', 'allLoadedText' => 'All articles loaded',
        ]);
        $manager->persist($blog);

        // Lab page content
        $lab = new LabPageContent();
        $this->addTranslation($lab, new LabPageContentTranslation(), 'hr', [
            'pageTitle' => 'Go2Labs - Go2Digital', 'breadcrumb' => 'O Go2Labsu',
            'intro' => "Go2Labs je kreativni tim koji osmišljava\ninovativne ideje za našu mrežu digitalnih ekrana.\nTehnološki naprednim rješenjima pretvaraju\nprostor u doživljaje koji izazivaju wow efekt –\npovezujući brend s publikom na potpuno novi\nnačin.",
            'title' => 'Labs', 'filterAllLabel' => 'Sve',
            'noResultsText' => 'Nema rezultata za odabranu kategoriju.',
            'viewAllLabel' => 'Prikaži sve', 'viewProjectLabel' => 'Pogledaj projekt',
        ]);
        $this->addTranslation($lab, new LabPageContentTranslation(), 'en', [
            'pageTitle' => 'Go2Labs - Go2Digital', 'breadcrumb' => 'About Go2Labs',
            'intro' => 'Go2Labs is a creative team that comes up with innovative ideas for our digital display network. With technologically advanced solutions, they turn the space into an experience that causes a wow effect - connecting the brand with the public in a completely new way.',
            'title' => 'Labs', 'filterAllLabel' => 'All',
            'noResultsText' => 'No results for selected category.',
            'viewAllLabel' => 'View all', 'viewProjectLabel' => 'View project',
        ]);
        $manager->persist($lab);

        // Contact page content
        $contact = new ContactPageContent();
        $this->addTranslation($contact, new ContactPageContentTranslation(), 'hr', [
            'pageTitle' => 'Kontakt', 'batteryLine1' => 'Mi smo dostupni od', 'batteryLine2' => '9 do 17 sati',
        ]);
        $this->addTranslation($contact, new ContactPageContentTranslation(), 'en', [
            'pageTitle' => 'Contact', 'batteryLine1' => 'We are available from', 'batteryLine2' => '9 AM to 5 PM',
        ]);
        $manager->persist($contact);

        // Team page content
        $team = new TeamPageContent();
        $this->addTranslation($team, new TeamPageContentTranslation(), 'hr', ['pageTitle' => 'O nama', 'intro' => '']);
        $this->addTranslation($team, new TeamPageContentTranslation(), 'en', ['pageTitle' => 'About', 'intro' => '']);
        $manager->persist($team);
    }

    // ─── SETTINGS ────────────────────────────────────────────

    private function loadSettings(ObjectManager $manager): void
    {
        $settings = [
            ['homepage.tracking.title', ['hr' => 'Praćenje i analitika u stvarnom vremenu.', 'en' => 'Real-time tracking and analytics.'], 'homepage'],
            ['homepage.tracking.buttonText', ['hr' => 'Saznaj više', 'en' => 'Learn More'], 'homepage'],
            ['homepage.featuredLabs.label', ['hr' => 'Go2Labs', 'en' => 'Go2Labs'], 'homepage'],
            ['homepage.featuredLabs.title', ['hr' => 'Istaknuti projekti', 'en' => 'Featured Labs'], 'homepage'],
            ['homepage.featuredLabs.buttonText', ['hr' => 'Zatraži ponudu', 'en' => 'Request a quote'], 'homepage'],
            ['homepage.featuredLabs.description', ['hr' => 'Kreiramo različita rješenja, od interaktivnih iskustva do personaliziranih brend aktivacija. To je prostor u kojem isprobavamo, razvijamo i stvaramo kampanje koje ostavljaju trag.', 'en' => 'From interactive experiences to custom brand activations, it\'s where we test, create, and invent for the public space.'], 'homepage'],
            ['homepage.featuredLabs.mode', ['value' => 'auto'], 'homepage'],
            ['homepage.featuredLabs.selectedProjectIds', ['value' => []], 'homepage'],
            ['homepage.horizontalScroll.scrollLabel', ['hr' => 'Scroll', 'en' => 'Scroll'], 'homepage'],
            ['footer.rights', ['hr' => 'Sva prava pridržana', 'en' => 'All rights reserved'], 'footer'],
            ['footer.newsletter.title', ['hr' => 'Newsletter', 'en' => 'Newsletter'], 'footer'],
            ['footer.newsletter.description', ['hr' => 'Bez spama, obećavamo. Možete se odjaviti bilo kada.', 'en' => 'No spam, we promise. You can unsubscribe anytime.'], 'footer'],
            // General
            ['general.siteName', ['value' => 'Go2Digital'], 'general'],
            ['general.siteUrl', ['value' => 'https://go2digital.hr'], 'general'],
            ['general.contactEmail', ['value' => 'info@go2digital.hr'], 'general'],
            ['general.copyrightHolder', ['value' => 'Go2Digital d.o.o.'], 'general'],
            ['general.defaultLocale', ['value' => 'hr'], 'general'],
            ['general.supportedLocales', ['value' => 'hr,en'], 'general'],
            // Integrations
            ['integrations.anthropicApiKey', ['value' => ''], 'integrations'],
            ['integrations.gtmId', ['value' => ''], 'integrations'],
            ['integrations.gaId', ['value' => ''], 'integrations'],
            ['integrations.fbPixelId', ['value' => ''], 'integrations'],
            ['integrations.hotjarId', ['value' => ''], 'integrations'],
            ['integrations.recaptchaSiteKey', ['value' => ''], 'integrations'],
            ['integrations.recaptchaSecretKey', ['value' => ''], 'integrations'],
            ['integrations.mapboxAccessToken', ['value' => ''], 'integrations'],
            // SEO global defaults
            ['seo.siteName', ['value' => 'Go2Digital'], 'seo'],
            ['seo.titleSeparator', ['value' => '|'], 'seo'],
            ['seo.defaultDescription', ['hr' => 'Go2Digital - Digitalna agencija za DOOH oglašavanje', 'en' => 'Go2Digital - Digital agency for DOOH advertising'], 'seo'],
            ['seo.defaultOgImage', ['value' => ''], 'seo'],
            ['seo.twitterHandle', ['value' => '@go2digital'], 'seo'],
            ['seo.robotsTxt', ['value' => "User-agent: *\nAllow: /\nSitemap: https://go2digital.hr/sitemap.xml"], 'seo'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            $setting = new Setting();
            $setting->setKey($key);
            $setting->setValue($value);
            $setting->setGroup($group);
            $manager->persist($setting);
        }
    }

    // ─── PRIVACY POLICY PAGE ─────────────────────────────────

    private function loadPrivacyPolicyPage(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setSlug('privacy-policy');
        $page->setStatus(ContentStatus::Published);
        $this->addTranslation($page, new PageTranslation(), 'hr', ['title' => 'Politika privatnosti', 'body' => 'Sadržaj politike privatnosti uskoro.']);
        $this->addTranslation($page, new PageTranslation(), 'en', ['title' => 'Privacy Policy', 'body' => 'Privacy policy content coming soon.']);
        $manager->persist($page);
    }

    // ─── HELPER ──────────────────────────────────────────────

    private function addTranslation(object $entity, object $translation, string $locale, array $fields): void
    {
        $translation->setLocale($locale);
        foreach ($fields as $field => $value) {
            $setter = 'set'.ucfirst($field);
            if (method_exists($translation, $setter)) {
                $translation->$setter($value);
            }
        }
        $entity->addTranslation($translation);
    }
}
