<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use App\Entity\User;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class AddressTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function address_can_not_be_created_without_user(): void
    {
        // Given
        $address = self::getTestObject();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /** @test */
    public function address_can_be_created_in_database(): void
    {
        // Given
        $user = UserTest::getTestObject();
        $address = self::getTestObject()
            ->setUserId($user);

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        /** @var Address $addressRecord */
        $addressRecord = $addressRepository->find($address->getId());

        // Then
        self::assertTestObject($addressRecord);
    }

    /** @test */
    public function user_is_not_deleted_when_address_is_deleted(): void
    {
        // Given
        $user = UserTest::getTestObject();
        $address = self::getTestObject()
            ->setUserId($user);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->entityManager->persist($user);
        $this->entityManager->persist($address);
        $this->entityManager->flush();
        
        // When
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        self::assertNotEquals(null, $userRecord);
    }

    public static function getTestObject(): Address
    {
        return (new Address())
            ->setStreet('address_street')
            ->setNumber('address_number')
            ->setPostCode('address_post_code');
    }

    public static function assertTestObject($address): void
    {
        self::assertNotEquals(null, $address);
        self::assertGreaterThan(0, $address->getId());
        self::assertEquals('address_street', $address->getStreet());
        self::assertEquals('address_number', $address->getNumber());
        self::assertEquals('address_post_code', $address->getPostCode());
    }
}