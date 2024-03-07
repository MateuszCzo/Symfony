<?php

namespace App\Controller;

use App\DTO\LowestPriceEnquiry;
use App\Filter\PromotionFilterInterface;
use App\Repository\ProductRepository;
use App\Repository\PromotionRepository;
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
    private PromotionFilterInterface $promotionFilter;
    private ProductRepository $productRepository;
    private PromotionRepository $promotionRepository;

    public function __construct(DTOSerializer $serializer,
                                PromotionFilterInterface $filter,
                                ProductRepository $productRepository,
                                PromotionRepository $promotionRepository)
    {
        $this->serializer = $serializer;
        $this->promotionFilter = $filter;
        $this->productRepository = $productRepository;
        $this->promotionRepository = $promotionRepository;
    }

    #[Route('/product/{productId}/lowest-price', name: 'lowest-price', methods: ['POST'])]
    public function lowestPrice(Request $request, int $productId): Response
    {
        if ($request->headers->has('force_fail')) {
            return new JsonResponse([
                'error' => 'Promotions Engine failure message',
            ], $request->headers->get('force_fail'));
        }
        /** @var LowestPriceEnquiry $lowestPriceEnquiry */
        $lowestPriceEnquiry = $this->serializer->deserialize(
            $request->getContent(), 
            LowestPriceEnquiry::class,
            'json');
        $product = $this->productRepository->find($productId);
        $lowestPriceEnquiry->setProduct($product);
        $promotions = $this->promotionRepository->findValidForProduct(
            $product,
            date_create_immutable($lowestPriceEnquiry->getRequestDate())
        );

        dd($promotions);

        $modifiedEnquiry = $this->promotionFilter->apply($lowestPriceEnquiry, $promotions);
        $responseContent = $this->serializer->serialize($modifiedEnquiry, 'json');
        return new Response($responseContent, 200);
    }

    #[Route('/product/{id}/promotions', name: 'promotions', methods: ['GET'])]
    public function promotions(int $id)
    {

    }
}
