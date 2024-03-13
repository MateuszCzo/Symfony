<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Image;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class CategoryTest extends KernelTestCaseWithDatabase
{
    public function category_can_not_be_created_without_image(): void
    {
        // Given
        $category = new Category();
        $category->setName('category_name');
        $category->setDescription('category_description');

        // Expect
        $this->expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    /** @test */
    public function category_can_be_created_in_database(): void
    {
        // Given
        $image = new Image();
        $image->setName('image_name');
        $image->setType('image_type');

        $category = new Category();
        $category->setImage($image);
        $category->setName('category_name');
        $category->setDescription('category_description');

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $imageId = $category->getImage()->getId();

        /** @var Category $categoryRecord */
        $categoryRecord = $categoryRepository->findOneBy(['name' => 'category_name']);

        // Then
        $this->assertNotEquals(null, $categoryRecord);
        $this->assertGreaterThan(0, $categoryRecord->getId());
        $this->assertEquals('category_name', $categoryRecord->getName());
        $this->assertEquals('category_description', $categoryRecord->getDescription());
        
        $imageRecord = $categoryRecord->getImage();

        $this->assertGreaterThan(0, $imageId);
        $this->assertEquals($imageId, $imageRecord->getId());
        $this->assertEquals('image_name', $imageRecord->getName());
        $this->assertEquals('image_type', $imageRecord->getType());
    }

    /** @test */
    public function child_category_can_access_parent_category(): void
    {
        // Given
        $imageParent = new Image();
        $imageParent->setName('image_parent_name');
        $imageParent->setType('image_parent_type');

        $categoryParent = new Category();
        $categoryParent->setImage($imageParent);
        $categoryParent->setName('category_parent_name');
        $categoryParent->setDescription('category_parent_description');

        $imageChild = new Image();
        $imageChild->setName('image_child_name');
        $imageChild->setType('image_child_type');

        $categoryChild = new Category();
        $categoryChild->setParent($categoryParent);
        $categoryChild->setImage($imageChild);
        $categoryChild->setName('category_child_name');
        $categoryChild->setDescription('category_child_description');

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // When
        $this->entityManager->persist($categoryParent);
        $this->entityManager->persist($categoryChild);
        $this->entityManager->flush();

        
        $categoryIdParent = $categoryParent->getId();

        /** @var Category $categoryChildRecord */
        $categoryChildRecord = $categoryRepository->findOneBy(['name' => 'category_child_name']);

        // Then
        $categoryParentRecord = $categoryChildRecord->getParent();

        $this->assertEquals($categoryIdParent, $categoryParentRecord->getId());
    }

    /** @test */
    public function parent_category_can_access_children_categories(): void
    {
        // Given
        $imageChild = new Image();
        $imageChild->setName('image_child_name');
        $imageChild->setType('image_child_type');

        $categoryChild = new Category();
        $categoryChild->setImage($imageChild);
        $categoryChild->setName('category_child_name');
        $categoryChild->setDescription('category_child_description');

        $imageParent = new Image();
        $imageParent->setName('image_parent_name');
        $imageParent->setType('image_parent_type');

        $categoryParent = new Category();
        $categoryParent->addChild($categoryChild);
        $categoryParent->setImage($imageParent);
        $categoryParent->setName('category_parent_name');
        $categoryParent->setDescription('category_parent_description');

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // When
        $this->entityManager->persist($categoryChild);
        $this->entityManager->persist($categoryParent);
        $this->entityManager->flush();

        $categoryIdChild = $categoryChild->getId();

        /** @var Category $categoryParentRecord */
        $categoryParentRecord = $categoryRepository->findOneBy(['name' => 'category_parent_name']);

        // Then
        $categoryChildrenCollection = $categoryParentRecord->getChildren();

        $this->assertEquals(1, $categoryChildrenCollection->count());

        $categoryChildRecord = $categoryChildrenCollection->first();

        $this->assertEquals($categoryIdChild, $categoryChildRecord->getId());
    }

    /** @test */
    public function image_is_deleted_when_category_is_deleted(): void
    {
        // Given
        $image = new Image();
        $image->setName('image_name');
        $image->setType('image_type');

        $category = new Category();
        $category->setImage($image);
        $category->setName('category_name');
        $category->setDescription('category_description');

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $imageId = $category->getImage()->getId();

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        /** @var Image $imageRecord */
        $imageRecord = $imageRepository->find($imageId);

        // Then
        $this->assertEquals(null, $imageRecord);
    }

    /** @test */
    public function product_is_not_deleted_when_category_is_deleted(): void
    {
        // todo
    }
}