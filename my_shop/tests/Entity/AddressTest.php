<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use App\Entity\User;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class AddressTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function address_can_not_be_created_without_user(): void
    {
        // Given
        $address = DataProvider::getAddress();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: address.user_id');

        // When
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /** @test */
    public function address_can_be_created_in_database(): void
    {
        // Given
        $user = DataProvider::getConfiguredUser($this->entityManager);

        $address = DataProvider::getAddress()
            ->setUser($user);

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        // When
        $this->entityManager->persist($address);
        $this->entityManager->flush();

        /** @var Address $addressRecord */
        $addressRecord = $addressRepository->find($address->getId());

        // Then
        self::assertTestObject($address, $addressRecord);
    }

    /** @test */
    public function user_is_not_deleted_when_address_is_deleted(): void
    {
        // Given
        $address = DataProvider::getConfiguredAddress($this->entityManager);

        $user = $address->getUser();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);
        
        // When
        $this->entityManager->remove($address);
        $this->entityManager->flush();

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        self::assertNotEquals(null, $userRecord);
    }

    public static function assertTestObject(Address $addressReference, Address $addressToTest): void
    {
        self::assertNotEquals(null, $addressToTest);
        self::assertEquals($addressReference->getId(), $addressToTest->getId());
        self::assertEquals($addressReference->getStreet(), $addressToTest->getStreet());
        self::assertEquals($addressReference->getNumber(), $addressToTest->getNumber());
        self::assertEquals($addressReference->getPostCode(), $addressToTest->getPostCode());
        self::assertEquals($addressReference->getUser(), $addressToTest->getUser());
    }
}
