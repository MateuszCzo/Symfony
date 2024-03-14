<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class ManufacturerTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function manufacturer_can_not_be_created_without_image(): void
    {
        // Given
        $manufacturer = self::getTestObject();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();
    }

    /** @test */
    public function manufacturer_can_be_created_in_database(): void
    {
        // Given
        $image = ImageTest::getTestObject();

        $manufacturer = self::getTestObject()
            ->setImage($image);

        /** @var ManufacturerRepository $manufacturerRepository */
        $manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        /** @var Manufacturer $manufacturerRecord */
        $manufacturerRecord = $manufacturerRepository->find($manufacturer->getId());

        // Then
        self::asserttestObject($manufacturerRecord);
        ImageTest::assertTestObject($manufacturerRecord->getImage());
    }

    /** @test */
    public function product_is_not_deleted_when_manufacturer_is_deleted(): void
    {
        // Given
        $manufacturer = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category)
            ->setManufacturer($manufacturer);

        $this->entityManager->persist($category);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        // When
        $this->entityManager->remove($manufacturer);
        $this->entityManager->flush();
        
        // Then
        $productRecord = $productRepository->find($product->getId());

        self::assertNotEquals(null, $productRecord);
    }

    public static function getTestObject(): Manufacturer
    {
        return (new Manufacturer())
            ->setName('manufacturer_name')
            ->setDescription('manufacturer_description');
    }

    public static function asserttestObject(Manufacturer $manufacturer): void
    {
        self::assertNotEquals(null, $manufacturer);
        self::assertGreaterThan(0, $manufacturer->getId());
        self::assertEquals('manufacturer_name', $manufacturer->getName());
        self::assertEquals('manufacturer_description', $manufacturer->getDescription());
    }
}