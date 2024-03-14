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
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_not_be_created_without_image(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_not_be_created_without_manufacturer(): void
    {
        // Given
        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setCategory($category);

        // Expect
        self::expectException(NotNullConstraintViolationException::class);

        // When
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    /** @test */
    public function product_can_be_created_in_database(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        // When
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

        // Then
        self::assertTestObject($productRecord);
        ImageTest::assertTestObject($productRecord->getImage());
    }

    /** @test */
    public function product_can_access_other_images(): void
    {
        // Given
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $otherImage = ImageTest::getTestObject();

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addOtherImage($otherImage);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

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
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $subcategory = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addSubcategory($subcategory);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);
        
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($subcategory);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

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
        $attatchment = AttatchmentTest::getTestObject();

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->setImage(ImageTest::getTestObject())
            ->addAttatchment($attatchment);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        $this->entityManager->persist($attatchment);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

        /** @var Product $productRecord */
        $productRecord = $productRepository->find($product->getId());

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
        $discount = DiscountTest::getTestObject();

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = ProductTest::getTestObject()
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->setImage(ImageTest::getTestObject())
            ->addDiscount($discount);

        /** @var ProductRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(Product::class);

        $this->entityManager->persist($discount);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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
        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $image = ImageTest::getTestObject();

        $product = ProductTest::getTestObject()
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->setImage($image);

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();

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
        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $otherImage = ImageTest::getTestObject();

        $product = ProductTest::getTestObject()
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->setImage(ImageTest::getTestObject())
            ->addOtherImage($otherImage);

        /** @var ImageRepository $imageRepository */
        $imageRepository = $this->entityManager->getRepository(Image::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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
        $attatchment = AttatchmentTest::getTestObject();

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addAttatchment($attatchment);

        /** @var AttatchmentRepository $attatchmentRepository */
        $attatchmentRepository = $this->entityManager->getRepository(Attatchment::class);

        $this->entityManager->persist($attatchment);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
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
        $subcategory = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addSubcategory($subcategory);

        /** @var CategoryRepository $categoryRepository */
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        $this->entityManager->persist($subcategory);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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
        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        /** @var ManufacturerRepository $manufacturerRepository */
        $manufacturerRepository = $this->entityManager->getRepository(Manufacturer::class);

        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        
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
        $discount = DiscountTest::getTestObject();

        $manufacturer = ManufacturerTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $category = CategoryTest::getTestObject()
            ->setImage(ImageTest::getTestObject());

        $product = self::getTestObject()
            ->setImage(ImageTest::getTestObject())
            ->setManufacturer($manufacturer)
            ->setCategory($category)
            ->addDiscount($discount);

        /** @var DiscountRepository $discountRepository */
        $discountRepository = $this->entityManager->getRepository(Discount::class);

        $this->entityManager->persist($discount);
        $this->entityManager->persist($manufacturer);
        $this->entityManager->persist($category);
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

    // /** @test */
    // public function product_can_not_be_deleted_while_in_order(): void
    // {
    //     // Given
    //     $manufacturer = ManufacturerTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $category = CategoryTest::getTestObject()
    //         ->setImage(ImageTest::getTestObject());

    //     $product = self::getTestObject()
    //         ->setImage(ImageTest::getTestObject())
    //         ->setManufacturer($manufacturer)
    //         ->setCategory($category);

    //     $user = UserTest::getTestObject();

    //     $delivery = DeliveryTest::getTestObject();

    //     $payment = PaymentTest::getTestObject();

    //     $order = OrderTest::getTestObject()
    //         ->setUser($user)
    //         ->setDelivery($delivery)
    //         ->setPaymeny($payment)
    //         ->addProduct($product);

    //     $this->entityManager->persist($manufacturer);
    //     $this->entityManager->persist($category);
    //     $this->entityManager->persist($product);
    //     $this->entityManager->persist($user);
    //     $this->entityManager->persist($delivery);
    //     $this->entityManager->persist($payment);
    //     $this->entityManager->persist($order);
    //     $this->entityManager->flush();

    //     // Expect
    //     self::expectException(NotNullConstraintViolationException::class);

    //     // When
    //     $this->entityManager->remove($product);
    //     $this->entityManager->flush();
    // }

    public static function getTestObject(): Product
    {
        return (new Product())
            ->setQuantity(1.0)
            ->setPrice(2.0)
            ->setName('product_name')
            ->setDescription('product_description')
            ->setActive(true);
    }

    public static function assertTestObject($product):void
    {
        self::assertNotEquals(null, $product);
        self::assertGreaterThan(0, $product->getId());
        self::assertEquals(1, $product->getQuantity());
        self::assertEquals(2.0, $product->getPrice());
        self::assertEquals('product_name', $product->getName());
        self::assertEquals('product_description', $product->getDescription());
        self::assertEquals(true, $product->isActive());
    }
}