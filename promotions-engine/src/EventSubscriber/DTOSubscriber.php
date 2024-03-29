<?php

namespace App\EventSubscriber;

use App\Event\AfterDTOCreatedEvant;
use App\Service\ServiceException;
use App\Service\ValidationExceptionData;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOSubscriber implements EventSubscriberInterface
{
    private $validator;

    public function __construct(ValidatorInterface $validator) {
        $this->validator = $validator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AfterDTOCreatedEvant::NAME => [
                ['validateDTO', 1],
                //['doSomethingElse', 100]
            ]
        ];
    }

    public function validateDTO(AfterDTOCreatedEvant $event): void
    {
        $dto = $event->getDto();
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $validationExceptionData = new ValidationExceptionData(422, 'ConstraintViolationList', $errors);
            throw new ServiceException($validationExceptionData);
        }
    }

    public function doSomethingElse(): void
    {
        dd('Doing something else.');
    }
}