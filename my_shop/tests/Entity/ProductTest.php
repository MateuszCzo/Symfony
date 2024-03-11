<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    /** @var EntityMenagetInterface $entityManager */
    protected $entityManager;

    protected function setUp(): void
    {
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
    public function product_can_be_created_in_database()
    {
        // Given
        $product = new Product();
        $product->setName('test_product1');
        $product->setPrice(100.0);

        /** @var ServiceEntityRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        // When
        $this->entityManager->persist($product);
        
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->findOneBy(['name' => 'test_product1']);

        // Then
        $this->assertEquals('test_product1', $productRecord->getName());
        $this->assertEquals(100, $productRecord->getPrice());
    }
}