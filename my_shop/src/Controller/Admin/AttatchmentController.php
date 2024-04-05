<?php

namespace App\Controller\Admin;

use App\Constants\AttatchmentConstants;
use App\Entity\Attatchment;
use App\Form\AttatchmentType;
use App\Form\AttatchmentUpdateType;
use App\Repository\AttatchmentRepository;
use App\Service\File\FileCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttatchmentController extends AbstractController
{
    private AttatchmentRepository $attatchmentRepository;
    private EntityManagerInterface $entityManager;
    private FileCrudService $fileCrudService;

    public function __construct(AttatchmentRepository $attatchmentRepository,
                                EntityManagerInterface $entityManager,
                                FileCrudService $fileCrudService)
    {
        $this->attatchmentRepository = $attatchmentRepository;
        $this->entityManager = $entityManager;
        $this->fileCrudService = $fileCrudService;
    }

    #[Route('/admin/attatchment/list', name: 'app_admin_attatchment_list', methods: ['GET'])]
    public function list(): Response
    {
        $attatchments = $this->attatchmentRepository->findAll();

        return $this->render('admin/attatchment/list.html.twig', [
            'attatchments' => $attatchments,
        ]);
    }

    #[Route('/admin/attatchment/create', name: 'app_admin_attatchment_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $attatchment = new Attatchment();
        $form = $this->createForm(AttatchmentType::class, $attatchment);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('admin/attatchment/create.html.twig', [
                'attatchmentCreateForm' => $form,
            ]);
        }

        $attatchment = $this->fileCrudService->create(
            $form->get('file')->getData(),
            AttatchmentConstants::FILE_UPLOAD_PATH,
            $attatchment
        );

        $this->entityManager->persist($attatchment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Attatchment created sucessfuly');
        return $this->redirectToRoute('app_admin_attatchment_list');
    }

    #[Route('/admin/attatchment/update/{id}', name: 'app_admin_attatchment_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $attatchment = $this->attatchmentRepository->find($id);

        if (!$attatchment) {
            $this->addFlash('error', 'Can not find attatchment');
            return $this->redirectToRoute('app_admin_attatchment_list');
        }

        $form = $this->createForm(AttatchmentUpdateType::class, $attatchment);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('admin/attatchment/update.html.twig', [
                'attatchmentUpdateForm' => $form,
            ]);
        }

        $attatchment = $this->fileCrudService->update(
            $attatchment,
            $form->get('file')->getData()
        );

        $this->entityManager->persist($attatchment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Attatchment updated sucessfuly');
        return $this->redirectToRoute('app_admin_attatchment_list');
    }

    #[Route('/admin/attatchment/delete/{id}', name: 'app_admin_attatchment_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $attatchment = $this->attatchmentRepository->find($id);

        if (!$attatchment) {
            $this->addFlash('error', 'Can not find attatchment');
            return $this->redirectToRoute('app_admin_attatchment_list');
        }

        $this->fileCrudService->delete($attatchment);

        $this->entityManager->remove($attatchment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Attatchment created sucessfuly');
        return $this->redirectToRoute('app_admin_attatchment_list');
    }
}