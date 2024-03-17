<?php

namespace App\Entity;

interface UserEmailVerifierInterface
{
    public function getId(): int;

    public function getEmail(): string;

    public function setIsVerified(bool $isVerified): static;
}