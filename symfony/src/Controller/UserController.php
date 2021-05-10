<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class UserController extends AbstractController
{
    /**
     * @Route("/api/users", name="show_users")
     *@method({"GET"})
     */
    public function showUserList(UserRepository $repo): Response
    {
        $usersList = $repo->findAll();

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];

        $serializer = new Serializer($normalizers, $encoders);
        $jsonContent = $serializer->serialize($usersList, 'json');

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