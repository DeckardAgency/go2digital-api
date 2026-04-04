<?php

declare(strict_types=1);

namespace App\Tests\Api;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    protected function getToken(string $email = 'admin@go2digital.hr', string $password = 'admin123'): string
    {
        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => $email, 'password' => $password]));

        $data = json_decode($this->client->getResponse()->getContent(), true);

        return $data['token'];
    }

    protected function apiGet(string $uri, array $headers = []): array
    {
        $serverHeaders = ['HTTP_ACCEPT' => 'application/json'];
        foreach ($headers as $key => $value) {
            $serverHeaders['HTTP_'.str_replace('-', '_', strtoupper($key))] = $value;
        }

        $this->client->request('GET', $uri, [], [], $serverHeaders);

        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    protected function apiPost(string $uri, array $data, string $token): array
    {
        $this->client->request('POST', $uri, [], [], [
            'CONTENT_TYPE' => 'application/ld+json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode($data));

        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    protected function apiPatch(string $uri, array $data, string $token): array
    {
        $this->client->request('PATCH', $uri, [], [], [
            'CONTENT_TYPE' => 'application/merge-patch+json',
            'HTTP_ACCEPT' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ], json_encode($data));

        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }

    protected function apiDelete(string $uri, string $token): void
    {
        $this->client->request('DELETE', $uri, [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
    }

    protected function assertResponseStatusCode(int $expected): void
    {
        self::assertSame($expected, $this->client->getResponse()->getStatusCode());
    }
}
