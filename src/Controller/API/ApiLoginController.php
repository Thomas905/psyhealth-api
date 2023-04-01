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
            $response = new Response();
            $response->setContent(json_encode([
                'message' => 'missing credentials',
            ]));
            $response->setStatusCode(Response::HTTP_UNAUTHORIZED);
            $response->headers->set('Content-Type', 'application/json');
            $response->headers->set('Access-Control-Allow-Origin', '*');
            $response->headers->set('Access-Control-Allow-Methods', 'POST');
            $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
            return $response;
        }

        $response = new Response();
        $response->setContent(json_encode([
            'user' => $user->getId(),
            'token' => $this->jwtManager->create($user),
        ]));
        $response->setStatusCode(Response::HTTP_OK);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'POST');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        return $response;
    }
}