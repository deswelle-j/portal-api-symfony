<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\PaginationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ProductController extends AbstractController
{
    public const PRODUCT_BY_PAGE = 10;

    /**
     * List the product.
     *
     * This call takes into account all confirmed product.
     *
     * @Route("/apiv1/products/{page}",
     *     name="show_products",
     *     methods={"GET"}
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the product",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="products")
     * @Security(name="Bearer")
     */
    public function showProductList(ProductRepository $repo, PaginationService $pagination, $page = 1): Response
    {
        $query = $repo->findProduct();
        $results = $pagination->paginate($query, $page, self::PRODUCT_BY_PAGE);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $jsonContent = $serializer->serialize($results, 'json');

        return $this->json([
            'products' => $jsonContent,
        ]);
    }

    /**
     * @Route("/apiv1/products/{id}",
     *     name="show_product",
     *     methods={"GET"}
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns a product detail",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Product::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="id",
     *     in="query",
     *     description="id of the product",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="products")
     * @Security(name="Bearer")
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
