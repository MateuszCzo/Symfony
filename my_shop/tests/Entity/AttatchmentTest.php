<?php

namespace App\Tests\Entity;

use App\Entity\Attatchment;
use App\Entity\Product;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class AttatchmentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function attatchment_can_be_created_in_database(): void
    {
        // Given
        $attatchment = DataProvider::getAttatchment();

        /** @var AttatchmentRepository $attatchmentRepository */
        $attatchmentRepository = $this->entityManager->getRepository(Attatchment::class);

        // When
        $this->entityManager->persist($attatchment);
        $this->entityManager->flush();

        /** @var Attatchment $attatchmentRecord */
        $attatchmentRecord = $attatchmentRepository->find($attatchment->getId());

        // Then
        self::assertTestObject($attatchment, $attatchmentRecord);
    }

    /** @test */
    public function product_is_not_deleted_when_attatchment_is_deleted(): void
    {
        // Given
        $attatchment = DataProvider::getConfiguredAttatchment($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addAttatchment($attatchment);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // When
        $this->entityManager->remove($attatchment);
        $this->entityManager->flush();

        // Then
        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        self::assertNotEquals(null, $productRecord);
    }

    public static function assertTestObject(Attatchment $attatchmentReference, Attatchment $attatchmentToTest): void
    {
        self::assertNotEquals(null, $attatchmentToTest);
        self::assertEquals($attatchmentReference->getId(), $attatchmentToTest->getId());
        self::assertEquals($attatchmentReference->getName(), $attatchmentToTest->getName());
        self::assertEquals($attatchmentReference->getDescription(), $attatchmentToTest->getDescription());
        self::assertEquals($attatchmentReference->getFileName(), $attatchmentToTest->getFileName());
        self::assertEquals($attatchmentReference->getType(), $attatchmentToTest->getType());
    }

}
