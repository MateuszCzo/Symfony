<?php

namespace App\Form\Validator;

use App\Entity\Category;

class CategoryParentValidator
{
    private const MAX_SEARCH = 10;

    public function validate(Category $category): bool
    {
        $parentCategory = $category->getParent();

        if (!$parentCategory) return true;

        return $this->validateParent($parentCategory, $category->getId());
    }

    public function validateParent(Category $category, int $searchedId, $iteration = 1): bool
    {
        if (self::MAX_SEARCH == $iteration) return false;

        if ($category->getId() == $searchedId) return false;

        $parentCategory = $category->getParent();

        if (!$parentCategory) return true;

        return $this->validateParent($parentCategory, $searchedId, $iteration++);
    }
}