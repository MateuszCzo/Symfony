<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Entity\Manufacturer;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class ImageTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function image_can_be_created_in_database(): void
    {
        // Given
        $image = self::getTestObject();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        // When
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($image->getId());

        // Then
        self::assertTestObject($imageRecord);
    }

    // /** @test */
    // public function image_can_not_be_deleted_while_in_category(): void
    // {
    //     // Given
    //     $image = self::getTestObject();
    //     $category = CategoryTest::getTestObject()
    //         ->setImage($image);

    //     $this->entityManager->persist($category);
    //     $this->entityManager->flush();

    //     // Expect
    //     self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($image);
    //     $this->entityManager->flush();
    // }

    // /** @test */
    // public function image_can_not_be_deleted_while_in_product(): void
    // {
    //     // Given
    //     $category = CategoryTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $manufacturer = ManufacturerTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $imageProduct = ImageTest::getTestObject();

    //     $product = ProductTest::getTestObject()
    //         ->setImage($imageProduct)
    //         ->setCategory($category);

    //     $this->entityManager->persist($category);
    //     $this->entityManager->persist($manufacturer);
    //     $this->entityManager->persist($product);
    //     $this->entityManager->flush();

    //     // Expect
    //     self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($imageProduct);
    //     $this->entityManager->flush();
    // }

    // /** @test */
    // public function image_can_not_be_deleted_while_in_manufacturer(): void
    // {
    //     // Given
    //     $image = self::getTestObject();
    //     $category = ManufacturerTest::getTestObject()
    //         ->setImage($image);

    //     $this->entityManager->persist($category);
    //     $this->entityManager->flush();

    //     // Expect
    //     self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($image);
    //     $this->entityManager->flush();
    // }

    /** @test */
    public function other_images_for_product_can_be_deleted()
    {
        // Given
        $otherImage = self::getTestObject();

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category)
            ->setManufacturer($manufacturer)
            ->addOtherImage($otherImage);

        $this->entityManager->persist($category);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $otherImageId = $otherImage->getId();

        // When
        $this->entityManager->remove($otherImage);
        $this->entityManager->flush();

        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($otherImageId);

        // Then
        self::assertEquals(null, $imageRecord);
    }
    
    public static function getTestObject(): Image
    {
        return (new Image())
            ->setName('image_name')
            ->setType('image_type');
    }

    public static function assertTestObject($image): void
    {
        self::assertNotEquals(null, $image);
        self::assertGreaterThan(0, $image->getId());
        self::assertEquals('image_name', $image->getName());
        self::assertEquals('image_type', $image->getType());
    }
}