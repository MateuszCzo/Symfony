<?php

namespace App\Tests\Controller;

use App\Tests\DataProvider;
use App\Tests\WebTestCaseWithDatabase;

class LoginControllerTest extends WebTestCaseWithDatabase
{
    /** @test */
    public function user_can_access_login_form(): void
    {
        // Given

        // When
        $crawler = $this->client->request('GET', '/login');

        // Then
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Please sign in'); 
    }

    /** @test */ 
    public function user_can_login(): void
    {
        // Given
        $user = DataProvider::getConfiguredUser($this->entityManager);

        // When
        $this->client->loginUser($user);

        // Then
        /** @var Crawler $crawlerGET */
        $crawlerGET = $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('div', 'You are logged in as');
    }
}