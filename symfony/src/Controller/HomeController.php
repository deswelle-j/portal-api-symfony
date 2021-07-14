<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     *@method({"GET"})
     */
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/HomeController.php',
        ]);
    }

    /**
     * @Route("/product", name="add_product")
     *@method({"post"})
     */
    public function addProduct(Request $request, SerializerInterface $serializer): Response
    {
        $data = $request->getContent();
        $product = $serializer->deserialize($data, Product::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/customer", name="add_customer")
     *@method({"post"})
     */
    public function addCustomer(Request $request, SerializerInterface $serializer, UserPasswordEncoderInterface $encoder): Response
    {
        $data = $request->getContent();
        $customer = $serializer->deserialize($data, Customer::class, 'json');

        $hash = $encoder->encodePassword($customer, $customer->getPassword());
        $customer->setPassword($hash);

        $em = $this->getDoctrine()->getManager();
        $em->persist($customer);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }
}
