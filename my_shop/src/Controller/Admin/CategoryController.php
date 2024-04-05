<?php

namespace App\Controller\Admin;

use App\Constants\CategoryConstants;
use App\Entity\Category;
use App\Entity\Image;
use App\Form\CategoryType;
use App\Form\CategoryUpdateType;
use App\Form\Validator\CategoryParentValidator;
use App\Repository\CategoryRepository;
use App\Service\File\FileCrudService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private FileCrudService $fileCrudService;
    private EntityManagerInterface $entityManager;

    public function __construct(CategoryRepository $categoryRepository,
                                FileCrudService $fileCrudService,
                                EntityManagerInterface $entityManager)
    {
        $this->categoryRepository = $categoryRepository;
        $this->fileCrudService = $fileCrudService;
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/category/list', name: 'app_admin_category_list', methods: ['GET'])]
    public function list(): Response
    {
        /** @var Category[] $categories */
        $categories = $this->categoryRepository->findAll();

        return $this->render('admin/category/list.html.twig', [
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
            return $this->render('admin/category/create.html.twig', [
                'categoryCreateForm' => $form,
            ]);
        }

        /** @var Image $image */
        $image = $this->fileCrudService->create(
            $form->get('image')->getData(),
            CategoryConstants::IMAGE_UPLOAD_PATH,
            new Image(),
        );

        $category->setImage($image);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Category created successfult');
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
            return $this->render('admin/category/update.html.twig', [
                'categoryUpdateForm' => $form,
            ]);
        }

        if (!$categoryParentValidator->validate($category)) {
            $this->addFlash('error', 'Invalid parent.');
            return $this->redirectToRoute('app_admin_category_update', ['id' => $category->getId()]);
        }

        /** @var Image $image */
        $image = $this->fileCrudService->update(
            $category->getImage(),
            $form->get('image')->getData()
        );

        $category->setImage($image);

        $this->entityManager->persist($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Category updated sucessfuly');
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

        $this->fileCrudService->delete($category->getImage());

        $this->entityManager->remove($category);
        $this->entityManager->flush();

        $this->addFlash('success', 'Category deleted successfuly');
        return $this->redirectToRoute('app_admin_category_list');
    }
}