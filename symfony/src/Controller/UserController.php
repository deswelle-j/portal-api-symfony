<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/apiv1/user",
     *     name="add_user",
     *     methods={"post"}
     *)
     *
     * @OA\Response(
     *     response=200,
     *     description="add a user",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=User::class, groups={"usersList"}))
     *     )
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function addUser(Request $request, UserRepository $repo, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $customer = $this->getUser();

        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        $user->setCustomer($customer);

        $errors = $validator->validate($user);

        $errorMessage = [];
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                array_push($errorMessage, $error->getMessage());
            };

            return new JsonResponse([
                "error" => Response::HTTP_BAD_REQUEST,
                "messages" => $errorMessage
            ],
                Response::HTTP_BAD_REQUEST,
                ['Content-Type'=> 'application/json']
            );
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response('201', Response::HTTP_CREATED);
    }

    /**
     * @Route("/apiv1/users",
     *     name="show_users",
     *     methods={"GET"}
     * )
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the user's list",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=User::class, groups={"usersList, usersCustomer"}))
     *     )
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function showUserList(UserRepository $repo): Response
    {
        $customer = $this->getUser();

        $usersList = $repo->findBy(['customer' => $customer->getId()]);

        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizer]);

        $jsonContent = $serializer->normalize($usersList[0],  null, ['groups' => ['usersList', 'usersCustomer']]);

        return $this->json([
            'users' => $jsonContent,
        ]);
    }

    /**
     * @Route("/apiv1/users/{id}",
     *     name="show_user",
     *     methods={"GET"}
     * )
     *
     * @OA\Response(
     *     response=200,
     *     description="Returns the user's detail",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=User::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     * )
     * @OA\Tag(name="users")
     * @Security(name="Bearer")
     */
    public function showUser(Request $request, UserRepository $repo, $id): Response
    {

        $user = $repo->findById($id);

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($user, 'json');

        return $this->json([
            'user' => $jsonContent,
        ]);
    }
}
