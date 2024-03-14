<?php

namespace App\Test\Entity;

use App\Entity\Address;
use App\Entity\Contact;
use App\Entity\User;
use App\Tests\Entity\AddressTest;
use App\Tests\Entity\ContactTest;
use App\Tests\KernelTestCaseWithDatabase;

class UserTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function user_can_be_created_in_database(): void
    {
        // Given
        $user = self::getTestObject();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        // Then
        self::assertTestObject($userRecord);
    }

    /** @test */
    public function user_can_access_address(): void
    {
        // Given
        $address = AddressTest::getTestObject();

        $user = self::getTestObject()
            ->setAddress($address);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $addressId = $address->getId();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        // When
        $addressRecord = $userRecord->getAddress();

        // Then
        self::assertEquals($addressId, $addressRecord->getId());
    }

    
    /** @test */
    public function address_is_deleted_when_user_is_deleted(): void
    {
        // Given
        $address = AddressTest::getTestObject();

        $user = UserTest::getTestObject();
        $user->setAddress($address);

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

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
        $contact = ContactTest::getTestObject();

        $user = self::getTestObject()
            ->setContact($contact);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $contactId = $contact->getId();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());
        
        // When
        $contactRecord = $userRecord->getContact();

        // Then
        self::assertEquals($contactId, $contactRecord->getId());
    }

    /** @test */
    public function contact_is_deleted_when_user_is_deleted(): void
    {
        // Given
        $contact = ContactTest::getTestObject();

        $user = UserTest::getTestObject();
        $user->setContact($contact);

        /** @var ContactRepository $contactRepository */
        $contactRepository = $this->entityManager->getRepository(Contact::class);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $contactId = $contact->getId();

        // When
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        // Then
        /** @var Address $contactRecord */
        $contactRecord = $contactRepository->find($contactId);

        self::assertEquals(null, $contactRecord);
    }

    public static function getTestObject(): User
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');
        return $user;
    }

    public static function assertTestObject($user): void
    {
        self::assertNotEquals(null, $user);
        self::assertGreaterThan(0, $user->getId());
        self::assertEquals('test@test.test', $user->getEmail());
        self::assertEquals(['ROLE_TEST', 'ROLE_USER'], $user->getRoles());
        self::assertEquals('user_password', $user->getPassword());
    }
}