<?php

namespace App\Tests\Entity;

use App\Entity\Payment;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class PaymentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function payment_can_be_created_in_database(): void
    {
        // Given
        $payment = DataProvider::getPayment();

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->entityManager->getRepository(Payment::class);

        // When
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        /** @var Payment $paymentRecord */
        $paymentRecord = $paymentRepository->find($payment->getId());

        // Then
        self::assertTestObject($payment, $paymentRecord);
    }

    public static function assertTestObject(Payment $paymentReference, Payment $paymentToTest): void
    {
        self::assertNotEquals(null, $paymentToTest);
        self::assertEquals($paymentReference->getId(), $paymentToTest->getId());
        self::assertEquals($paymentReference->getName(), $paymentToTest->getName());
        self::assertEquals($paymentReference->getDescription(), $paymentToTest->getDescription());
        self::assertEquals($paymentReference->getType(), $paymentToTest->getType());
        self::assertEquals($paymentReference->isActive(), $paymentToTest->isActive());
    }
}
