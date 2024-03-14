<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Image;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class CategoryTest extends KernelTestCaseWithDatabase
{
    public function category_can_not_be_created_without_image(): void
    {
        // Given
        $category = self::getTestObject();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /** @test */
    public function category_can_be_created_in_database(): void
    {
        // Given
        $category = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        /** @var Category $categoryRecord */
        $categoryRecord = $categoryRepository->find($category->getId());

        // Then
        self::assertTestObject($categoryRecord);
        ImageTest::assertTestObject($categoryRecord->getImage());
    }

    /** @test */
    public function child_category_can_access_parent_category(): void
    {
        // Given
        $categoryParent = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $categoryChild = self::getTestObject()
            ->setParent($categoryParent)
            ->setImage(ImageTest::getTestObject());

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $this->entityManager->persist($categoryParent);
        $this->entityManager->persist($categoryChild);
        $this->entityManager->flush();

        /** @var Category $categoryChildRecord */
        $categoryChildRecord = $categoryRepository->find($categoryChild->getId());

        // When
        $categoryParentRecord = $categoryChildRecord->getParent();

        // Then
        self::assertEquals($categoryParent->getId(), $categoryParentRecord->getId());
    }

    /** @test */
    public function parent_category_can_access_children_categories(): void
    {
        // Given
        $categoryChild = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $categoryParent = self::getTestObject()
            ->addChild($categoryChild)
            ->setImage(ImageTest::getTestObject());

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $this->entityManager->persist($categoryChild);
        $this->entityManager->persist($categoryParent);
        $this->entityManager->flush();

        /** @var Category $categoryParentRecord */
        $categoryParentRecord = $categoryRepository->find($categoryParent->getId());

        // When
        $categoryChildrenCollection = $categoryParentRecord->getChildren();

        // Then
        self::assertEquals(1, $categoryChildrenCollection->count());

        $categoryChildRecord = $categoryChildrenCollection->first();

        self::assertEquals($categoryChild->getId(), $categoryChildRecord->getId());
    }

    /** @test */
    public function image_is_deleted_when_category_is_deleted(): void
    {
        // Given
        $image = ImageTest::getTestObject();
        $category = self::getTestObject()
            ->setImage($image);

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $imageId = $image->getId();
        
        // When
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        // Then
        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($imageId);

        self::assertEquals(null, $imageRecord);
    }

    // /** @test */
    // public function category_can_not_be_deleted_while_in_product(): void
    // {
    //     // Given
    //     $category = self::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $manufacturer = ManufacturerTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $product = ProductTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject())
    //         ->setCategory($category)
    //         ->setManufacturer($manufacturer);

    //     $this->entityManager->persist($category);
    //     $this->entityManager->persist($manufacturer);
    //     $this->entityManager->persist($product);
    //     $this->entityManager->flush();

    //     // Expect
    //    self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($category);
    //     $this->entityManager->flush();
    // }

    /** @test */
    public function subcategory_can_be_deleted_while_in_product(): void
    {
        // Given
        $category = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $subcategory = self::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setCategory($category)
            ->setImage(ImageTest::getTestObject())
            ->addSubcategory($subcategory)
            ->setManufacturer($manufacturer);

        $this->entityManager->persist($category);
        $this->entityManager->persist($subcategory);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $subcategoryId = $subcategory->getId();

        // When
        $this->entityManager->remove($subcategory);
        $this->entityManager->flush();

        // Then
        /** @var Category $subcategoryRecord */
        $subcategoryRecord = $categoryRepository->find($subcategoryId);
        
        self::assertEquals(null, $subcategoryRecord);
    }

    public static function getTestObject(): Category
    {
        return (new Category())
            ->setName('category_name')
            ->setDescription('category_description');
    }

    public static function assertTestObject(Category $category): void
    {
        self::assertNotEquals(null, $category);
        self::assertGreaterThan(0, $category->getId());
        self::assertEquals('category_name', $category->getName());
        self::assertEquals('category_description', $category->getDescription());
    }
}