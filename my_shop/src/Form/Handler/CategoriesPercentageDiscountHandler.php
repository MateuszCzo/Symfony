<?php

namespace App\Form\Handler;

use Symfony\Component\Form\FormInterface;
use App\Entity\Discount;
use Doctrine\Common\Collections\ArrayCollection;

class CategoriesPercentageDiscountHandler extends DiscountFormHandlerTypeParent implements DiscountFormHandlerInterface
{   
    public function handle(FormInterface $form, Discount $discount): Discount
    {
        /**  @var ArrayCollection $categories */
        $categories = $form->get('categories')->getData();

        if (!$categories->count()) return $discount;

        $discount = $this->removeDiscountProducts($discount);

        $criteria = [];

        foreach($categories as $category) $criteria['categoryIds'][] = $category->getId();

        $discount->setCriteria($criteria);

        return $discount;
    }
}