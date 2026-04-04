<?php

declare(strict_types=1);

namespace App\Tests\Api;

class AuthTest extends AbstractApiTestCase
{
    public function testLoginSuccess(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'admin@go2digital.hr', 'password' => 'admin123']));

        $this->assertResponseStatusCode(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $data);
        self::assertArrayHasKey('refreshToken', $data);
    }

    public function testLoginWrongPassword(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'admin@go2digital.hr', 'password' => 'wrong']));

        $this->assertResponseStatusCode(401);
    }

    public function testMeEndpoint(): void
    {
        $token = $this->getToken();

        $this->client->request('GET', '/api/auth/me', [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertResponseStatusCode(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertSame('admin@go2digital.hr', $data['email']);
        self::assertContains('ROLE_ADMIN', $data['roles']);
    }

    public function testMeWithoutToken(): void
    {
        $this->client->request('GET', '/api/auth/me');
        $this->assertResponseStatusCode(401);
    }

    public function testRefreshToken(): void
    {
        $this->client->request('POST', '/api/auth/login', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['username' => 'admin@go2digital.hr', 'password' => 'admin123']));

        $loginData = json_decode($this->client->getResponse()->getContent(), true);

        $this->client->request('POST', '/api/auth/refresh', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode(['refreshToken' => $loginData['refreshToken']]));

        $this->assertResponseStatusCode(200);
        $data = json_decode($this->client->getResponse()->getContent(), true);
        self::assertArrayHasKey('token', $data);
    }
}
