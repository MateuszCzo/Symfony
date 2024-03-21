<?php

namespace App\Test\Form\Validator;

use App\Form\Validator\CategoryParentValidator;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class CategoryParentCalidatorTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function null_parent_id_validation(): void
    {
        // Given
        $category = DataProvider::getConfiguredCategory($this->entityManager);

        /** @var CategoryParentValidator $categoryParentValidator */
        $categoryParentValidator = self::getContainer()->get(CategoryParentValidator::class);

        // When
        $idValid = $categoryParentValidator->validate($category);

        // Then
        self::assertTrue($idValid);
    }

    /** @test */
    public function loop_category_validation(): void
    {
        // Given
        $categoryParent = DataProvider::getConfiguredCategory($this->entityManager);

        $categoryChild = DataProvider::getConfiguredCategory($this->entityManager)
            ->setParent($categoryParent);

        $this->entityManager->persist($categoryChild);
        $this->entityManager->flush();

        $categoryParent->setParent($categoryChild);

        /** @var CategoryParentValidator $categoryParentValidator */
        $categoryParentValidator = self::getContainer()->get(CategoryParentValidator::class);

        // When
        $idValid = $categoryParentValidator->validate($categoryParent);

        // Then
        self::assertFalse($idValid);
    }

    /** @test */
    public function parent_id_validation(): void
    {
        // Given
        $categoryParent = DataProvider::getConfiguredCategory($this->entityManager);

        $categoryChild = DataProvider::getConfiguredCategory($this->entityManager)
            ->setParent($categoryParent);

        /** @var CategoryParentValidator $categoryParentValidator */
        $categoryParentValidator = self::getContainer()->get(CategoryParentValidator::class);

        // When
        $idValid = $categoryParentValidator->validate($categoryChild);

        // Then
        self::assertTrue($idValid);
    }
}