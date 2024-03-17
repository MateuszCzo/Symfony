<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use App\Entity\User;
use App\Tests\DataProvider;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class ContactTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function contact_can_not_be_created_without_user(): void
    {
        // Given
        $contact = DataProvider::getContact();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);
        self::expectExceptionMessage('constraint failed: contact.user_id');

        // When
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }

    /** @test */
    public function contact_can_be_created_in_database(): void
    {
        // Given
        $user = DataProvider::getConfiguredUser($this->entityManager);

        $contact = DataProvider::getContact()
            ->setUser($user);

        /** @var ContactRepository $contactRepository */
        $contactRepository = $this->entityManager->getRepository(Contact::class);

        // When
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        /** @var Contact $contactRecord */
        $contactRecord = $contactRepository->find($contact->getId());

        // Then
        self::assertTestObject($contact, $contactRecord);
    }

    /** @test */
    public function user_is_not_deleted_when_contact_is_deleted(): void
    {
        // Given
        $contact = DataProvider::getConfiguredContact($this->entityManager);
            
        $user = $contact->getUser();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        self::assertNotEquals(null, $userRecord);
    }

    public static function assertTestObject(Contact $contactReference, Contact $contactToTest): void
    {
        self::assertNotEquals(null, $contactToTest);
        self::assertEquals($contactReference->getId(), $contactToTest->getId());
        self::assertEquals($contactReference->getUser(), $contactToTest->getUser());
        self::assertEquals($contactReference->getPhoneNumber(), $contactToTest->getPhoneNumber());
    }
}