<?php

namespace App\Controller\Admin;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private PaymentRepository $paymentRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(PaymentRepository $paymentRepository,
                                EntityManagerInterface $entityManager)
    {
        $this->paymentRepository = $paymentRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/payment/list', name: 'app_admin_payment_list', methods: ['GET'])]
    public function list(): Response
    {
        $payments = $this->paymentRepository->findAll();

        return $this->render('payment/list.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/admin/payment/create', name: 'app_admin_payment_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('payment/create.html.twig', [
                'paymentCreateForm' => $form,
            ]);
        }
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Payment created successfuly');

        return $this->redirectToRoute('app_admin_payment_list');
    }

    #[Route('/admin/payment/update/{id}', name: 'app_admin_payment_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            $this->addFlash('error', 'Can not find payment');

            return $this->redirectToRoute('app_admin_payment_list');
        }

        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('payment/update.html.twig', [
                'paymentUpdateForm' => $form,
            ]);
        }
        $this->entityManager->persist($payment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Payment updated successfuly');

        return $this->redirectToRoute('app_admin_payment_list');
    }

    #[Route('/admin/payment/delete/{id}', name: 'app_admin_payment_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $payment = $this->paymentRepository->find($id);

        if (!$payment) {
            $this->addFlash('error', 'Can not find payment');

            return $this->redirectToRoute('app_admin_payment_list');
        }

        $this->entityManager->remove($payment);
        $this->entityManager->flush();

        $this->addFlash('success', 'Payment removed successfuly');

        return $this->redirectToRoute('app_admin_payment_list');
    }
}