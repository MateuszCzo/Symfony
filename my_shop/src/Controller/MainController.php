<?php

namespace App\Controller;

use App\Constants\MainConstants;
use App\Repository\CategoryRepository;
use App\Repository\ManufacturerRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    private ProductRepository $productRepository;
    private CategoryRepository $categoryRepository;
    private ManufacturerRepository $manufacturerRepository;

    public function __construct(ProductRepository $productRepository,
                                CategoryRepository $categoryRepository,
                                ManufacturerRepository $manufacturerRepository)
    {
        $this->productRepository = $productRepository;
        $this->categoryRepository = $categoryRepository;
        $this->manufacturerRepository = $manufacturerRepository;
    }

    #[Route(path: '/', name: 'app')]
    public function main(): Response
    {
        $mainCategories = $this->categoryRepository->findBy(['parent' => null]);
        $randomProducts = $this->productRepository->getRandomProducts(MainConstants::NUMBER_OF_DISPLAYED_PRODUCTS);
        $manufacturers = $this->manufacturerRepository->findAll();

        return $this->render('main.html.twig', [
            'categories' => $mainCategories,
            'products' => $randomProducts,
            'manufacturers' => $manufacturers,
        ]);
    }
}