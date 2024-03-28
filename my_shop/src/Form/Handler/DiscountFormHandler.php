<?php

namespace App\Form\Handler;

use App\Entity\Discount;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;

class DiscountFormHandler
{
    private ContainerInterface $container;
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function handle(FormInterface $form, Discount $discount): Discount
    {
        // todo get class from container
        $type = $form->get('type')->getData();

        $class = str_replace('_', '', ucwords($type, '_')) . 'Handler';

        /** @var DiscountFormHandlerInterface $validator */
        $handler = $this->container->get('App\\Form\\Handler\\' . $class);

        return $handler->handle($form, $discount);
    }
}