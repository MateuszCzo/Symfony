<?php

namespace App\Tests\Entity;

use App\Entity\Image;
use App\Entity\Manufacturer;
use App\Tests\KernelTestCaseWithDatabase;

class ManufacturerTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function manufacturer_can_not_be_created_without_image(): void
    {
        //todo
    }

    /** @test */
    public function manufacturer_can_be_created_in_database(): void
    {
        // Given
        $image = new Image();
        $image->setName('image_name');
        $image->setType('image_type');

        $manufacturer = new Manufacturer();
        $manufacturer->setImage($image);
        $manufacturer->setName('manufacturer_name');
        $manufacturer->setDescription('manufacturer_description');

        /** @var ManufacturerRepository $manufacturerRepository */
        $manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();
        
        $imageId = $manufacturer->getImage()->getId();

        /** @var Manufacturer $manufacturerRecord */
        $manufacturerRecord = $manufacturerRepository->findOneBy(['name' => 'manufacturer_name']);

        // Then
        $this->assertNotEquals(null, $manufacturerRecord);
        $this->assertGreaterThan(0, $manufacturerRecord->getId());
        $this->assertEquals('manufacturer_name', $manufacturerRecord->getName());
        $this->assertEquals('manufacturer_description', $manufacturerRecord->getDescription());

        $imageRecord = $manufacturerRecord->getImage();

        $this->assertGreaterThan(0, $imageId);
        $this->assertEquals($imageId, $imageRecord->getId());
        $this->assertEquals('image_name', $imageRecord->getName());
        $this->assertEquals('image_type', $imageRecord->getType());
    }

    /** @test */
    public function product_is_not_deleted_when_manufacturer_is_deleted(): void
    {
        //todo
    }
}