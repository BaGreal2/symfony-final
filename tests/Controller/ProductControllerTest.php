<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{

    private ?string $testToken = null;

    public function testRegisterAndLogin()
    {
        $client = static::createClient();

        // 1. Register a user
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser@example.com',
            'password' => 'testpassword'
        ]));

        $this->assertResponseStatusCodeSame(201);  // User should be created successfully
        $this->assertJson($client->getResponse()->getContent());

        // 2. Log in as the user
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser@example.com',
            'password' => 'testpassword'
        ]));

        // 3. Get and store the token
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        // 4. Assert that a token was received
        $this->assertNotNull($this->testToken, 'No token received in login response');

        // Optionally, you can print out the token for debugging purposes
        echo 'Test Token: ' . $this->testToken . '\n';
    }

    public function testProductListing(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser@example.com',
            'password' => 'testpassword'
        ]));

        // 3. Get and store the token
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('GET', '/api/products');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());

        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $responseData);
    }
}
