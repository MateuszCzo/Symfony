<?php

namespace App\Entity;

interface FileInterface
{
    public function getName(): string;

    public function setName(string $name): static;

    public function getType(): string;

    public function setType(string $type): static;

    public function getPath(): string;

    public function setPath(string $path): static;

    public function getFullPath(): string;
}