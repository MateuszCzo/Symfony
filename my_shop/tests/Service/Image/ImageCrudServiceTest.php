<?php

namespace App\Test\Service\Image;

use App\Entity\Image;
use App\Service\Image\ImageCrudService;
use App\Tests\KernelTestCaseWithDatabase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCrudServiceTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function image_created_successfully(): void
    {
        // Given
        copy(__DIR__ . '/../../Fixtures/Images/test.png', __DIR__ . '/../../tmp/test.png');

        /** @var ImageCrudService $imageCrudService */
        $imageCrudService = self::getContainer()->get(ImageCrudService::class);

        $uploadedImage = new UploadedFile(__DIR__ . '/../../tmp/test.png', 'test.png', 'image/png', null, true);

        // When
        /** @var Image $image */
        $image = $imageCrudService->create($uploadedImage, '/uploads/tmp');

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

        /** @var ImageCrudService $imageCrudService */
        $imageCrudService = $container->get(ImageCrudService::class);

        $image = (new Image)
            ->setName('test')
            ->setType('png')
            ->setPath('/../tests/Fixtures/Images');

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    
        $imageExists = $imageCrudService->exists($image);

        // Then
        self::assertTrue($imageExists);
    }

    /** @test */
    public function image_does_not_exists(): void
    {
        // Given
        $container = self::getContainer();

        /** @var ImageCrudService $imageCrudService */
        $imageCrudService = $container->get(ImageCrudService::class);

        $image = (new Image)
            ->setName('test')
            ->setType('jpg')
            ->setPath('/../tests/Fixtures/Images');

        $this->entityManager->persist($image);
        $this->entityManager->flush();
    
        $imageExists = $imageCrudService->exists($image);

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

        /** @var ImageCrudService $imageCrudService */
        $imageCrudService = self::getContainer()->get(ImageCrudService::class);

        // When
        $updatedImage = $imageCrudService->update($image, $uploadedImage);

        // Then
        self::assertEquals('/uploads/tmp', $image->getPath());
        self::assertNotEquals('test', $image->getName());
        self::assertEquals('png', $image->getType());

        self::assertFalse(file_exists(__DIR__ . '/../../public/uploads/tmp/test.png'));
        self::assertTrue(file_exists(__DIR__ . '/../../../public' . $image->getPath() . '/' . $image->getName() . '.' . $image->getType()));

        unlink(__DIR__ . '/../../../public' . $image->getPath() . '/' . $image->getName() . '.' . $image->getType());
    }
}