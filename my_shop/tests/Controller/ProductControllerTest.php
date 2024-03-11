<?php

namespace App\Tests\Controller;

use App\Tests\DatabasePrimer;
use App\Tests\WebTestCaseWithDatabase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ProductControllerTest extends WebTestCaseWithDatabase
{
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