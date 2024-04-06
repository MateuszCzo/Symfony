<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    #[Route('/product/{id}', name: 'app_product', methods: ['GET'])]
    public function index(int $id): Response
    {
        $product = $this->productRepository->find($id);

        return $this->render('product.html.twig', [
            'product' => $product,
        ]);
    }
}