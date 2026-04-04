<?php

declare(strict_types=1);

namespace App\Tests\Api;

class SingletonTest extends AbstractApiTestCase
{
    public function testGetHomepageHeroHr(): void
    {
        $data = $this->apiGet('/api/singletons/homepage-hero', ['Accept-Language' => 'hr']);
        $this->assertResponseStatusCode(200);
        self::assertSame('Brendovi', $data['titleLine1']);
        self::assertSame('hr', $data['locale']);
    }

    public function testGetHomepageHeroEn(): void
    {
        $data = $this->apiGet('/api/singletons/homepage-hero', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertSame('Brands', $data['titleLine1']);
        self::assertSame('en', $data['locale']);
    }

    public function testGetEsgPageContent(): void
    {
        $data = $this->apiGet('/api/singletons/esg-page-content', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertSame('Environmental, Social & Governance', $data['heroLabel']);
    }

    public function testGetBlogPageContent(): void
    {
        $data = $this->apiGet('/api/singletons/blog-page-content', ['Accept-Language' => 'hr']);
        $this->assertResponseStatusCode(200);
        self::assertSame('Blog', $data['title']);
        self::assertSame('Sve', $data['filterAllLabel']);
    }

    public function testGetContactPageContent(): void
    {
        $data = $this->apiGet('/api/singletons/contact-page-content', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertSame('Contact', $data['pageTitle']);
        self::assertSame('9 AM to 5 PM', $data['batteryLine2']);
    }

    public function testUpdateSingletonRequiresAuth(): void
    {
        $this->client->request('PUT', '/api/singletons/homepage-hero', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['translations' => ['hr' => ['titleLine1' => 'Test']]]));

        $this->assertResponseStatusCode(401);
    }

    public function testUnknownSingletonType(): void
    {
        $this->apiGet('/api/singletons/nonexistent');
        $this->assertResponseStatusCode(404);
    }
}
