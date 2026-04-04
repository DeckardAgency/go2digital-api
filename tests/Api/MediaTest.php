<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class MediaTest extends AbstractApiTestCase
{
    public function testUploadRequiresAuth(): void
    {
        $this->client->request('POST', '/api/media/upload');
        $this->assertResponseStatusCode(401);
    }

    public function testUploadImage(): void
    {
        $token = $this->getToken();

        // Create a minimal valid PNG
        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        $img = imagecreatetruecolor(100, 100);
        imagepng($img, $tmpFile);
        imagedestroy($img);

        $file = new UploadedFile($tmpFile, 'test.png', 'image/png', null, true);

        $this->client->request('POST', '/api/media/upload', [
            'collection' => 'blog',
        ], [
            'file' => $file,
        ], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCode(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('blog', $data['collection']);
        self::assertSame('image/png', $data['mimeType']);
        self::assertSame(100, $data['width']);
        self::assertSame(100, $data['height']);
        self::assertArrayHasKey('thumbnails', $data);
    }

    public function testFileUploadAndDownload(): void
    {
        $token = $this->getToken();

        $tmpFile = tempnam(sys_get_temp_dir(), 'test_');
        file_put_contents($tmpFile, 'Test document content');

        $file = new UploadedFile($tmpFile, 'report.txt', 'text/plain', null, true);

        $this->client->request('POST', '/api/files/upload', [
            'category' => 'esg-report',
            'description' => 'Test report',
        ], [
            'file' => $file,
        ], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCode(201);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        $fileId = $data['id'];

        // Download — BinaryFileResponse needs special handling
        $this->client->request('GET', '/api/files/'.$fileId.'/download');
        $this->assertResponseStatusCode(200);
        $response = $this->client->getResponse();
        self::assertStringContainsString('attachment', $response->headers->get('Content-Disposition', ''));
    }
}
