<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Image;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class CategoryTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function category_can_not_be_created_without_image(): void
    {
        // Given
        $category = DataProvider::getCategory();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: category.image_id');

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /** @test */
    public function category_can_be_created_in_database(): void
    {
        // Given
        $image = DataProvider::getConfiguredImage($this->entityManager);

        $category = DataProvider::getCategory()
            ->setImage($image);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        /** @var Category $categoryRecord */
        $categoryRecord = $categoryRepository->find($category->getId());

        // Then
        self::assertTestObject($category, $categoryRecord);
    }

    /** @test */
    public function child_category_can_access_parent_category(): void
    {
        // Given
        $categoryParent = DataProvider::getConfiguredCategory($this->entityManager);

        $categoryChild = DataProvider::getConfiguredCategory($this->entityManager)
            ->setParent($categoryParent);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

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
        $categoryChild = DataProvider::getConfiguredCategory($this->entityManager);

        $categoryParent = DataProvider::getConfiguredCategory($this->entityManager)
            ->addChild($categoryChild);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

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
        $category = DataProvider::getConfiguredCategory($this->entityManager);
        
        $image = $category->getImage();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $imageId = $image->getId();
        
        // When
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        // Then
        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($imageId);

        self::assertEquals(null, $imageRecord);
    }

    /** @test */
    public function subcategory_can_be_deleted_while_in_product(): void
    {
        // Given
        $subcategory = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addSubcategory($subcategory);

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

    public static function assertTestObject(Category $categoryReference, Category $categoryToTest): void
    {
        self::assertNotEquals(null, $categoryToTest);
        self::assertEquals($categoryReference->getId(), $categoryToTest->getId());
        self::assertEquals($categoryReference->getName(), $categoryToTest->getName());
        self::assertEquals($categoryReference->getDescription(), $categoryToTest->getDescription());
        self::assertEquals($categoryReference->getImage(), $categoryToTest->getImage());
        self::assertEquals($categoryReference->getChildren(), $categoryToTest->getChildren());
        self::assertEquals($categoryReference->getParent(), $categoryToTest->getParent());
    }
}