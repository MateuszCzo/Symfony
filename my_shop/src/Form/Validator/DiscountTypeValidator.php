<?php

namespace App\Form\Validator;

use App\Constants\DiscountConstants;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

class DiscountTypeValidator
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function validate(FormInterface $form, int $action = DiscountConstants::ACTION_CREATE): bool
    {
        $type = $form->get('type')->getData();

        $class = str_replace('_', '', ucwords($type, '_')) . 'Validator';

        /** @var DiscountTypeValidatorInterface $validator */
        $validator = $this->container->get('App\\Form\\Validator\\' . $class);

        return $validator->validate($form, $action);
    }
}