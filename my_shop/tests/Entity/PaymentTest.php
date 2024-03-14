<?php

namespace App\Tests\Entity;

use App\Entity\Payment;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class PaymentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function payment_can_be_created_in_database(): void
    {
        // Given
        $payment = self::getTestObject();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->entityManager->getRepository(Payment::class);

        // When
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        /** @var Payment $paymentRecord */
        $paymentRecord = $paymentRepository->find($payment->getId());

        // Then
        self::assertTestObject($paymentRecord);
    }

    // /** @test */
    // public function payment_can_not_be_deleted_hile_in_order(): void
    // {
    //     // Given
    //     $category = CategoryTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $manufacturer = ManufacturerTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $product = ProductTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject())
    //         ->setManufacturer($manufacturer)
    //         ->setCategory($category);

    //     $user = UserTest::getTestObject();

    //     $delivery = DeliveryTest::getTestObject();

    //     $payment = self::getTestObject();

    //     $order = OrderTest::getTestObject()
    //         ->setUser($user)
    //         ->setDelivery($delivery)
    //         ->setPaymeny($payment)
    //         ->addProduct($product);

    //     $this->entityManager->persist($category);
    //     $this->entityManager->persist($manufacturer);
    //     $this->entityManager->persist($product);
    //     $this->entityManager->persist($user);
    //     $this->entityManager->persist($delivery);
    //     $this->entityManager->persist($payment);
    //     $this->entityManager->persist($order);
    //     $this->entityManager->flush();

    //     // Expect
    //    self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($delivery);
    //     $this->entityManager->flush();
    // }

    public static function getTestObject(): Payment
    {
        return (new Payment())
            ->setName('payment_name')
            ->setDescription('payment_description')
            ->setType('payment_type')
            ->setActive(true);
    }

    public static function assertTestObject(Payment $payment): void
    {
        self::assertNotEquals(null, $payment);
        self::assertGreaterThan(0, $payment->getId());
        self::assertEquals('payment_name', $payment->getName());
        self::assertEquals('payment_description', $payment->getDescription());
        self::assertEquals('payment_type', $payment->getType());
        self::assertEquals(true, $payment->isActive());
    }
}
