<?php

namespace App\Controller\Admin;

use App\Constants\ProductConstants;
use App\Entity\Image;
use App\Entity\Product;
use App\Form\ProductType;
use App\Form\ProductUpdateType;
use App\Mapper\OtherImagesMapper;
use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Service\File\FileCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    private ProductRepository $productRepository;
    private FileCrudService $fileCrudService;
    private EntityManagerInterface $entityManager;
    private ImageRepository $imageRepository;
    private Filesystem $filesystem;
    private OtherImagesMapper $otherImagesMapper;
    private string $projectDirectory;

    public function __construct(ProductRepository $productRepository,
                                FileCrudService $fileCrudService,
                                EntityManagerInterface $entityManager,
                                ImageRepository $imageRepository,
                                Filesystem $filesystem,
                                OtherImagesMapper $otherImagesMapper,
                                string $projectDirectory)
    {
        $this->productRepository = $productRepository;
        $this->fileCrudService = $fileCrudService;
        $this->entityManager = $entityManager;
        $this->imageRepository = $imageRepository;
        $this->filesystem = $filesystem;
        $this->otherImagesMapper = $otherImagesMapper;
        $this->projectDirectory = $projectDirectory;
    }

    #[Route('/admin/product/list', name: 'app_admin_product_list', methods: ['GET'])]
    public function list(): Response
    {
        $products = $this->productRepository->findAll();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/admin/product/create', name: 'app_admin_product_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('product/create.html.twig', [
                'productCreateForm' => $form,
            ]);
        }

        /** @var Image $image */
        $image = $this->fileCrudService->create(
            $form->get('image')->getData(),
            ProductConstants::IMAGE_UPLOAD_PATH,
            new Image(),
        );

        $product->setImage($image);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        foreach ($form->get('images')->getData() as $uploadedimage) {
            /** @var Image $image */
            $image = $this->fileCrudService->create(
                $uploadedimage,
                ProductConstants::IMAGE_UPLOAD_PATH . '/' . $product->getId(),
                new Image(),
            );

            $image->setProduct($product);

            $this->entityManager->persist($image);
        }

        $this->entityManager->flush();

        $this->addFlash('success', 'Product created successfuly');
        return $this->redirectToRoute('app_admin_product_list');
    }

    #[Route('/admin/product/update/{id}', name: 'app_admin_product_update', methods: ['GET', 'POST'])]
    public function update(Request $request, int $id): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('error', 'can not find product');
            return $this->redirectToRoute('app_admin_product_list');
        }

        $form = $this->createForm(ProductUpdateType::class, $product);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            $images = $this->imageRepository->findImagesByProduct($product);

            /** @var OtherImagesDto $otherImagesDto */
            $otherImagesDto = $this->otherImagesMapper->map($images);

            return $this->render('product/update.html.twig', [
                'productUpdateForm' => $form,
                'otherImagesDto' => $otherImagesDto,
            ]);
        }

        /** @var Image $image */
        $image = $this->fileCrudService->update(
            $product->getImage(),
            $form->get('image')->getData()
        );

        $product->setImage($image);

        foreach ($form->get('images')->getData() as $uploadedimage) {
            /** @var Image $image */
            $image = $this->fileCrudService->create(
                $uploadedimage,
                ProductConstants::IMAGE_UPLOAD_PATH . '/' . $product->getId(),
                new Image(),
            );

            $image->setProduct($product);

            $this->entityManager->persist($image);
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Product created successfuly');
        return $this->redirectToRoute('app_admin_product_list');
    }

    #[Route('/admin/product/delete/{id}', name: 'app_admin_product_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        $product = $this->productRepository->find($id);

        if (!$product) {
            $this->addFlash('error', 'can not find product');
            return $this->redirectToRoute('app_admin_product_list');
        }

        /** @var Image[] $images */
        $images = $this->imageRepository->findImagesByProduct($product);

        foreach($images as $image) {
            $this->entityManager->remove($image);
        }

        $this->fileCrudService->delete($product->getImage());

        $this->filesystem->remove($this->projectDirectory . '/public' . ProductConstants::IMAGE_UPLOAD_PATH . '/' . $product->getId());

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->addFlash('success', 'Product created successfuly');
        return $this->redirectToRoute('app_admin_product_list');
    }
}