<?php

namespace App\Tests\Entity;

use App\Entity\Address;
use App\Entity\User;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class AddressTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function address_can_not_be_created_without_user(): void
    {
        // Given
        $address = new Address();
        $address->setStreet('address_street');
        $address->setNumber('address_number');
        $address->setPostCode('address_post_code');

        // Expect
        $this->expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($address);
        $this->entityManager->flush();
    }

    /** @test */
    public function address_can_be_created_in_database(): void
    {
        // Given
        $address = new Address();
        $address->setStreet('address_street');
        $address->setNumber('address_number');
        $address->setPostCode('address_post_code');

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');
        $user->setAddress($address);

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var Address $addressRecord */
        $addressRecord = $addressRepository->findOneBy(['street' => 'address_street']);

        // Then
        $this->assertNotEquals(null, $addressRecord);
        $this->assertGreaterThan(0, $addressRecord->getId());
        $this->assertEquals('address_street', $addressRecord->getStreet());
        $this->assertEquals('address_number', $addressRecord->getNumber());
        $this->assertEquals('address_post_code', $addressRecord->getPostCode());
    }

    /** @test */
    public function address_is_deleted_when_user_is_deleted(): void
    {
        // Given
        $address = new Address();
        $address->setStreet('address_street');
        $address->setNumber('address_number');
        $address->setPostCode('address_post_code');

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');
        $user->setAddress($address);

        /** @var AddressRepository $addressRepository */
        $addressRepository = $this->entityManager->getRepository(Address::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $addressId = $address->getId();

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        /** @var Address $addressRecord */
        $addressRecord = $addressRepository->find($addressId);

        // Then
        $this->assertEquals(null, $addressRecord);
    }

    /** @test */
    public function user_is_not_deleted_when_address_is_deleted(): void
    {
        // Given
        $address = new Address();
        $address->setStreet('address_street');
        $address->setNumber('address_number');
        $address->setPostCode('address_post_code');

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');
        $user->setAddress($address);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();

        $this->entityManager->remove($address);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($userId);

        // Then
        $this->assertNotEquals(null, $userRecord);
    }

    /** @test */
    public function user_can_access_address(): void
    {
        // Given
        $address = new Address();
        $address->setStreet('address_street');
        $address->setNumber('address_number');
        $address->setPostCode('address_post_code');

        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');
        $user->setAddress($address);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userId = $user->getId();
        $addressId = $address->getId();

        /** @var User $userRecord */
        $userRecord = $userRepository->find($userId);

        // Then
        $this->assertEquals($addressId, $userRecord->getAddress()->getId());
    }
}