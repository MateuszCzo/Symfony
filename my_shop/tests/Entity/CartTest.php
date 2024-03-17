<?php

namespace App\Tests\Entity;

use App\Entity\Cart;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class CartTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function cart_can_not_be_created_without_user(): void
    {
        // Given
        $cart = DataProvider::getCart();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: cart.user_id');

        // When
        $this->entityManager->persist($cart);
        $this->entityManager->flush();
    }

    /** @test */
    public function cart_can_be_created_in_database(): void
    {
        // Given
        $user = DataProvider::getConfiguredUser($this->entityManager);

        $cart = DataProvider::getCart()
            ->setUser($user);

        /** @var CartRepository $cartRepository */
        $cartRepository = $this->entityManager->getRepository(Cart::class);

        // When
        $this->entityManager->persist($cart);
        $this->entityManager->flush();

        $cartId = $cart->getId();

        /** @var Cart $cartRecord */
        $cartRecord = $cartRepository->find($cartId);

        // Then
        self::assertTestObject($cart, $cartRecord);
    }

    public function cart_can_access_products(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $cart = DataProvider::getConfiguredCart($this->entityManager)
            ->addProduct($product);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();
        
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

        self::assertEquals($product->getId(), $productRecord->getId());
    }

    public function cart_can_access_discounts(): void
    {
        // Given
        $discount = DataProvider::getConfiguredDiscount($this->entityManager);

        $cart = DataProvider::getConfiguredCart($this->entityManager)
            ->addDiscount($discount);

        $this->entityManager->persist($cart);
        $this->entityManager->flush();

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

        self::assertEquals($discount->getId(), $discountRecord->getId());
    }

    public static function assertTestObject(Cart $cartReference, Cart $cartToTest): void
    {
        self::assertNotEquals(null, $cartToTest);
        self::assertEquals($cartReference->getID(), $cartToTest->getId());
        self::assertEquals($cartReference->getPrice(), $cartToTest->getPrice());
        self::assertEquals($cartReference->getUser(), $cartToTest->getUser());
        self::assertEquals($cartReference->getProducts(), $cartToTest->getProducts());
        self::assertEquals($cartReference->getDiscounts(), $cartToTest->getDiscounts());
    }
}