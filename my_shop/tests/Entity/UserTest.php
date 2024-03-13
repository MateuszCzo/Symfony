<?php

namespace App\Test\Entity;

use App\Entity\User;
use App\Tests\KernelTestCaseWithDatabase;

class UserTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function user_can_be_created_in_database(): void
    {
        // Given
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setRoles(['ROLE_TEST']);
        $user->setPassword('user_password');

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        /** @var User $userRecord */
        $userRecord = $userRepository->findOneBy(['email' => 'test@test.test']);

        // Then
        $this->assertEquals('test@test.test', $userRecord->getEmail());
        $this->assertEquals(['ROLE_TEST', 'ROLE_USER'], $userRecord->getRoles());
        $this->assertEquals('user_password', $userRecord->getPassword());
    }
}