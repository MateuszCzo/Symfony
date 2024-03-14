<?php

namespace App\Tests\Entity;

use App\Entity\Attatchment;
use App\Entity\Product;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;

class AttatchmentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function attatchment_can_be_created_in_database(): void
    {
        // Given
        $attatchment = self::getTestObject();

        /** @var AttatchmentRepository $attatchmentRepository */
        $attatchmentRepository = $this->entityManager->getRepository(Attatchment::class);

        // When
        $this->entityManager->persist($attatchment);
        $this->entityManager->flush();

        /** @var Attatchment $attatchmentRecord */
        $attatchmentRecord = $attatchmentRepository->find($attatchment->getId());

        // Then
        self::assertTestObject($attatchmentRecord);
    }

    /** @test */
    public function product_is_not_deleted_when_attatchment_is_deleted(): void
    {
        // Given
        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $attatchment = self::getTestObject();

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setCategory($category)
            ->setImage(ImageTest::getTestObject())
            ->addAttatchment($attatchment)
            ->setManufacturer($manufacturer);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        $this->entityManager->persist($attatchment);
        $this->entityManager->persist($category);
        $this->entityManager->persist($manufacturer);
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

    public static function getTestObject(): Attatchment
    {
        return (new Attatchment())
            ->setName('attatchment_name')
            ->setDescription('attatchemnt_description')
            ->setFileName('attatchment_file_name')
            ->setType('attatchemnt_type');
    }

    public static function assertTestObject($attatchment): void
    {
        self::assertNotEquals(null, $attatchment);
        self::assertGreaterThan(0, $attatchment->getId());
        self::assertEquals('attatchment_name', $attatchment->getName());
        self::assertEquals('attatchemnt_description', $attatchment->getDescription());
        self::assertEquals('attatchment_file_name', $attatchment->getFileName());
        self::assertEquals('attatchemnt_type', $attatchment->getType());
    }

}
