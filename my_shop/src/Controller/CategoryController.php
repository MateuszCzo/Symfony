<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ProductRepository $productRepository;
    
    public function __construct(CategoryRepository $categoryRepository,
                                ProductRepository $productRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->productRepository = $productRepository;
    }

    #[Route('/category/{id}', name: 'app_category', methods: ['GET'])]
    public function index(int $id): Response
    {
        $category = $this->categoryRepository->find($id);

        $products = $this->productRepository->findAllByCategories([$category]);

        return $this->render('category.html.twig', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}