<?php

namespace App\DTO;

class OtherImageDto
{
    private string $name;
    
    private string $fullPath;

    private string $imageDeleteUrl;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getFullPath(): string
    {
        return $this->fullPath;
    }

    public function setFullPath(string $fullPath): static
    {
        $this->fullPath = $fullPath;

        return $this;
    }

    public function getImageDeleteUrl(): string
    {
        return $this->imageDeleteUrl;
    }

    public function setImageDeleteUrl(string $imageDeleteUrl): static
    {
        $this->imageDeleteUrl = $imageDeleteUrl;

        return $this;
    }
}