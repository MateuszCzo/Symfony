<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Event\AfterDTOCreatedEvant;
use App\Tests\ServiceTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class DTOSubscriberTest extends ServiceTestCase
{
    /** @test */
    public function isDTOValidatedAfterItHasBeenCreated(): void
    {
        // Given
        $dto = new LowestPriceEnquiry();
        $dto->setQuantity(-5);
        $event = new AfterDTOCreatedEvant($dto);
        /** @var EventDispatcherInterface $eventDispatcher */
        $eventDispatcher = $this->container->get('debug.event_dispatcher');

        // Expect
        $this->expectException(ValidationFailedException::class);
        $this->expectExceptionMessage('This value should be positive.');

        // When
        $eventDispatcher->dispatch($event, $event::NAME);
    }
}