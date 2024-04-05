<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/admin/main', 'app_admin_main', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/main/index.html.twig');
    }
}