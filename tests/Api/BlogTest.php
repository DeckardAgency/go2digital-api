<?php

declare(strict_types=1);

namespace App\Tests\Api;

class BlogTest extends AbstractApiTestCase
{
    public function testListBlogPostsPublic(): void
    {
        $data = $this->apiGet('/api/blog_posts');
        $this->assertResponseStatusCode(200);
        self::assertGreaterThanOrEqual(12, count($data));
    }

    public function testBlogPostTranslationHr(): void
    {
        $data = $this->apiGet('/api/blog_posts?itemsPerPage=1', ['Accept-Language' => 'hr']);
        $this->assertResponseStatusCode(200);
        self::assertNotEmpty($data);
        $first = $data[0];
        self::assertArrayHasKey('slug', $first);
        // Translations should be merged by normalizer
        self::assertArrayHasKey('locale', $first);
        self::assertSame('hr', $first['locale']);
    }

    public function testBlogPostTranslationEn(): void
    {
        $data = $this->apiGet('/api/blog_posts?itemsPerPage=1', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertNotEmpty($data);
        $first = $data[0];
        self::assertArrayHasKey('locale', $first);
        self::assertSame('en', $first['locale']);
    }

    public function testBlogPostHasTranslatedTitle(): void
    {
        $data = $this->apiGet('/api/blog_posts?slug=future-digital-marketing-2025', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(1, $data);
        // Title should be English
        self::assertArrayHasKey('title', $data[0]);
        self::assertSame('The Future of Digital Marketing in 2025', $data[0]['title']);
    }

    public function testBlogPostIncludeTranslations(): void
    {
        $token = $this->getToken();
        $data = $this->apiGet('/api/blog_posts?includeTranslations=true&itemsPerPage=1', [
            'Authorization' => 'Bearer '.$token,
        ]);
        $this->assertResponseStatusCode(200);
        self::assertNotEmpty($data);
        $first = $data[0];
        self::assertArrayHasKey('translations', $first);
    }

    public function testFilterBlogPostsByCategory(): void
    {
        $data = $this->apiGet('/api/blog_posts?category.slug=marketing');
        $this->assertResponseStatusCode(200);
        self::assertGreaterThanOrEqual(4, count($data));
    }

    public function testFilterBlogPostsByFeatured(): void
    {
        $data = $this->apiGet('/api/blog_posts?featured=true');
        $this->assertResponseStatusCode(200);
        // No posts are featured in fixtures
        self::assertCount(0, $data);
    }

    public function testListBlogCategories(): void
    {
        $data = $this->apiGet('/api/blog_categories', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(4, $data);
        $slugs = array_column($data, 'slug');
        self::assertContains('marketing', $slugs);
        self::assertContains('design', $slugs);
        self::assertContains('technology', $slugs);
        self::assertContains('news', $slugs);
    }

    public function testCreateBlogPostRequiresAuth(): void
    {
        $this->client->request('POST', '/api/blog_posts', [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
        ], json_encode(['slug' => 'test']));

        $this->assertResponseStatusCode(401);
    }

    public function testCreateBlogPostWithAuth(): void
    {
        $token = $this->getToken();
        $data = $this->apiPost('/api/blog_posts', [
            'slug' => 'test-post-'.uniqid(),
            'date' => '2025-06-01',
            'author' => 'Test',
            'status' => 'draft',
        ], $token);

        $this->assertResponseStatusCode(201);
        self::assertArrayHasKey('id', $data);
    }
}
