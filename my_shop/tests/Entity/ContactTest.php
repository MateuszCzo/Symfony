<?php

namespace App\Tests\Entity;

use App\Entity\Contact;
use App\Entity\User;
use App\Test\Entity\UserTest;
use App\Tests\KernelTestCaseWithDatabase;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;

class ContactTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function contact_can_not_be_created_without_user(): void
    {
        // Given
        $contact = self::getTestObject();

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($contact);
        $this->entityManager->flush();
    }

    /** @test */
    public function contact_can_be_created_in_database(): void
    {
        // Given
        $user = UserTest::getTestObject();

        $contact = self::getTestObject()
            ->setUserId($user);

        /** @var ContactRepository $contactRepository */
        $contactRepository = $this->entityManager->getRepository(Contact::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        /** @var Contact $contactRecord */
        $contactRecord = $contactRepository->find($contact->getId());

        // Then
        self::assertTestObject($contactRecord);
    }

    /** @test */
    public function user_is_not_deleted_when_contact_is_deleted(): void
    {
        // Given
        $user = UserTest::getTestObject();

        $contact = self::getTestObject()
            ->setUserId($user);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $this->entityManager->persist($user);
        $this->entityManager->persist($contact);
        $this->entityManager->flush();

        // When
        $this->entityManager->remove($contact);
        $this->entityManager->flush();

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->find($user->getId());

        self::assertNotEquals(null, $userRecord);
    }

    public static function getTestObject(): Contact
    {
        return (new Contact())
            ->setPhoneNumber(123456789);
    }

    public static function assertTestObject(Contact $contact): void
    {
        self::assertNotEquals(null, $contact);
        self::assertGreaterThan(0, $contact->getId());
        self::assertEquals(123456789, $contact->getPhoneNumber());
    }
}