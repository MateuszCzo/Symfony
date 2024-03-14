<?php

namespace App\Tests\Entity;

use App\Entity\Delivery;
use App\Entity\Manufacturer;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use App\Tests\ProductTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class DeliveryTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function delivery_can_be_created_in_database(): void
    {
        // Given
        $delivery = self::getTestObject();

        /** @var DeliveryRepository $deliveryRepository */
        $deliveryRepository = $this->entityManager->getRepository(Delivery::class);

        // When
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        /** @var Delivery $deliveryRecord */
        $deliveryRecord = $deliveryRepository->find($delivery->getId());

        // Then
        self::assertTestObject($deliveryRecord);
    }

    // /** @test */
    // public function delivery_can_not_be_deleted_while_in_order(): void
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

    //     $delivery = self::getTestObject();

    //     $payment = PaymentTest::getTestObject();

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

    public static function getTestObject(): Delivery
    {
        return (new Delivery())
            ->setName('delivery_name')
            ->setDescription('delivery_description')
            ->setType('delivery_type')
            ->setActive(true);
    }

    public static function assertTestObject($delivery): void
    {
        self::assertNotEquals(null, $delivery);
        self::assertGreaterThan(0, $delivery->getId());
        self::assertEquals('delivery_name', $delivery->getName());
        self::assertEquals('delivery_description', $delivery->getDescription());
        self::assertEquals('delivery_type', $delivery->getType());
        self::assertEquals(true, $delivery->isActive());
    }
}