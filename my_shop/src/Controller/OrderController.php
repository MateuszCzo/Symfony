<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private OrderRepository $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    #[Route('/order/{id}', name: 'app_order', methods: ['GET'])]
    public function index(int $id): Response
    {
        $order = $this->orderRepository->find($id);

        /** @var User $user */
        $user = $this->getUser();

        if (!$order || $order->getUser()->getId() == $user->getId()) {
            $this->addFlash('error', 'Order not found');
            return $this->redirectToRoute('app');
        }

        return $this->render('order.html.twig', [
            'order' => $order,
        ]);
    }

    //todo ask for status change
}