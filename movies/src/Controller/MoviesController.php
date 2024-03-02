<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    #[Route('/movies', name: 'app_movies')]
    public function index(): Response
    {
        $movies = ['asd', 'sdf', 'dfg'];
        return $this->render('index.html.twig', [
            'title' => 'asd',
            'movies' => $movies,
        ]);
    }

    #[Route('/movies2', name: 'app_movies2')]
    public function index2(): Response
    {
        return $this->render('index2.html.twig');
    }
}
