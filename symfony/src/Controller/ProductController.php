<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController
{
    /**
     * @Route("/api/products/{page}", name="show_products")
     *@method({"GET"})
     */
    public function showProductList(ProductRepository $repo, $page = 1): Response
    {
        $limit = 10;
        $page = $page -1;
        $offset = ($page * $limit);

        $productList = $repo->findBy([], [], $limit, $offset);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($productList, 'json');

        return $this->json([
            'products' => $jsonContent,
        ]);
    }

    /**
     * @Route("/api/products/{id}", name="show_product")
     *@method({"GET"})
     */
    public function showProduct(Request $request, ProductRepository $repo, $id): Response
    {

        $product = $repo->findById($id);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($product, 'json');

        return $this->json([
            'product' => $jsonContent,
        ]);
    }
}
