<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class CartTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function cart_can_not_be_created_without_user(): void
    {
        // Given
        $cart = self::getTestObject();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    /** @test */
    public function cart_can_be_created_in_database(): void
    {
        // Given
        $user = UserTest::getTestObject();
        $cart = self::getTestObject()
            ->setUser($user);

        /** @var CartRepository $cartRepository */
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $cartId = $cart->getId();

        /** @var Cart $cartRecord */
        $cartRecord = $cartRepository->find($cartId);

        // Then
        self::assertTestObject($cartRecord);
    }

    public function cart_can_access_products(): void
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

        $user = UserTest::getTestObject();
        $cart = self::getTestObject()
            ->setUser($user)
            ->addProduct($product);

        $this->entityManager->persist($category);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $productId = $product->getId();
        
        /** @var CartRepository $cartRepository */
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        /** @var Cart $cartRecord */
        $cartRecord = $cartRepository->find($cart->getId());

        // When
        $productCollection = $cartRecord->getProducts();

        // Then
        self::assertEquals(1, $productCollection->count());

        /** @var Product $productRecord */
        $productRecord = $productCollection->first();

        self::assertEquals($productId, $productRecord->getId());
    }

    public function cart_can_access_discounts(): void
    {
        // Given
        $discount = DiscountTest::getTestObject();

        $user = UserTest::getTestObject();
        $cart = self::getTestObject()
            ->setUser($user)
            ->addDiscount($discount);

        $this->entityManager->persist($discount);
        $this->entityManager->persist($user);
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $discountId = $discount->getId();

        /** @var CartRepository $cartRepository */
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        /** @var Cart $cartRecord */
        $cartRecord = $cartRepository->find($cart->getId());

        // When
        $discountCollection = $cartRecord->getDiscounts();

        // Then
        self::assertEquals(1, $discountCollection->count());

        /** @var Discount $discountRecord */
        $discountRecord = $discountCollection->first();

        self::assertEquals($discountId, $discountRecord->getId());
    }

    public static function getTestObject(): Cart
    {
        return (new Cart())
            ->setPrice(1.0);
    }

    public static function assertTestObject(Cart $cart): void
    {
        self::assertNotEquals(null, $cart);
        self::assertGreaterThan(0, $cart->getId());
        self::assertEquals(1, $cart->getPrice());
    }
}