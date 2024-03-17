<?php

namespace App\Test\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Tests\DataProvider;
use App\Tests\WebTestCaseWithDatabase;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationControllerTest extends WebTestCaseWithDatabase
{
    /** @test */
    public function user_can_access_registration_form(): void
    {
        // Given

        // When
        $crawler = $this->client->request('GET', '/register');
        //dd($crawler);

        // Then
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Register'); 
    }

    /** @test */
    public function user_can_not_be_created_without_email(): void
    {
        // Given
        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[plainPassword]' => 'test_password',
            'registration_form[agreeTerms]' => true,
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('li', 'Please enter an email'); 
        self::assertEquals(0, count($userRepository->findAll()));
    }

    /** @test */
    public function user_can_not_be_created_without_password(): void
    {
        // Given
        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => 'test@test.test',
            'registration_form[agreeTerms]' => true,
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('li', 'Please enter a password');
        self::assertEquals(0, count($userRepository->findAll()));
    }

    /** @test */
    public function user_can_not_be_created_without_password_min_6_characters(): void
    {
        // Given
        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => 'test@test.test',
            'registration_form[plainPassword]' => 'pass',
            'registration_form[agreeTerms]' => true,
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('li', 'Your password should be at least 6 characters');
        self::assertEquals(0, count($userRepository->findAll()));
    }

    /** @test */
    public function user_can_not_be_created_without_user_consent(): void
    {
        // Given
        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => 'test@test.test',
            'registration_form[plainPassword]' => 'test_password',
        ]);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('li', 'You should agree to our terms.');
        self::assertEquals(0, count($userRepository->findAll()));
    }

    /** @test */
    public function user_can_not_be_created_when_email_is_used(): void
    {
        // Given
        $user = DataProvider::getConfiguredUser($this->entityManager);

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => $user->getEmail(),
            'registration_form[plainPassword]' => $user->getPassword(),
            'registration_form[agreeTerms]' => true,
        ]);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertResponseStatusCodeSame(422);
        self::assertSelectorTextContains('li', 'There is already an account with this email');

        /** @var Collection $userCollection */
        $userCollection = $userRepository->findBy(['email' => $user->getEmail()]);

        self::assertCount(1, $userCollection);
    }

    /** @test */
    public function user_password_is_hashed(): void
    {
        // Given
        $this->client->disableReboot();

        $emailVerifierMock = $this->getMockBuilder(EmailVerifier::class)
            ->disableOriginalConstructor()
            ->getMock();
        $emailVerifierMock->expects(self::once())
            ->method('sendEmailConfirmation');
        
        self::getContainer()->set(EmailVerifier::class, $emailVerifierMock);

        $user = DataProvider::getUser();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => $user->getEmail(),
            'registration_form[plainPassword]' => $user->getPassword(),
            'registration_form[agreeTerms]' => true,
        ]);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->findOneBy(['email' => $user->getEmail()]);

        self::assertResponseStatusCodeSame(302);

        $this->client->followRedirect();
        
        self::assertEquals('http://localhost/', $this->client->getRequest()->getUri());
        self::assertNotEquals(null, $userRecord);
        self::assertNotEquals($user->getPassword(), $userRecord->getPassword());

        $this->client->enableReboot();
    }

    /** @test */
    public function email_is_send(): void
    {
        // Given
        $this->client->disableReboot();

        $userPasswordHasher = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $userPasswordHasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('hashed_password');
        
        self::getContainer()->set(UserPasswordHasherInterface::class, $userPasswordHasher);

        $user = DataProvider::getUser();

        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/register');

        $form = $crawlerGET->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => $user->getEmail(),
            'registration_form[plainPassword]' => $user->getPassword(),
            'registration_form[agreeTerms]' => true,
        ]);

        // When
        /** @var Crawler $crawlerPOST */
        $crawlerPOST = $this->client->submit($form);

        // Then
        self::assertEmailCount(1);
        
        $email = self::getMailerMessage();

        self::assertEmailHtmlBodyContains($email, 'Hi! Please confirm your email!');

        $this->client->enableReboot();
    }

    /** @test */
    public function verification_link_works_correctly(): void
    {
        // Given
        $this->client->disableReboot();

        /** @var UserRepository $userRepository */
        $userRepository = $this->entityManager->getRepository(User::class);

        $userPasswordHasher = $this->getMockBuilder(UserPasswordHasherInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $userPasswordHasher->expects(self::once())
            ->method('hashPassword')
            ->willReturn('hashed_password');
        
        self::getContainer()->set(UserPasswordHasherInterface::class, $userPasswordHasher);

        $user = DataProvider::getUser();

        /** @var Crawler $crawler */
        $crawler = $this->client->request('GET', '/register');

        $form = $crawler->selectButton('Register')->form();

        $form->setValues([
            'registration_form[email]' => $user->getEmail(),
            'registration_form[plainPassword]' => $user->getPassword(),
            'registration_form[agreeTerms]' => true,
        ]);

        $crawler = $this->client->submit($form);

        $email = self::getMailerMessage();

        $crawler = new Crawler($email->getHtmlBody());

        $href = $crawler->filter('a')->attr('href');

        // When
        $crawler = $this->client->request('GET', $href);

        // Then
        /** @var User $userRecord */
        $userRecord = $userRepository->findOneBy(['email' => $user->getEmail()]);

        self::assertTrue($userRecord->isVerified());

        $this->client->enableReboot();
    }
}
