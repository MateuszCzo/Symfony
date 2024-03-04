<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieFormType;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MoviesController extends AbstractController
{
    private $em;
    private $movieRepository;

    public function __construct(MovieRepository         $movieRepository,
                                EntityManagerInterface  $em) {
        $this->movieRepository =    $movieRepository;
        $this->em =                 $em;
    }

    #[Route('/movies', name: 'read_movies')]
    public function index(): Response
    {
        $movies = $this->movieRepository->findAll();
        return $this->render('movies/index.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/movies/create', methods:['GET', 'POST'], name: 'create_movie')]
    public function create(Request $request): Response
    {
        $movie = new Movie();
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleCreateForm($form);
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            return $this->redirectToRoute('read_movies');
        }
        return $this->render('movies/create.html.twig', [
            'form' => $form,
        ]);
    }

    protected function handleCreateForm(FormInterface $form): void {
        $newMovie = $form->getData();
        $imagePath = $form->get('imagePath')->getData();
        if ($imagePath) {
            $newFileName = uniqid() . '.' . $imagePath->guessExtension();
            $imagePath->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $newFileName,
            );
            $newMovie->setImagePath('/uploads/' . $newFileName);
        }
        $this->em->persist($newMovie);
        $this->em->flush();
    }

    #[Route('/movies/{id}', methods:['GET'], name: 'read_movie')]
    public function read(Int $id): Response
    {
        $movie = $this->movieRepository->find($id);
        return $this->render('movies/read.html.twig', [
            'movie' => $movie,
        ]);
    }

    #[Route('/movies/update/{id}', methods:['GET', 'POST'], name: 'update_movie')]
    public function update(Int $id, Request $request): Response
    {
        $movie = $this->movieRepository->find($id);
        $form = $this->createForm(MovieFormType::class, $movie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->handleUpdateForm($form, $movie);
            } catch (FileException $e) {
                return new Response($e->getMessage());
            }
            return $this->redirectToRoute('read_movies');
        }
        return $this->render('movies/update.html.twig', [
            'form' => $form,
        ]);
    }

    protected function handleUpdateForm(FormInterface $form, Movie &$movie): void {
        $imagePath = $form->get('imagePath')->getData();
        if ($imagePath) {
            $newFileName = uniqid() . '.' . $imagePath->guessExtension();
            $imagePath->move(
                $this->getParameter('kernel.project_dir') . '/public/uploads',
                $newFileName,
            );
            $movie->setImagePath('/uploads/' . $newFileName);
        }
        $movie->setTitle($form->get('title')->getData());
        $movie->setReleaseYear($form->get('releaseYear')->getData());
        $movie->setDescription($form->get('description')->getData());
        $this->em->flush();
    }

    #[Route('/movies/delete/{id}', methods:['GET', 'DELETE'], name: 'delete_movie')]
    public function delete(Int $id): Response
    {
        $movie = $this->movieRepository->find($id);
        $this->em ->remove($movie);
        $this->em->flush();
        return $this->redirectToRoute('read_movies');
    }
}
