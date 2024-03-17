<?php

namespace App\Tests\Entity;

use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class ManufacturerTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function manufacturer_can_not_be_created_without_image(): void
    {
        // Given
        $manufacturer = DataProvider::getManufacturer();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: manufacturer.image_id');

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();
    }

    /** @test */
    public function manufacturer_can_be_created_in_database(): void
    {
        // Given
        $image = DataProvider::getConfiguredImage($this->entityManager);

        $manufacturer = DataProvider::getManufacturer()
            ->setImage($image);

        /** @var ManufacturerRepository $manufacturerRepository */
        $manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        /** @var Manufacturer $manufacturerRecord */
        $manufacturerRecord = $manufacturerRepository->find($manufacturer->getId());

        // Then
        self::asserttestObject($manufacturer, $manufacturerRecord);
    }

    /** @test */
    public function product_is_not_deleted_when_manufacturer_is_deleted(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $manufacturer = $product->getManufacturer();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        // When
        $this->entityManager->remove($manufacturer);
        $this->entityManager->flush();
        
        // Then
        $productRecord = $productRepository->find($product->getId());

        self::assertNotEquals(null, $productRecord);
    }

    public static function asserttestObject(Manufacturer $manufacturerReference, Manufacturer $manufacturerToTest): void
    {
        self::assertNotEquals(null, $manufacturerToTest);
        self::assertEquals($manufacturerReference->getId(), $manufacturerToTest->getId());
        self::assertEquals($manufacturerReference->getImage(), $manufacturerToTest->getImage());
        self::assertEquals($manufacturerReference->getName(), $manufacturerToTest->getName());
        self::assertEquals($manufacturerReference->getDescription(), $manufacturerToTest->getDescription());
        self::assertEquals($manufacturerReference->getProducts(), $manufacturerToTest->getProducts());
    }
}