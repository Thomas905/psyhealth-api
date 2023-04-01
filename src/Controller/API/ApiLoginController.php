<?php

namespace App\Controller\API;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            header("Content-Type: application/json");
            header("Access-Control-Allow-Origin: *");
            header("Access-Control-Allow-Methods: POST");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST");
        header("Access-Control-Allow-Headers: Content-Type, Authorization");
        return $this->json([
            'user' => $user->getId(),
            'token' => $this->jwtManager->create($user),
        ]);
    }
}