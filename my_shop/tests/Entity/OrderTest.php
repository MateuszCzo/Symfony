<?php

namespace App\Tests\Entity;

use App\Entity\Delivery;
use App\Entity\Order;
use App\Entity\Payment;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class OrderTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function order_can_not_be_created_without_user(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $delivery = DataProvider::getConfiguredDelivery($this->entityManager);

        $payment = DataProvider::getConfiguredPayment($this->entityManager);

        $order = DataProvider::getOrder()
            ->addProduct($product)
            ->setDelivery($delivery)
            ->setPaymeny($payment);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: order.user_id');

        // When
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /** @test */
    public function order_can_not_be_created_without_delivery(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $payment = DataProvider::getConfiguredPayment($this->entityManager);

        $user = DataProvider::getConfiguredUser($this->entityManager);

        $order = DataProvider::getOrder()
            ->addProduct($product)
            ->setUser($user)
            ->setPaymeny($payment);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: order.delivery_id');

        // When
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /** @test */
    public function order_can_not_be_created_without_payment(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $delivery = DataProvider::getConfiguredDelivery($this->entityManager);

        $user = DataProvider::getConfiguredUser($this->entityManager);

        $order = DataProvider::getOrder()
            ->addProduct($product)
            ->setUser($user)
            ->setDelivery($delivery);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: order.paymeny_id');

        // When
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /** @test */
    public function order_can_be_created_in_database(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $delivery = DataProvider::getConfiguredDelivery($this->entityManager);

        $payment = DataProvider::getConfiguredPayment($this->entityManager);

        $user = DataProvider::getConfiguredUser($this->entityManager);

        $order = DataProvider::getOrder()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery)
            ->addProduct($product);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->entityManager->getRepository(Order::class);

        // When
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        /** @var Order $orderRecord */
        $orderRecord = $orderRepository->find($order->getId());

        // Then
        self::asserttestObject($order, $orderRecord);
    }

    /** @test */
    public function delivery_is_not_deleted_when_order_is_deleted(): void
    {
        // Given
        $order = DataProvider::getConfiguredOrder($this->entityManager);

        $delivery = $order->getDelivery();

        /** @var DeliveryRepository $deliveryRepository */
        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);

        // When
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        // Then
        /** @var Delivery $deliveryRecord */
        $deliveryRecord = $deliveryRepository->find($delivery->getId());

        self::assertNotEquals(null, $deliveryRecord);
    }

    /** @test */
    public function payment_is_not_deleted_when_order_is_deleted(): void
    {
        // Given
        $order = DataProvider::getConfiguredOrder($this->entityManager);

        $payment = $order->getPaymeny();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->entityManager->getRepository(Payment::class);

        // When
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        // Then
        /** @var Payment $paymentRecord */
        $paymentRecord = $paymentRepository->find($payment->getId());

        self::assertNotEquals(null, $paymentRecord);
    }

    public static function assertTestObject(Order $orderReference, Order $orderToTest): void
    {
        self::assertNotEquals(null, $orderToTest);
        self::assertEquals($orderReference->getId(), $orderToTest->getId());
        self::assertEquals($orderReference->getUser(), $orderToTest->getUser());
        self::assertEquals($orderReference->getDelivery(), $orderToTest->getDelivery());
        self::assertEquals($orderReference->getPaymeny(), $orderToTest->getPaymeny());
        self::assertEquals($orderReference->getStatus(), $orderToTest->getStatus());
        self::assertEquals($orderReference->getProducts(), $orderToTest->getProducts());
        self::assertEquals($orderReference->getDiscounts(), $orderToTest->getDiscounts());
        self::assertEquals($orderReference->getPrice(), $orderToTest->getPrice());
    }
}