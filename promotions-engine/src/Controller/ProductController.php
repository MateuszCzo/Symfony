<?php

namespace App\Controller;

use App\DTO\LowestPriceEnquiry;
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

    public function __construct(DTOSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/product/{productId}/lowest-price', name: 'lowest-price', methods: ['POST'])]
    public function lowestPrice(Request $request, int $productId): Response
    {
        if ($request->headers->has('force_fail')) {
            return new JsonResponse([
                'error' => 'Promotions Engine failure message',
            ], $request->headers->get('force_fail'));
        }

        // 1. Deserialize json data into a EnquiryDTO
        /** @var LowestPriceEnquiry $lowestPriceEnquiry */
        $lowestPriceEnquiry = $this->serializer->deserialize(
            $request->getContent(), 
            LowestPriceEnquiry::class,
            'json');
        // 2. Pass the Enquiry into a promotions filter
            // the appropriate promotion will be applied

        // 3. Return the modified Enquiry
        $lowestPriceEnquiry->setDiscountedPrice(50);
        $lowestPriceEnquiry->setPrice(100);
        $lowestPriceEnquiry->setPromotionId(3);
        $lowestPriceEnquiry->setPromotionName('Black Friday half price sale');
        $responseContent = $this->serializer->serialize($lowestPriceEnquiry, 'json');
        return new Response($responseContent, 200);
    }

    #[Route('/product/{id}/promotions', name: 'promotions', methods: ['GET'])]
    public function promotions(int $id)
    {

    }
}
