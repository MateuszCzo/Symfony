<?php

namespace App\Tests\Entity;

use App\Entity\Delivery;
use App\Tests\KernelTestCaseWithDatabase;

class DeliveryTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function delivery_can_be_created_in_database()
    {
        // Given
        $delivery = new Delivery();
        $delivery->setName('delivery_name');
        $delivery->setDescription('delivery_description');
        $delivery->setType('delivery_type');

        /** @var DeliveryRepository $deliveryRepository */
        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);

        // When
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        /** @var Manufacturer $manufacturerRecord */
        $deliveryRecord = $deliveryRepository->findOneBy(['name' => 'delivery_name']);

        // Then
        $this->assertNotEquals(null, $deliveryRecord);
        $this->assertGreaterThan(0, $deliveryRecord->getId());
        $this->assertEquals('delivery_name', $deliveryRecord->getName());
        $this->assertEquals('delivery_description', $deliveryRecord->getDescription());
        $this->assertEquals('delivery_type', $deliveryRecord->getType());
    }

    /** @test */
    public function delivery_can_not_be_deleted_when_order_is_using_it(): void
    {
        //todo
    }
}