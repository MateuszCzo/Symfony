<?php

namespace App\Controller;

use App\Repository\ManufacturerRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ManufacturerController extends AbstractController
{
    private ManufacturerRepository $manufacturerRepository;
    private ProductRepository $productRepository;

    public function __construct(ManufacturerRepository $manufacturerRepository,
                                ProductRepository $productRepository)
    {
        $this->manufacturerRepository = $manufacturerRepository;
        $this->productRepository = $productRepository;
    }

    #[Route('/manufacturer/{id}', name: 'app_manufacturer', methods: ['GET'])]
    public function index(int $id): Response
    {
        $manufacturer = $this->manufacturerRepository->find($id);
        
        $products = $this->productRepository->findBy(['manufacturer' => $manufacturer, 'active' => true]);

        return $this->render('manufacturer.html.twig', [
            'manufacturer' => $manufacturer,
            'products' => $products,
        ]);
    }
}