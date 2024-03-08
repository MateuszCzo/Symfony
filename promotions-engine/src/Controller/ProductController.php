<?php

namespace App\Controller;

use App\Cache\PromotionCache;
use App\DTO\LowestPriceEnquiry;
use App\Filter\PriceFilterInterface;
use App\Repository\ProductRepository;
use App\Service\Serializer\DTOSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    private SerializerInterface $serializer;
    private PriceFilterInterface $promotionFilter;
    private ProductRepository $productRepository;
    private PromotionCache $promotionCache;

    public function __construct(DTOSerializer $serializer,
                                PriceFilterInterface $filter,
                                ProductRepository $productRepository,
                                PromotionCache $promotionCache)
    {
        $this->serializer = $serializer;
        $this->promotionFilter = $filter;
        $this->productRepository = $productRepository;
        $this->promotionCache = $promotionCache;
    }

    #[Route('/product/{productId}/lowest-price', name: 'lowest-price', methods: ['POST'])]
    public function lowestPrice(Request $request, int $productId): Response
    {
        /** @var LowestPriceEnquiry $lowestPriceEnquiry */
        $lowestPriceEnquiry = $this->serializer->deserialize(
            $request->getContent(), 
            LowestPriceEnquiry::class,
            'json');
        $product = $this->productRepository->findOrFail($productId);
        $lowestPriceEnquiry->setProduct($product);
        $promotions = $this->promotionCache->findValidForProduct($product, $lowestPriceEnquiry->getRequestDate());
        $modifiedEnquiry = $this->promotionFilter->apply($lowestPriceEnquiry, ...$promotions);
        $responseContent = $this->serializer->serialize($modifiedEnquiry, 'json');
        return new Response($responseContent, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/product/{id}/promotions', name: 'promotions', methods: ['GET'])]
    public function promotions(int $id)
    {

    }
}
