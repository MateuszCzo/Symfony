<?php

namespace App\Tests;

use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCaseWithDatabase
{
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