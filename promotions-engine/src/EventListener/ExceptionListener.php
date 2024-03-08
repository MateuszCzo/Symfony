<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $response = new JsonResponse([
            'type'          => 'ConstraintViolationList',
            'title'         => 'An error occured',
            'description'   => 'This value should be positive',
            'violations'    => [
                [
                    'propertyPath'  => 'quantity',
                    'message'       => 'This value should be positive',
                ]
            ]
        ]);
        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $response->setStatusCode(500);
        }
        $event->setResponse($response);
    }
}