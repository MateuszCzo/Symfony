<?php

namespace App\Service\Image;

use App\Entity\Image;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageCrudService
{
    private string $projectDirectory;
    private Filesystem $filesystem;

    public function __construct(string $projectDirectory, Filesystem $filesystem)
    {
        $this->projectDirectory =   $projectDirectory . '/public';
        $this->filesystem =         $filesystem;
    }

    public function create(UploadedFile $uploadedFile, string $uploadPath, Image $image = null): Image
    {
        if (!$image) $image = new Image();

        $image->setType($uploadedFile->guessExtension())
            ->setPath($uploadPath)
            ->setName(uniqid());

        $uploadedFile->move(
            $this->projectDirectory . $uploadPath,
            $image->getName() . '.' . $image->getType(),
        );
        return $image;
    }

    public function exists(Image $image): bool
    {
        $fullImagePath = $this->projectDirectory . $image->getPath() . '/' . $image->getName() . '.' . $image->getType();

        return $this->filesystem->exists($fullImagePath);
    }

    public function update(Image $image, UploadedFile $uploadedFile): Image
    {
        $this->delete($image);
        return $this->create($uploadedFile, $image->getPath(), $image);
    }

    public function delete(Image $image): void
    {
        $fullImagePath = $this->projectDirectory . $image->getPath() . '/' . $image->getName() . '.' . $image->getType();
        if ($this->filesystem->exists($fullImagePath)) $this->filesystem->remove($fullImagePath);
    }
}