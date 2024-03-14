<?php

namespace App\Tests\Entity;

use App\Entity\Discount;
use App\Entity\Product;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;

class DiscountTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function discount_can_be_created_in_database(): void
    {
        // Given
        $discount = self::getTestObject();

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        // When
        $this->entityManager->persist($discount);
        $this->entityManager->flush();

        /** @var Discount $discountRecord */
        $discountRecord = $discountRepository->find($discount->getId());

        // Then
        self::assertTestObject($discountRecord);
    }

    /** @test */
    public function discount_can_access_products(): void
    {
        // Given
        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $discount = self::getTestObject()
            ->addProduct($product);

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        $this->entityManager->persist($category);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
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
        $discount = self::getTestObject();

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addDiscount($discount);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        $this->entityManager->persist($discount);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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

    public static function getTestObject(): Discount
    {
        return (new Discount())
            ->setName('discount_name')
            ->setCriteria(['criteria_name' => 'criteria_value'])
            ->setValue(0.5)
            ->setType('discount_type');
    }

    public static function assertTestObject(Discount $discount): void
    {
        self::assertNotEquals(null, $discount);
        self::assertGreaterThan(0, $discount->getId());
        self::assertEquals('discount_name', $discount->getName());
        self::assertEquals(['criteria_name' => 'criteria_value'], $discount->getCriteria());
        self::assertEquals(0.5, $discount->getValue());
        self::assertEquals('discount_type', $discount->getType());
    }
}