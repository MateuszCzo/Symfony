<?php

namespace App\Controller;

use App\DTO\LowestPriceEnquiry;
use App\Filter\PromotionFilterInterface;
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

    public function __construct(DTOSerializer $serializer,
                                PromotionFilterInterface $filter)
    {
        $this->serializer = $serializer;
        $this->promotionFilter = $filter;
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
        $modifiedEnquiry = $this->promotionFilter->apply($lowestPriceEnquiry);
        $responseContent = $this->serializer->serialize($modifiedEnquiry, 'json');
        return new Response($responseContent, 200);
    }

    #[Route('/product/{id}/promotions', name: 'promotions', methods: ['GET'])]
    public function promotions(int $id)
    {

    }
}
