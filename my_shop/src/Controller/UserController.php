<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Form\Validator\NewPasswordFormValidator;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private OrderRepository $orderRepository;
    private NewPasswordFormValidator $newPasswordFormValidator;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(OrderRepository $orderRepository,
                                NewPasswordFormValidator $newPasswordFormValidator,
                                UserPasswordHasherInterface $userPasswordHasher,
                                EntityManagerInterface $entityManager)
    {
        $this->orderRepository = $orderRepository;
        $this->newPasswordFormValidator = $newPasswordFormValidator;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route('/user', name: 'app_user', methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/user/change-password', name: 'app_user_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(NewPasswordType::class);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid() && 
            $this->newPasswordFormValidator->validate($user, $form))) {
            return $this->render('user_change_password.html.twig', [
                'changePasswordForm' => $form,
            ]);
        }
        $newPassowrd = $form->get('newPassword')->getData();
        
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $newPassowrd));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->addFlash('success', 'Password changed successfully');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/user/history', name: 'app_user_history', methods: ['GET'])]
    public function history(): Response
    {
        $user = $this->getUser();

        $orders = $this->orderRepository->findBy(['user' => $user]);

        return $this->render('user_history.html.twig', [
            'orders' => $orders,
        ]);
    }
}