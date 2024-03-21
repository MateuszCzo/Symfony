<?php

namespace App\Tests;

use App\Entity\Address;
use App\Entity\Attatchment;
use App\Entity\Cart;
use App\Entity\Category;
use App\Entity\Contact;
use App\Entity\Delivery;
use App\Entity\Discount;
use App\Entity\Image;
use App\Entity\Manufacturer;
use App\Entity\Order;
use App\Entity\Payment;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class DataProvider
{
    private static int $emailIndex = 0;

    public static function getAddress(): Address
    {
        return (new Address())
            ->setStreet('address_street')
            ->setNumber('address_number')
            ->setPostCode('address_post_code');
    }

    public static function getAttatchment(): Attatchment
    {
        return (new Attatchment())
            ->setName('attatchment_name')
            ->setDescription('attatchemnt_description')
            ->setFileName('attatchment_file_name')
            ->setType('attatchemnt_type');
    }

    public static function getCart(): Cart
    {
        return (new Cart())
            ->setPrice(1.0);
    }

    public static function getCategory(): Category
    {
        return (new Category())
            ->setName('category_name')
            ->setDescription('category_description');
    }

    public static function getContact(): Contact
    {
        return (new Contact())
            ->setPhoneNumber(123456789);
    }

    public static function getDelivery(): Delivery
    {
        return (new Delivery())
            ->setName('delivery_name')
            ->setDescription('delivery_description')
            ->setType('delivery_type')
            ->setActive(true);
    }

    public static function getDiscount(): Discount
    {
        return (new Discount())
            ->setName('discount_name')
            ->setCriteria(['criteria_name' => 'criteria_value'])
            ->setValue(0.5)
            ->setType('discount_type');
    }

    public static function getImage(): Image
    {
        return (new Image())
            ->setName('image_name')
            ->setPath('image_path')
            ->setType('image_type');
    }

    public static function getManufacturer(): Manufacturer
    {
        return (new Manufacturer())
            ->setName('manufacturer_name')
            ->setDescription('manufacturer_description');
    }

    public static function getOrder(): Order
    {
        return (new Order())
            ->setStatus('order_status')
            ->setPrice(1.0);
    }

    public static function getPayment(): Payment
    {   
        return (new Payment())
            ->setName('payment_name')
            ->setDescription('payment_description')
            ->setType('payment_type')
            ->setActive(true);
    }

    public static function getProduct(): Product
    {
        return (new Product())
            ->setQuantity(1.0)
            ->setPrice(2.0)
            ->setName('product_name')
            ->setDescription('product_description')
            ->setActive(true);
    }

    public static function getUser(): User
    {
        self::$emailIndex++;
        
        return (new User())
            ->setEmail('test' . self::$emailIndex . '@test.test')
            ->setRoles(['ROLE_TEST'])
            ->setPassword('user_password');
    }

    public static function getConfiguredAddress(EntityManagerInterface $entityManager): Address
    {
        $user = self::getUser();

        $address = self::getAddress()
            ->setUser($user);

        $entityManager->persist($user);
        $entityManager->persist($address);
        $entityManager->flush();

        return $address;
    }

    public static function getConfiguredAttatchment(EntityManagerInterface $entityManager): Attatchment
    {
        $attatchment = self::getAttatchment();

        $entityManager->persist($attatchment);
        $entityManager->flush();

        return $attatchment;
    }

    public static function getConfiguredCart(EntityManagerInterface $entityManager): Cart
    {
        $user = self::getUser();

        $cart = self::getCart()
            ->setUser($user);

        $entityManager->persist($user);
        $entityManager->persist($cart);
        $entityManager->flush();

        return $cart;
    }

    public static function getConfiguredCategory(EntityManagerInterface $entityManager): Category
    {
        $category = self::getCategory()
            ->setImage(self::getImage());

        $entityManager->persist($category);
        $entityManager->flush();

        return $category;
    }

    public static function getConfiguredContact(EntityManagerInterface $entityManager): Contact
    {
        $user = self::getUser();

        $contact = self::getContact()
            ->setUser($user);

        $entityManager->persist($user);
        $entityManager->persist($contact);
        $entityManager->flush();

        return $contact;
    }

    public static function getConfiguredDelivery(EntityManagerInterface $entityManager): Delivery
    {
        $delivery = self::getDelivery();

        $entityManager->persist($delivery);
        $entityManager->flush();

        return $delivery;
    }

    public static function getConfiguredDiscount(EntityManagerInterface $entityManager): Discount
    {
        $discount = self::getDiscount();

        $entityManager->persist($discount);
        $entityManager->flush();

        return $discount;
    }

    public static function getConfiguredImage(EntityManagerInterface $entityManager): Image
    {
        $image = self::getImage();

        $entityManager->persist($image);
        $entityManager->flush();

        return $image;
    }

    public static function getConfiguredManufacturer(EntityManagerInterface $entityManager): Manufacturer
    {
        $manufacturer = self::getManufacturer()
            ->setImage(self::getImage());

        $entityManager->persist($manufacturer);
        $entityManager->flush();

        return $manufacturer;
    }

    public static function getConfiguredOrder(EntityManagerInterface $entityManager): Order
    {
        $manufacturer = self::getManufacturer()
            ->setImage(self::getImage());

        $category = self::getCategory()
            ->setImage(self::getImage());

        $product = self::getProduct()
            ->setImage(self::getImage())
            ->setCategory($category)
            ->setManufacturer($manufacturer);

        $user = self::getUser();

        $delivery = self::getDelivery();

        $payment = self::getPayment();

        $order = self::getOrder()
            ->addProduct($product)
            ->setUser($user)
            ->setDelivery($delivery)
            ->setPaymeny($payment);

        $entityManager->persist($manufacturer);
        $entityManager->persist($category);
        $entityManager->persist($product);
        $entityManager->persist($user);
        $entityManager->persist($delivery);
        $entityManager->persist($payment);
        $entityManager->persist($order);
        $entityManager->flush();

        return $order;
    }

    public static function getConfiguredPayment(EntityManagerInterface $entityManager): Payment
    {
        $payment = self::getPayment();

        $entityManager->persist($payment);
        $entityManager->flush();

        return $payment;
    }

    public static function getConfiguredProduct(EntityManagerInterface $entityManager): Product
    {
        $manufacturer = self::getManufacturer()
            ->setImage(self::getImage());

        $category = self::getCategory()
            ->setImage(self::getImage());

        $product = self::getProduct()
            ->setImage(self::getImage())
            ->setManufacturer($manufacturer)
            ->setCategory($category);

        $entityManager->persist($manufacturer);
        $entityManager->persist($category);
        $entityManager->persist($product);
        $entityManager->flush();

        return $product;
    }

    public static function getConfiguredUser(EntityManagerInterface $entityManager): User
    {
        $user = self::getUser();

        $entityManager->persist($user);
        $entityManager->flush();

        return $user;
    }
}
