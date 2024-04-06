<?php

namespace App\Form\Validator;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class NewPasswordFormValidator
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function validate(PasswordAuthenticatedUserInterface $user, FormInterface $form): bool
    {
        $oldPassword = $form->get('oldPassword')->getData();
        $newPassword = $form->get('newPassword')->getData();
        $repeatNewPassword = $form->get('repeatNewPassword')->getData();

        if (!$this->userPasswordHasher->isPasswordValid($user, $oldPassword)) {
            $form->addError(new FormError('Invalid password'));

            return false;
        }
        if ($newPassword != $repeatNewPassword) {
            $form->addError(new FormError('wrong new password'));

            return false;
        }
        return true;
    }
}