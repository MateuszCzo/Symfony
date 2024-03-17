<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class ImageTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function image_can_be_created_in_database(): void
    {
        // Given
        $image = DataProvider::getImage();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        // When
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($image->getId());

        // Then
        self::assertTestObject($image, $imageRecord);
    }

    /** @test */
    public function other_images_for_product_can_be_deleted()
    {
        // Given
        $otherImage = DataProvider::getConfiguredImage($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addOtherImage($otherImage);

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

    public static function assertTestObject(Image $imageReference, Image $imageToTest): void
    {
        self::assertNotEquals(null, $imageToTest);
        self::assertEquals($imageReference->getId(), $imageToTest->getId());
        self::assertEquals($imageReference->getName(), $imageToTest->getName());
        self::assertEquals($imageReference->getType(), $imageToTest->getType());
        self::assertEquals($imageReference->getProduct(), $imageToTest->getProduct());
    }
}