<?php
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\RawMinkContext;
use Symfony\Component\HttpKernel\KernelInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @BeforeScenario
 */
public function beginTransaction()
{
    $this->kernel->boot();
    $this->kernel->getContainer()->get('doctrine')->getManager()->beginTransaction();
}

/**
 * @AfterScenario
 */
public function rollbackTransaction()
{
    $em = $this->kernel->getContainer()->get('doctrine')->getManager();
    $em->rollback();
    $em->clear();
}

class FeatureContext extends RawMinkContext implements Context
{
    private KernelInterface $kernel;
    private ?string $jwtToken = null;

    // 1. Authentication Steps
    /**
     * @Given I am authenticated as :email with password :password
     */
    public function iAmAuthenticatedAsWithPassword($email, $password)
    {
        $this->visitPath('/api/login');
        $this->getSession()->getDriver()->setRequestHeader('Content-Type', 'application/json');
        $this->getSession()->getDriver()->request(
            'POST',
            $this->locatePath('/api/login'),
            [],
            [],
            [],
            json_encode(['email' => $email, 'password' => $password])
        );
        
        $response = json_decode($this->getSession()->getPage()->getContent(), true);
        $this->jwtToken = $response['token'] ?? null;
    }

    // 2. Request Steps
    /**
     * @When I send a :method request to :url with:
     */
    public function iSendARequestToWithBody($method, $url, \Behat\Gherkin\Node\PyStringNode $body)
    {
        $this->getSession()->getDriver()->setRequestHeader('Authorization', 'Bearer ' . $this->jwtToken);
        $this->getSession()->getDriver()->request(
            $method,
            $this->locatePath($url),
            [],
            [],
            [],
            $body->getRaw()
        );
    }

    // 3. Assertion Steps
    /**
     * @Then the response status code should be :code
     */
    public function assertResponseStatusCode($code)
    {
        $response = $this->getSession()->getDriver()->getStatusCode();
        \PHPUnit\Framework\Assert::assertEquals($code, $response);
    }

    /**
     * @Then the response should contain a valid JWT token
     */
    public function assertValidJwtToken()
    {
        $response = json_decode($this->getSession()->getPage()->getContent(), true);
        \PHPUnit\Framework\Assert::assertArrayHasKey('token', $response);
        \PHPUnit\Framework\Assert::assertNotEmpty($response['token']);
    }
}
