<?php

namespace App\Controller\Admin;

use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ImageController extends AbstractController
{
    private ImageRepository $imageRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ImageRepository $imageRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->imageRepository = $imageRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/image/delete/{id}', 'app_admin_image_delete', methods: ['GET'])]
    public function delete(int $id): JsonResponse
    {
        $image = $this->imageRepository->find($id);

        if (!$image) {
            return new JsonResponse('Can not find Image', 422);
        }

        if (!$image->getProduct()) {
            return new JsonResponse('Invalid image id', 422);
        }

        $this->entityManager->remove($image);
        $this->entityManager->flush();

        return new JsonResponse('Image deleted successfuly', 200);
    }
}