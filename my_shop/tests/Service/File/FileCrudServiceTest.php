<?php

namespace App\Test\Service\Image;

use App\Entity\Image;
use App\Service\File\FileCrudService;
use App\Tests\KernelTestCaseWithDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileCrudServiceTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function image_created_successfully(): void
    {
        // Given
        copy(__DIR__ . '/../../Fixtures/Images/test.png', __DIR__ . '/../../tmp/test.png');

        /** @var FileCrudService $fileCrudService */
        $fileCrudService = self::getContainer()->get(FileCrudService::class);

        $uploadedImage = new UploadedFile(__DIR__ . '/../../tmp/test.png', 'test.png', 'image/png', null, true);

        // When
        /** @var Image $image */
        $image = $fileCrudService->create($uploadedImage, '/uploads/tmp', new Image());

        // Then
        self::assertEquals('/uploads/tmp', $image->getPath());
        self::assertNotEquals('test', $image->getName());
        self::assertEquals('png', $image->getType());

        self::assertTrue(file_exists(__DIR__ . '/../../../public' . $image->getPath() . '/' . $image->getName() . '.' . $image->getType()));

        unlink(__DIR__ . '/../../../public' . $image->getPath() . '/' . $image->getName() . '.' . $image->getType());
    }

    /** @test */
    public function image_exists(): void
    {
        // Given
        $container = self::getContainer();

        /** @var FileCrudService $fileCrudService */
        $fileCrudService = $container->get(FileCrudService::class);

        $image = (new Image)
            ->setName('test')
            ->setType('png')
            ->setPath('/../tests/Fixtures/Images');

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    
        $imageExists = $fileCrudService->exists($image);

        // Then
        self::assertTrue($imageExists);
    }

    /** @test */
    public function image_does_not_exists(): void
    {
        // Given
        $container = self::getContainer();

        /** @var FileCrudService $fileCrudService */
        $fileCrudService = $container->get(FileCrudService::class);

        $image = (new Image)
            ->setName('test')
            ->setType('jpg')
            ->setPath('/../tests/Fixtures/Images');

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    
        $imageExists = $fileCrudService->exists($image);

        // Then
        self::assertFalse($imageExists);
    }

    /** @test */
    public function image_updated_successfully(): void
    {
        // Given
        copy(__DIR__ . '/../../Fixtures/Images/test.png', __DIR__ . '/../../../public/uploads/tmp/test.png');
        copy(__DIR__ . '/../../Fixtures/Images/test.png', __DIR__ . '/../../tmp/test2.png');

        $image = (new Image)
            ->setName('test')
            ->setType('png')
            ->setPath('/uploads/tmp');

        $uploadedImage = new UploadedFile(__DIR__ . '/../../tmp/test2.png', 'test2.png', 'png', null, true);

        /** @var FileCrudService $fileCrudService */
        $fileCrudService = self::getContainer()->get(FileCrudService::class);

        // When
        $updatedImage = $fileCrudService->update($image, $uploadedImage);

        // Then
        self::assertEquals('/uploads/tmp', $image->getPath());
        self::assertNotEquals('test', $image->getName());
        self::assertEquals('png', $image->getType());

        self::assertFalse(file_exists(__DIR__ . '/../../public/uploads/tmp/test.png'));
        self::assertTrue(file_exists(__DIR__ . '/../../../public' . $updatedImage->getPath() . '/' . $updatedImage->getName() . '.' . $updatedImage->getType()));

        unlink(__DIR__ . '/../../../public' . $updatedImage->getPath() . '/' . $updatedImage->getName() . '.' . $updatedImage->getType());
    }

    /** @test */
    public function image_is_not_changed_without_new_uploaded_image(): void
    {
        // Given
        copy(__DIR__ . '/../../Fixtures/Images/test.png', __DIR__ . '/../../../public/uploads/tmp/test.png');

        $image = (new Image)
            ->setName('test')
            ->setType('png')
            ->setPath('/uploads/tmp');

        /** @var FileCrudService $fileCrudService */
        $fileCrudService = self::getContainer()->get(FileCrudService::class);

        // When
        $updatedImage = $fileCrudService->update($image, null);

        // Then
        self::assertEquals('/uploads/tmp', $image->getPath());
        self::assertEquals('test', $image->getName());
        self::assertEquals('png', $image->getType());

        self::assertEquals($image,  $updatedImage);

        unlink(__DIR__ . '/../../../public' . $updatedImage->getPath() . '/' . $updatedImage->getName() . '.' . $updatedImage->getType());
    }
}