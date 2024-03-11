<?php

namespace App\Tests\Controller;

use App\Tests\DatabasePrimer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductControllerTest extends WebTestCase
{
    /** @var EntityMenagetInterface $entityManager */
    private $entityManager;

    /** @var KernelBrowser $client */
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    /** @test */
    public function can_connect_to_product_controller()
    {
        // Given

        // When
        $crawler = $this->client->request('GET', '/product');

        // Then
        $this->assertResponseIsSuccessful();
    }
}