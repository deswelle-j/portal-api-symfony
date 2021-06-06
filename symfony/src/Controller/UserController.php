<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/api/user", name="add_user")
     *@method({"post"})
     */
    public function addUser(Request $request, UserRepository $repo, SerializerInterface $serializer): Response
    {
        $customer = $this->getUser();

        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');
        $user->setCustomer($customer);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return new Response('', Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/users", name="show_users")
     * @method({
    "GET"
    })
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
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
     * @Route("/api/users/{id}", name="show_user")
     *@method({"GET"})
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
