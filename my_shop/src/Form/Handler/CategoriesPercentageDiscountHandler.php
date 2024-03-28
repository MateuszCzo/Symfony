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

        if (!$categories->count()) {
            return $discount;
        }

        $oldProducts = $discount->getProducts();

        foreach($oldProducts as $oldProduct) {
            $discount->removeProduct($oldProduct);
        }

        /**  @var ArrayCollection $products */
        $products = $this->productRepository->findAllByCategories($categories);

        foreach($products as $product) {
            $discount->addProduct($product);
        }

        $discount->setCriteria([]);

        return $discount;
    }
}