<?php

namespace App\Tests;

use App\Entity\Attatchment;
use App\Entity\Category;
use App\Entity\Discount;
use App\Entity\Image;
use App\Entity\Manufacturer;
use App\Entity\Product;
use App\Test\Entity\UserTest;
use App\Tests\Entity\AttatchmentTest;
use App\Tests\Entity\CategoryTest;
use App\Tests\Entity\DeliveryTest;
use App\Tests\Entity\DiscountTest;
use App\Tests\Entity\ImageTest;
use App\Tests\Entity\ManufacturerTest;
use App\Tests\Entity\OrderTest;
use App\Tests\Entity\PaymentTest;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCaseWithDatabase
{
    /** @test */
    public function product_can_not_be_created_without_category(): void
    {
        // Given
        $image = DataProvider::getConfiguredImage($this->entityManager);

        $manufacturer = DataProvider::getConfiguredManufacturer($this->entityManager);

        $product = DataProvider::getProduct()
            ->setImage($image)
            ->setManufacturer($manufacturer);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_not_be_created_without_image(): void
    {
        // Given
        $manufacturer = DataProvider::getConfiguredManufacturer($this->entityManager);

        $category = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getProduct()
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_not_be_created_without_manufacturer(): void
    {
        // Given
        $image = DataProvider::getConfiguredImage($this->entityManager);

        $category = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getProduct()
            ->setImage($image)
            ->setCategory($category);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_be_created_in_database(): void
    {
        // Given
        $image = DataProvider::getConfiguredImage($this->entityManager);
        
        $manufacturer = DataProvider::getConfiguredManufacturer($this->entityManager);

        $category = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getProduct()
            ->setImage($image)
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        // When
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        // Then
        self::assertTestObject($product, $productRecord);
    }

    /** @test */
    public function product_can_access_other_images(): void
    {
        // Given
        $otherImage = DataProvider::getImage($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addOtherImage($otherImage);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // When
        $otherImageCollection = $productRecord->getOtherImages();

        // Then
        self::assertEquals(1, $otherImageCollection->count());

        /** @var Image $otherImageRecord */
        $otherImageRecord = $otherImageCollection->first();

        self::assertEquals($otherImage->getId(), $otherImageRecord->getId());
    }

    /** @test */
    public function product_can_access_subcategories(): void
    {
        // Given
        $subcategory = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addSubcategory($subcategory);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // When
        $subcategoryCollection = $productRecord->getSubcategories();

        // Then
        self::assertEquals(1, $subcategoryCollection->count());

        /** @var Category $subcategoryRecord */
        $subcategoryRecord = $subcategoryCollection->first();

        self::assertEquals($subcategory->getId(), $subcategoryRecord->getId());
    }

    /** @test */
    public function product_can_access_attatchments(): void
    {
        // Given
        $attatchment = DataProvider::getConfiguredAttatchment($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addAttatchment($attatchment);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Whne
        $attatchmentCollection = $productRecord->getAttatchments();

        // Then
        self::assertEquals(1, $attatchmentCollection->count());
        
        $attatchmentRecord = $attatchmentCollection->first();
        
        self::assertEquals($attatchment->getId(), $attatchmentRecord->getId());
    }

    /** @test */
    public function product_can_access_discounts(): void
    {
        // Given
        $discount = DataProvider::getConfiguredDiscount($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addDiscount($discount);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        // When
        $discountCollection = $productRecord->getDiscounts();

        // Then
        self::assertEquals(1, $discountCollection->count());
        
        $discountRecord = $discountCollection->first();
        
        self::assertEquals($discount->getId(), $discountRecord->getId());
    }

    /** @test */
    public function image_is_deleted_when_product_is_deleted(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $image = $product->getImage();

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $imageId = $image->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        $imageRecord = $imageRepository->find($imageId);

        self::assertEquals(null, $imageRecord);
    }

    /** @test */
    public function other_images_are_deleted_when_product_is_deleted(): void
    {
        // Given
        $otherImage = DataProvider::getConfiguredImage($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addOtherImage($otherImage);

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $otherImageId = $otherImage->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        $imageRecord = $imageRepository->find($otherImageId);

        self::assertEquals(null, $imageRecord);
    }

    /** @test */
    public function attatchment_is_not_deleted_when_product_is_deleted(): void
    {
        // Given
        $attatchment = DataProvider::getConfiguredAttatchment($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addAttatchment($attatchment);

        /** @var AttatchmentRepository $attatchmentRepository */
        $attatchmentRepository = $this->entityManager->getRepository(Attatchment::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        $attatchmentId = $attatchment->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        /** @var Attatchment $attatchmentRecord */
        $attatchmentRecord = $attatchmentRepository->find($attatchmentId);

        self::assertNotEquals(null, $attatchmentRecord);
    }

    /** @test */
    public function category_is_not_deleted_when_product_is_deleted(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $category = $product->getCategory();

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);
        
        $categorytId = $category->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        /** @var Category $categoryRecord */
        $categoryRecord = $categoryRepository->find($categorytId);

        self::assertNotEquals(null, $categoryRecord);
    }

    /** @test */
    public function subcategories_are_not_deleted_when_product_is_deleted(): void
    {
        // Given
        $subcategory = DataProvider::getConfiguredCategory($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addSubcategory($subcategory);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        $subcategorytId = $subcategory->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        /** @var Category $categoryRecord */
        $subcategoryRecord = $categoryRepository->find($subcategorytId);

        self::assertNotEquals(null, $subcategoryRecord);
    }

    /** @test */
    public function manufacturer_is_not_deleted_when_product_is_deleted(): void
    {
        // Given
        $product = DataProvider::getConfiguredProduct($this->entityManager);

        $manufacturer = $product->getManufacturer();

        /** @var ManufacturerRepository $manufacturerRepository */
        $manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);
        
        $manufacturerId = $manufacturer->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        /** @var Manufacturer $manufacturerRecord */
        $manufacturerRecord = $manufacturerRepository->find($manufacturerId);

        self::assertNotEquals(null, $manufacturerRecord);
    }

    /** @test */
    public function discounts_are_not_deleted_when_product_is_deleted(): void
    {
        // Given
        $discount = DataProvider::getConfiguredDiscount($this->entityManager);

        $product = DataProvider::getConfiguredProduct($this->entityManager)
            ->addDiscount($discount);

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
        $discountId = $discount->getId();

        // When
        $this->entityManager->remove($product);
        $this->entityManager->flush();

        // Then
        /** @var Discount $discountRecord */
        $discountRecord = $discountRepository->find($discountId);

        self::assertNotEquals(null, $discountRecord);
    }

    public static function assertTestObject(Product $productReference, Product $productToTest):void
    {
        self::assertNotEquals(null, $productToTest);
        self::assertEquals($productReference->getId(), $productToTest->getId());
        self::assertEquals($productReference->getCategory(), $productToTest->getCategory());
        self::assertEquals($productReference->getImage(), $productToTest->getImage());
        self::assertEquals($productReference->getSubcategories(), $productToTest->getSubcategories());
        self::assertEquals($productReference->getOtherImages(), $productToTest->getOtherImages());
        self::assertEquals($productReference->getQuantity(), $productToTest->getQuantity());
        self::assertEquals($productReference->getPrice(), $productToTest->getPrice());
        self::assertEquals($productReference->getName(), $productToTest->getName());
        self::assertEquals($productReference->getDescription(), $productToTest->getDescription());
        self::assertEquals($productReference->getDiscounts(), $productToTest->getDiscounts());
        self::assertEquals($productReference->isActive(), $productToTest->isActive());
    }
}