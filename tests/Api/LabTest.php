<?php

declare(strict_types=1);

namespace App\Tests\Api;

class LabTest extends AbstractApiTestCase
{
    public function testListLabProjects(): void
    {
        $data = $this->apiGet('/api/lab_projects', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(6, $data);
    }

    public function testLabProjectHasCategories(): void
    {
        $data = $this->apiGet('/api/lab_projects?slug=ai-analytics-dashboard', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(1, $data);
        self::assertSame('AI-Powered Analytics Dashboard', $data[0]['title']);
        self::assertCount(2, $data[0]['categories']);
    }

    public function testFilterLabProjectByCategory(): void
    {
        $data = $this->apiGet('/api/lab_projects?categories.slug=mobile');
        $this->assertResponseStatusCode(200);
        self::assertCount(2, $data); // mobile-shop + fitness-app
    }

    public function testListLabCategories(): void
    {
        $data = $this->apiGet('/api/lab_categories', ['Accept-Language' => 'en']);
        $this->assertResponseStatusCode(200);
        self::assertCount(4, $data);
    }
}
