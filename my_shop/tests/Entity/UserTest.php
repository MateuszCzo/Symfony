<?php

namespace App\Test\Entity;

use App\Entity\Address;
use App\Entity\Contact;
use App\Entity\User;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;

class UserTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function user_can_be_created_in_database(): void
    {
        // Given
        $user = DataProvider::getUser();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        // Then
        self::assertTestObject($user, $userRecord);
    }

    /** @test */
    public function user_can_access_address(): void
    {
        // Given
        $address = DataProvider::getConfiguredAddress($this->entityManager);

        $user = DataProvider::getUser()
            ->setAddress($address);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        // When
        $addressRecord = $userRecord->getAddress();

        // Then
        self::assertEquals($address->getId(), $addressRecord->getId());
    }

    
    /** @test */
    public function address_is_deleted_when_user_is_deleted(): void
    {
        // Given
        $address = DataProvider::getConfiguredAddress($this->entityManager);

        $user = DataProvider::getConfiguredUser($this->entityManager)
            ->setAddress($address);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        $addressId = $address->getId();

        // When
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Then
        /** @var Address $addressRecord */
        $addressRecord = $addressRepository->find($addressId);

        self::assertEquals(null, $addressRecord);
    }

    /** @test */
    public function user_can_access_contact(): void
    {
        // Given
        $contact = DataProvider::getConfiguredContact($this->entityManager);

        $user = DataProvider::getUser()
            ->setContact($contact);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());
        
        // When
        $contactRecord = $userRecord->getContact();

        // Then
        self::assertEquals($contact->getId(), $contactRecord->getId());
    }

    /** @test */
    public function contact_is_deleted_when_user_is_deleted(): void
    {
        // Given
        $contact = DataProvider::getConfiguredContact($this->entityManager);

        $user = DataProvider::getConfiguredUser($this->entityManager)
            ->setContact($contact);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var ContactRepository $contactRepository */
        $contactRepository = $this->entityManager->getRepository(Contact::class);

        $contactId = $contact->getId();

        // When
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Then
        /** @var Address $contactRecord */
        $contactRecord = $contactRepository->find($contactId);

        self::assertEquals(null, $contactRecord);
    }

    public static function assertTestObject(User $userReference, User $userToTest): void
    {
        self::assertNotEquals(null, $userToTest);
        self::assertEquals($userReference->getId(), $userToTest->getId());
        self::assertEquals($userReference->getEmail(), $userToTest->getEmail());
        self::assertEquals($userReference->getRoles(), $userToTest->getRoles());
        self::assertEquals($userReference->getPassword(), $userToTest->getPassword());
        self::assertEquals($userReference->getAddress(), $userToTest->getAddress());
        self::assertEquals($userReference->getContact(), $userToTest->getContact());
        self::assertEquals($userReference->getCart(), $userToTest->getCart());
        self::assertEquals($userReference->getOrders(), $userToTest->getOrders());
        self::assertEquals($userReference->isVerified(), $userToTest->isVerified());
    }
}