<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Tests\KernelTestCaseWithDatabase;

class ImageTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function image_can_be_created_in_database(): void
    {
        // Given
        $image = new Image();
        $image->setName('image_name');
        $image->setType('image_type');

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        // When
        $this->entityManager->persist($image);
        $this->entityManager->flush();

        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->findOneBy(['name' => 'image_name']);

        // Then
        $this->assertNotEquals(null, $imageRecord);
        $this->assertGreaterThan(0, $imageRecord->getId());
        $this->assertEquals('image_name', $imageRecord->getName());
        $this->assertEquals('image_type', $imageRecord->getType());
    }

    /** @test */
    public function image_for_category_can_not_be_deleted(): void
    {
        //todo
    }

    /** @test */
    public function image_for_product_can_not_be_deleted(): void
    {
        //todo
    }

    /** @test */
    public function image_for_manufacturer_can_not_be_deleted(): void
    {
        //todo
    }
}