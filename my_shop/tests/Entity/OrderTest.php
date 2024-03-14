<?php

namespace App\Tests\Entity;

use App\Entity\Category;
use App\Entity\Delivery;
use App\Entity\Manufacturer;
use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Product;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class OrderTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function order_can_not_be_created_without_user(): void
    {
        // Given
        $category = (new Category())
            ->setImage(ImageTest::getTestObject());

        $product = (new Product)
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category);

        $delivery = DeliveryTest::getTestObject();

        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->addProduct($product)
            ->setDelivery($delivery)
            ->setPaymeny($payment);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /** @test */
    public function order_can_not_be_created_without_delivery(): void
    {
        // Given
        $category = (new Category())
            ->setImage(ImageTest::getTestObject());

        $product = (new Product)
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->addProduct($product)
            ->setUser($user)
            ->setPaymeny($payment);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    /** @test */
    public function order_can_not_be_created_without_payment(): void
    {
        // Given
        $category = (new Category())
            ->setImage(ImageTest::getTestObject());

        $product = (new Product)
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();

        $order = self::getTestObject()
            ->addProduct($product)
            ->setUser($user)
            ->setDelivery($delivery);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }

    // /** @test */
    // public function order_can_not_be_created_without_product(): void
    // {
    //     // Given
    //     $user = UserTest::getTestObject();

    //     $delivery = DeliveryTest::getTestObject();
        
    //     $payment = PaymentTest::getTestObject();

    //     $order = self::getTestObject()
    //         ->setPaymeny($payment)
    //         ->setUser($user)
    //         ->setDelivery($delivery);

    //     // Expect
    //     self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->persist($user);
    //     $this->entityManager->persist($payment);
    //     $this->entityManager->persist($delivery);
    //     $this->entityManager->persist($order);
    //     $this->entityManager->flush();
    // }

    /** @test */
    public function order_can_be_created_in_database(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();
        
        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->entityManager->getRepository(Order::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        /** @var Order $orderRecord */
        $orderRecord = $orderRepository->find($order->getId());

        // Then
        self::asserttestObject($orderRecord);
    }

    /** @test */
    public function order_can_access_delivery(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();
        
        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->entityManager->getRepository(Order::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        /** @var Order $orderRecord */
        $orderRecord = $orderRepository->find($order->getId());

        // When
        $deliveryRecord = $orderRecord->getDelivery();

        // Then
        self::assertEquals($delivery->getId(), $deliveryRecord->getId());
    }

    /** @test */
    public function delivery_is_not_deleted_when_order_is_deleted(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();
        
        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery);

        /** @var DeliveryRepository $deliveryRepository */
        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $deliveryId = $delivery->getId();

        // When
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        // Then
        /** @var Delivery $deliveryRecord */
        $deliveryRecord = $deliveryRepository->find($deliveryId);

        self::assertNotEquals(null, $deliveryRecord);
    }

    /** @test */
    public function order_can_access_payment(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();
        
        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery);

        /** @var OrderRepository $orderRepository */
        $orderRepository = $this->entityManager->getRepository(Order::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        /** @var Order $orderRecord */
        $orderRecord = $orderRepository->find($order->getId());

        // When
        $paymentRecord = $orderRecord->getPaymeny();

        // Then
        self::assertEquals($payment->getId(), $paymentRecord->getId());
    }

    /** @test */
    public function delivery_is_not_deleted_when_order_is_payment(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $user = UserTest::getTestObject();

        $delivery = DeliveryTest::getTestObject();
        
        $payment = PaymentTest::getTestObject();

        $order = self::getTestObject()
            ->setPaymeny($payment)
            ->setUser($user)
            ->setDelivery($delivery);

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->entityManager->getRepository(Payment::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->persist($user);
        $this->entityManager->persist($delivery);
        $this->entityManager->persist($payment);
        $this->entityManager->persist($order);
        $this->entityManager->flush();

        $paymentId = $payment->getId();

        // When
        $this->entityManager->remove($order);
        $this->entityManager->flush();

        // Then
        /** @var Payment $paymentRecord */
        $paymentRecord = $paymentRepository->find($paymentId);

        self::assertNotEquals(null, $paymentRecord);
    }

    public static function getTestObject(): Order
    {
        return (new Order())
            ->setStatus('order_status')
            ->setPrice(1.0);
    }

    public static function assertTestObject(Order $order): void
    {
        self::assertNotEquals(null, $order);
        self::assertGreaterThan(0, $order->getId());
        self::assertEquals('order_status', $order->getStatus());
        self::assertEquals(1, $order->getPrice());
    }
}