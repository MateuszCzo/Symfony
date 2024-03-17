<?php

namespace App\Tests\Entity;

use App\Entity\Delivery;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class DeliveryTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function delivery_can_be_created_in_database(): void
    {
        // Given
        $delivery = DataProvider::getDelivery();

        /** @var DeliveryRepository $deliveryRepository */
        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);

        // When
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        /** @var Delivery $deliveryRecord */
        $deliveryRecord = $deliveryRepository->find($delivery->getId());

        // Then
        self::assertTestObject($delivery, $deliveryRecord);
    }

    public static function assertTestObject(Delivery $deliveryReference, Delivery $deliveryToTest): void
    {
        self::assertNotEquals(null, $deliveryToTest);
        self::assertEquals($deliveryReference->getId(), $deliveryToTest->getId());
        self::assertEquals($deliveryReference->getName(), $deliveryToTest->getName());
        self::assertEquals($deliveryReference->getDescription(), $deliveryToTest->getDescription());
        self::assertEquals($deliveryReference->getType(), $deliveryToTest->getType());
        self::assertEquals($deliveryReference->isActive(), $deliveryToTest->isActive());
    }
}