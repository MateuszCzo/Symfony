<?php

namespace App\Controller\Admin;

use App\Entity\Delivery;
use App\Form\DeliveryType;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DeliveryController extends AbstractController
{
    private DeliveryRepository $deliveryRepository;
    private EntityManagerInterface $entityManager;
    
    public function __construct(DeliveryRepository $deliveryRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->deliveryRepository = $deliveryRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/delivery/list', name: 'app_admin_delivery_list', methods: ['GET'])]
    public function list(): Response
    {
        $deliveries = $this->deliveryRepository->findAll();

        return $this->render('delivery/list.html.twig', [
            'deliveries' => $deliveries,
        ]);
    }

    #[Route('/admin/delivery/create', name: 'app_admin_delivery_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $delivery = new Delivery();
        $form = $this->createForm(DeliveryType::class, $delivery);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('delivery/create.html.twig', [
                'deliveryCreateForm' => $form,
            ]);
        }
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        $this->addFlash('success', 'Delivery created successfuly');

        return $this->redirectToRoute('app_admin_delivery_list');
    }

    #[Route('/admin/delivery/update/{id}', name: 'app_admin_delivery_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $delivery = $this->deliveryRepository->find($id);

        if (!$delivery) {
            $this->addFlash('error', 'Can not find delivery');

            return $this->redirectToRoute('app_admin_delivery_list');   
        }

        $form = $this->createForm(DeliveryType::class, $delivery);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('delivery/update.html.twig', [
                'deliveryUpdateForm' => $form,
            ]);
        }
        $this->entityManager->persist($delivery);
        $this->entityManager->flush();

        $this->addFlash('success', 'Delivery updated successfuly');

        return $this->redirectToRoute('app_admin_delivery_list');
    }

    #[Route('/admin/delivery/delete/{id}', name: 'app_admin_delivery_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $delivery = $this->deliveryRepository->find($id);

        if (!$delivery) {
            $this->addFlash('error', 'Can not find delivery');

            return $this->redirectToRoute('app_admin_delivery_list');   
        }

        $this->entityManager->remove($delivery);
        $this->entityManager->flush();

        $this->addFlash('success', 'Delivery remover successfuly');

        return $this->redirectToRoute('app_admin_delivery_list');
    }

}