<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Entity\Customer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CustomerController extends AbstractController
{

    /**
     * @Route("/apiv1/customers",
     *     name="show_customers",
     *     methods={"GET"}
     * )
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of customer",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=Customer::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="customers")
     * @Security(name="Bearer")
     */
    public function showCustomerList(CustomerRepository $repo): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        dump($user);
        $customerList = $repo->findAll();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($customerList, 'json');

        return $this->json([
            'customers' => $jsonContent,
        ]);
    }
}
