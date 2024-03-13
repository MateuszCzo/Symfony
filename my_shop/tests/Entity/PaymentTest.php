<?php

namespace App\Tests\Entity;

use App\Entity\Payment;
use App\Tests\KernelTestCaseWithDatabase;

class PaymentTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function payment_can_be_created_in_database(): void
    {
        // Given
        $payment = new Payment();
        $payment->setName('payment_name');
        $payment->setDescription('payment_description');
        $payment->setType('payment_type');

        /** @var PaymentRepository $paymentRepository */
        $paymentRepository = $this->entityManager->getRepository(Payment::class);

        // When
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        /** @var Payment $paymentRecord */
        $paymentRecord = $paymentRepository->findOneBy(['name' => 'payment_name']);

        // Then
        $this->assertEquals('payment_name', $paymentRecord->getName());
        $this->assertEquals('payment_description', $paymentRecord->getDescription());
        $this->assertEquals('payment_type', $paymentRecord->getType());
    }

    /** @test */
    public function payment_can_not_be_deleted_when_order_is_using_it(): void
    {
        //todo
    }
}