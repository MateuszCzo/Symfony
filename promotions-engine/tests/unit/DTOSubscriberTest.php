<?php

namespace App\Tests\unit;

use App\DTO\LowestPriceEnquiry;
use App\Event\AfterDTOCreatedEvant;
use App\Service\ServiceException;
use App\Tests\ServiceTestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
        $this->expectException(ServiceException::class);

        // When
        $eventDispatcher->dispatch($event, $event::NAME);
    }
}