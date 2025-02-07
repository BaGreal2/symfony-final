<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private ?string $testToken = null;

    public function testRegisterAndLogin()
    {
        $client = static::createClient();

        // 1. Register a user
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $this->assertResponseStatusCodeSame(201);  // User should be created successfully
        $this->assertJson($client->getResponse()->getContent());

        // 2. Log in as the user
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        // 3. Get and store the token
        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        // 4. Assert that a token was received
        $this->assertNotNull($this->testToken, 'No token received in login response');

        echo 'Test Token: ' . $this->testToken . '\n';
    }


    public function testUserList()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('GET', '/api/users');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $response);
    }

    public function testCreateUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser1@example.com',
            'password' => 'password'
        ]));

        $this->assertResponseStatusCodeSame(201);
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('testuser1@example.com', $response['data']['email']);
    }

    public function testShowUser()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('GET', '/api/users/1');

        $this->assertResponseIsSuccessful();
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals(1, $response['data']['id']);
    }

    public function testCreateUserInvalidData()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('POST', '/api/users', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'invalidmail',
            'password' => ''
        ]));

        $this->assertResponseStatusCodeSame(500);
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
    }

    public function testAdminDashboard()
    {
        $client = static::createClient();
        $client->request('GET', '/api/admin/dashboard');

        // Since authentication is ignored for now, this will fail for an unauthenticated user
        $this->assertResponseStatusCodeSame(401); // Unauthorized
    }

    public function testShowUserNotFound()
    {
        $client = static::createClient();
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testuser2@example.com',
            'password' => 'testpassword'
        ]));

        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $this->testToken = $responseContent['token'] ?? null;

        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $this->testToken);
        $client->request('GET', '/api/users/999');  // Assuming user with ID 999 does not exist

        $this->assertResponseStatusCodeSame(404);  // Not Found
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('User not found', $response['message']);
    }

    public function testRegisterDuplicateEmail()
    {
        $client = static::createClient();
        
        // First user registration
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testduplicate@example.com',
            'password' => 'password'
        ]));
        
        $this->assertResponseStatusCodeSame(201);  // Successful registration
        
        // Try registering again with the same email
        $client->request('POST', '/api/register', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'testduplicate@example.com',
            'password' => 'password'
        ]));

        $this->assertResponseStatusCodeSame(400);  // Bad Request, email already exists
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $response);
        $this->assertEquals('Email already registered', $response['message']);
    }

    public function testAdminDashboardAuthenticated()
    {
        $client = static::createClient();
        
        // Login as admin (if you have a login system)
        // You should adapt this part to suit your login process, e.g., using JWT or cookies
        $client->request('POST', '/api/login', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'email' => 'admin@example.com',
            'password' => 'admin'
        ]));


        $responseContent = json_decode($client->getResponse()->getContent(), true);
        $token = $responseContent['token'] ?? null;

        $this->assertNotNull($token, 'No token received in login response');

        // Assuming the response contains an authentication token or cookie, set it as a header
        $client->setServerParameter('HTTP_AUTHORIZATION', 'Bearer ' . $token);
        
        // Now try accessing the admin dashboard
        $client->request('GET', '/api/admin/dashboard');

        $this->assertResponseIsSuccessful();  // 200 OK
        $this->assertJson($client->getResponse()->getContent());
        $response = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('data', $response);
        $this->assertEquals('Welcome, admin!', $response['data']['message']);
    }
}

