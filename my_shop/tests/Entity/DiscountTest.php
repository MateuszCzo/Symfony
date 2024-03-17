<?php

namespace App\Tests\Entity;

use App\Entity\Discount;
use App\Entity\Product;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class DiscountTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function discount_can_be_created_in_database(): void
    {
        // Given
        $discount = DataProvider::getDiscount();

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        // When
        $this->entityManager->persist($discount);
        $this->entityManager->flush();

        /** @var Discount $discountRecord */
        $discountRecord = $discountRepository->find($discount->getId());

        // Then
        self::assertTestObject($discount, $discountRecord);
    }

    /** @test */
    public function discount_can_access_products(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $discount = DataProvider::getDiscount()
            ->addProduct($product);

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        $this->entityManager->persist($discount);
        $this->entityManager->flush();

        /** @var Discount $discountRecord */
        $discountRecord = $discountRepository->find($discount->getId());

        // When
        $productCollection = $discountRecord->getProducts();

        // Then
        self::assertEquals(1, $productCollection->count());

        /** @var Product $productRecord */
        $productRecord = $productCollection->first();

        self::assertEquals($product->getId(), $productRecord->getId());
    }

    /** @test */
    public function product_is_not_deleted_when_discount_is_delted(): void
    {
        // Given
        $discount = DataProvider::getConfiguredDiscount($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addDiscount($discount);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        $productId = $product->getId();

        // When
        $this->entityManager->remove($discount);
        $this->entityManager->flush();

        // When
        /** @var Product $productRecord */
        $productRecord = $productRepository->find($productId);

        self::assertNotEquals(null, $productRecord);

    }

    public static function assertTestObject(Discount $discountReference, Discount $discountToTest): void
    {
        self::assertNotEquals(null, $discountToTest);
        self::assertEquals($discountReference->getId(), $discountToTest->getId());
        self::assertEquals($discountReference->getName(), $discountToTest->getName());
        self::assertEquals($discountReference->getCriteria(), $discountToTest->getCriteria());
        self::assertEquals($discountReference->getValue(), $discountToTest->getValue());
        self::assertEquals($discountReference->getType(), $discountToTest->getType());
        self::assertEquals($discountReference->getProducts(), $discountToTest->getProducts());
    }
}