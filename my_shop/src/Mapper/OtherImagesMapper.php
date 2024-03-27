<?php

namespace App\Mapper;

use App\DTO\OtherImageDto;
use App\Entity\Image;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class OtherImagesMapper
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param Image[]|Image $data
     * 
     * @return OtherImageDto[]|OtherImageDto
     */
    public function map(mixed $data): mixed
    {
        if (!is_array($data)) {
            return $this->mapSingleImage($data);
        }

        $otherImageDtos = [];

        foreach($data as $image) {
            $otherImageDtos[] = $this->mapSingleImage($image);
        }
        return $otherImageDtos;
    }

    protected function mapSingleImage(Image $image): OtherImageDto
    {
        $otherImageDto = (new OtherImageDto())
            ->setName($image->getName())
            ->setFullPath($image->getFullPath())
            ->setImageDeleteUrl($this->router->generate('app_admin_image_delete', ['id' => $image->getId()], UrlGeneratorInterface::ABSOLUTE_PATH));

        return $otherImageDto;
    }
}