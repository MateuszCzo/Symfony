<?php

namespace App\Controller\Admin;

use App\Constants\CategoryConstants;
use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\CategoryUpdateType;
use App\Form\Validator\CategoryParentValidator;
use App\Repository\CategoryRepository;
use App\Service\Image\ImageCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private ImageCrudService $imageCrudService;
    private EntityManagerInterface $entityManager;

    public function __construct(CategoryRepository $categoryRepository,
                                ImageCrudService $imageCrudService,
                                EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->imageCrudService = $imageCrudService;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/category/list', name: 'app_admin_category_list', methods: ['GET'])]
    public function list(): Response
    {
        $categories = $this->categoryRepository->findAll();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }
    
    #[Route('/admin/category/create', name: 'app_admin_category_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('category/create.html.twig', [
                'categoryCreateFrom' => $form,
            ]);
        }

        $image = $this->imageCrudService->create(
            $form->get('image')->getData(),
            CategoryConstants::IMAGE_UPLOAD_PATH
        );

        $category->setImage($image);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_category_list');
    }

    #[Route('/admin/category/update/{id}', name: 'app_admin_category_update', methods: ['GET', 'POST'])]
    public function update(int $id, Request $request, CategoryParentValidator $categoryParentValidator): Response
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            $this->addFlash('error', 'Can not find category.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        $form = $this->createForm(CategoryUpdateType::class, $category);
        $form->handleRequest($request);

        if (!($form->isSubmitted() && $form->isValid())) {
            return $this->render('category/update.html.twig', [
                'categoryUpdateFrom' => $form,
            ]);
        }

        if (!$categoryParentValidator->validate($category)) {
            $this->addFlash('error', 'Invalid parent.');
            return $this->redirectToRoute('app_admin_category_update', ['id' => $category->getId()]);
        }

        $updatedImage = $form->get('image')->getData();

        if ($updatedImage) {
            $image = $this->imageCrudService->update(
                $category->getImage(),
                $updatedImage
            );
            $category->setImage($image);
        }

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_category_list');
    }

    #[Route('/admin/category/delete/{id}', name: 'app_admin_category_delete', methods: ['GET'])]
    public function delete(int $id): Response
    {
        /** @var Category $category */
        $category = $this->categoryRepository->find($id);

        if (!$category) {
            $this->addFlash('error', 'Can not find category.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        if (count($category->getChildren())) {
            $this->addFlash('error', 'Can not delete parent category.');
            return $this->redirectToRoute('app_admin_category_list');
        }

        $this->imageCrudService->delete($category->getImage());

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_admin_category_list');
    }
}