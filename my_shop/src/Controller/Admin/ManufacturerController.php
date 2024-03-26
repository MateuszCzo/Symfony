<?php

namespace App\Controller\Admin;

use App\Constants\ManufacturerConstants;
use App\Entity\Manufacturer;
use App\Form\ManufacturerType;
use App\Repository\ManufacturerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Image;
use App\Form\ManufacturerUpdateType;
use App\Service\File\FileCrudService;

class ManufacturerController extends AbstractController
{
    private ManufacturerRepository $manufacturerRepository;
    private EntityManagerInterface $entityManager;
    private FileCrudService $fileCrudService;

    public function __construct(ManufacturerRepository $manufacturerRepository,
                                EntityManagerInterface $entityManager,
                                FileCrudService $fileCrudService)
    {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->entityManager = $entityManager;
        $this->fileCrudService = $fileCrudService;
    }

    #[Route('/admin/manufacturer/list', name: 'app_admin_manufacturer_list', methods: ['GET'])]
    public function list(): Response
    {
        $manufacturers = $this->manufacturerRepository->findAll();

        return $this->render('manufacturer/list.html.twig', [
            'manufacturers' => $manufacturers,
        ]);
    }

    #[Route('/admin/manufacturer/create', name: 'app_admin_manufacturer_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $manufacturer = new Manufacturer();

        $form = $this->createForm(ManufacturerType::class, $manufacturer);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('manufacturer/create.html.twig', [
                'manufacturerCreateForm' => $form,
            ]);
        }

        /** @var Image $image */
        $image = $this->fileCrudService->create(
            $form->get('image')->getData(),
            ManufacturerConstants::IMAGE_UPLOAD_PATH,
            new Image(),
        );

        $manufacturer->setImage($image);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();

        $this->addFlash('success', 'Manufacturer successfuly created');

        return $this->redirectToRoute('app_admin_manufacturer_list');
    }

    #[Route('/admin/manufacturer/update/{id}', name: 'app_admin_manufacturer_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        if (!$manufacturer) {
            $this->addFlash('error', 'Can not find manufacturer');

            return $this->redirectToRoute('app_admin_manufacturer_list');
        }

        $form = $this->createForm(ManufacturerUpdateType::class, $manufacturer);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('manufacturer/update.html.twig', [
                'manufacturerUpdateForm' => $form,
            ]);
        }

        $updatedImage = $form->get('image')->getData();

        if ($updatedImage) {
            $image = $this->fileCrudService->update(
                $manufacturer->getImage(),
                $updatedImage,
                new Image(),
            );
            $manufacturer->setImage($image);
        }

        $this->entityManager->persist($manufacturer);
        $this->entityManager->flush();
        
        $this->addFlash('success', 'Manufacturer successfuly updated');

        return $this->redirectToRoute('app_admin_manufacturer_list');
    }

    #[Route('/admin/manufacturer/delete/{id}', name: 'app_admin_manufacturer_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $manufacturer = $this->manufacturerRepository->find($id);

        if (!$manufacturer) {
            $this->addFlash('error', 'Can not find manufacturer');

            return $this->redirectToRoute('app_admin_manufacturer_list');
        }

        $this->fileCrudService->delete($manufacturer->getImage());

        $this->entityManager->remove($manufacturer);
        $this->entityManager->flush();

        $this->addFlash('success', 'Manufacturer successfuly deleted');

        return $this->redirectToRoute('app_admin_manufacturer_list');
    }
}