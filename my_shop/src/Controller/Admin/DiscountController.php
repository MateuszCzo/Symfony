<?php

namespace App\Controller\Admin;

use App\Constants\DiscountConstants;
use App\Entity\Discount;
use App\Form\DiscountType;
use App\Form\Handler\DiscountFormHandler;
use App\Form\Validator\DiscountTypeValidator;
use App\Repository\DiscountRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscountController extends AbstractController
{
    private DiscountRepository $discountRepository;
    private DiscountTypeValidator $discountTypeValidator;
    private EntityManagerInterface $entityManager;
    private DiscountFormHandler $discountFormHandler;

    public function __construct(DiscountRepository $discountRepository,
                                DiscountTypeValidator $discountTypeValidator,
                                EntityManagerInterface $entityManager,
                                DiscountFormHandler $discountFormHandler)
    {
        $this->discountRepository = $discountRepository;
        $this->discountTypeValidator = $discountTypeValidator;
        $this->entityManager = $entityManager;
        $this->discountFormHandler = $discountFormHandler;
    }

    #[Route('/admin/discount/list', name: 'app_admin_discount_list', methods: ['GET'])]
    public function list(): Response
    {
        $discounts = $this->discountRepository->findAll();

        return $this->render('admin/discount/list.html.twig', [
            'discounts' => $discounts,
        ]);
    }

    #[Route('/admin/discount/create', name: 'app_admin_discount_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $discount = new Discount();
        $form = $this->createForm(DiscountType::class, $discount);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid() && $this->discountTypeValidator->validate($form))) {
            return $this->render('admin/discount/create.html.twig', [
                'discountCreateForm' => $form,
            ]);
        }

        $discount = $this->discountFormHandler->handle($form, $discount);
        
        $this->entityManager->persist($discount);
        $this->entityManager->flush();

        $this->addFlash('success', 'Discount created successfuly');
        return $this->redirectToRoute('app_admin_discount_list');
    }

    #[Route('/admin/discount/update/{id}', name: 'app_admin_discount_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $discount = $this->discountRepository->find($id);
        $form = $this->createForm(DiscountType::class, $discount, [
            'cartValue' => isset($discount->getCriteria()['cartValue']) ? $discount->getCriteria()['cartValue'] : ''
        ]);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid() && 
            $this->discountTypeValidator->validate($form, DiscountConstants::ACTION_UPDATE))) {
            return $this->render('admin/discount/update.html.twig', [
                'discountUpdateForm' => $form,
            ]);
        }

        $discount = $this->discountFormHandler->handle($form, $discount);
        
        $this->entityManager->persist($discount);
        $this->entityManager->flush();

        $this->addFlash('success', 'Discount updated successfuly');
        return $this->redirectToRoute('app_admin_discount_list');
    }

    #[Route('/admin/discount/delete/{id}', name: 'app_admin_discount_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $discount = $this->discountRepository->find($id);

        $this->entityManager->remove($discount);
        $this->entityManager->flush();

        $this->addFlash('success', 'Discount deleted successfuly');
        return $this->redirectToRoute('app_admin_discount_list');
    }
}