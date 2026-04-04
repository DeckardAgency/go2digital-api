<?php

declare(strict_types=1);

namespace App\Tests\Api;

class HomepageTest extends AbstractApiTestCase
{
    public function testListPanels(): void
    {
        $data = $this->apiGet('/api/homepage_panels', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(4, $data);
        self::assertSame('2.5M', $data[0]['statValue']);
        self::assertSame('Reach', $data[0]['tag']);
    }

    public function testListWhyCards(): void
    {
        $data = $this->apiGet('/api/homepage_why_cards', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(3, $data);
        self::assertSame('Measurable Impact', $data[0]['title']);
        self::assertIsArray($data[0]['dotPattern']);
    }

    public function testListTrackingFeatures(): void
    {
        $data = $this->apiGet('/api/homepage_tracking_features', ['Accept-Language' => 'hr']);
        $this->assertResponseStatusCode(200);
        self::assertCount(5, $data);
        self::assertSame('Prikazi u stvarnom vremenu', $data[0]['title']);
    }

    public function testListProducts(): void
    {
        $data = $this->apiGet('/api/homepage_products', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(2, $data);
    }

    public function testFilterProductByType(): void
    {
        $data = $this->apiGet('/api/homepage_products?productType=display', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(1, $data);
        self::assertSame("Digital\nscreens", $data[0]['title']);
    }

    public function testListProductFeatures(): void
    {
        $data = $this->apiGet('/api/homepage_product_features', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(6, $data); // 4 display + 2 cube
    }

    public function testListFeaturedLabItems(): void
    {
        $data = $this->apiGet('/api/homepage_featured_lab_items', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(3, $data);
        self::assertSame('Interactive Billboard', $data[0]['title']);
        self::assertIsArray($data[0]['categories']);
    }

    public function testEsgPillars(): void
    {
        $data = $this->apiGet('/api/esg_pillars', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(3, $data);
        self::assertSame('Environment', $data[0]['title']);
    }

    public function testNavigationMainGroup(): void
    {
        $data = $this->apiGet('/api/navigation_items?group=main', ['Accept-Language' => 'hr']);
        $this->assertResponseStatusCode(200);
        self::assertCount(5, $data);
        self::assertSame('Početna', $data[0]['label']);
    }

    public function testContactInfo(): void
    {
        $data = $this->apiGet('/api/contact_infos');
        $this->assertResponseStatusCode(200);
        self::assertCount(3, $data);
    }

    public function testSocialLinks(): void
    {
        $data = $this->apiGet('/api/social_links');
        $this->assertResponseStatusCode(200);
        self::assertCount(4, $data);
    }

    public function testSettings(): void
    {
        $data = $this->apiGet('/api/settings');
        $this->assertResponseStatusCode(200);
        self::assertCount(10, $data);
    }
}
