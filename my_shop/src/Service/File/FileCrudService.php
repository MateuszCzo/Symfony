<?php

namespace App\Service\File;

use App\Entity\FileInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileCrudService
{
    private string $projectDirectory;
    private Filesystem $filesystem;

    public function __construct(string $projectDirectory, Filesystem $filesystem)
    {
        $this->projectDirectory =   $projectDirectory . '/public';
        $this->filesystem =         $filesystem;
    }

    public function create(UploadedFile $uploadedFile, string $uploadPath, FileInterface $file): FileInterface
    {
        $file->setType($uploadedFile->guessExtension())
            ->setPath($uploadPath)
            ->setName(uniqid());

        $uploadedFile->move(
            $this->projectDirectory . $uploadPath,
            $file->getName() . '.' . $file->getType(),
        );
        return $file;
    }

    public function exists(FileInterface $file): bool
    {
        return $this->filesystem->exists($this->projectDirectory . $file->getFullPath());
    }

    public function update(FileInterface $file, UploadedFile|null $uploadedFile): FileInterface
    {
        if ($uploadedFile == null) return $file;
        $this->delete($file);
        return $this->create($uploadedFile, $file->getPath(), $file);
    }

    public function delete(FileInterface $file): void
    {
        $fullFilePath = $this->projectDirectory . $file->getFullPath();
        if ($this->filesystem->exists($fullFilePath)) $this->filesystem->remove($fullFilePath);
    }
}