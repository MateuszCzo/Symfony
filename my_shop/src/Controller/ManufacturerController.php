<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManufacturerController extends AbstractController
{
    #[Route('/manufacturer/{id}', name: 'app_manufacturer', methods: ['GET'])]
    public function index(int $id): Response
    {
        return $this->render('todo');
    }
}