<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/product/{productId}/lowest-price', name: 'lowest-price', methods: ['POST'])]
    public function lowestPrice(Request $request, int $productId): Response
    {
        if ($request->headers->has('force_fail')) {
            return new JsonResponse([
                'error' => 'Promotions Engine failure message',
            ], $request->headers->get('force_fail'));
        }

        return new JsonResponse([
            'quantity' => 5,
            'request_location' => 'UK',
            'voucher_code' => 'OUI812',
            'request_data' => '2022-04-04',
            'product_id' => $productId,
            'price' => 100,
            'discounted_price' => 50,
            'promotion_id' => 3,
            'promotion_name' => 'Black Friday half price sale',
        ], 200);
    }

    #[Route('/product/{id}/promotions', name: 'promotions', methods: ['GET'])]
    public function promotions(int $id)
    {

    }
}
