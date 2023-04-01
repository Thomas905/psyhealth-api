<?php

namespace App\Controller\API;

use App\Repository\PlanRepository;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/plans', name: 'api_dashboard_plans')]
class ApiDashboardController extends AbstractController
{
    private PlanRepository $planRepository;
    private RequestStack $requestStack;
    private TokenStorageInterface $tokenStorage;
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepository;

    public function __construct(PlanRepository $planRepository, RequestStack $requestStack, TokenStorageInterface $tokenStorage, JWTTokenManagerInterface $jwtManager, UserRepository $userRepository)
    {
        $this->planRepository = $planRepository;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
    }

    #[Route('', name: '_index', methods: ['GET'])]
    public function index( SerializerInterface $serializer): JsonResponse
    {
        $jwtToken = $this->requestStack->getCurrentRequest()->headers->get('Authorization');
        $tokenParts = explode(".", $jwtToken);
        $tokenHeader = base64_decode($tokenParts[0]);
        $tokenPayload = base64_decode($tokenParts[1]);
        $jwtHeader = json_decode($tokenHeader);
        if ($jwtHeader == null) {
            return $this->json([
                'message' => 'missing credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }
        $jwtPayload = json_decode($tokenPayload);
        $user = $this->userRepository->findBy(['username' => $jwtPayload->username]);
        return $this->json($serializer->normalize($user, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'hasReplied',
                'plan' => [
                    'name',
                    'month',
                    'question' => [
                        'id',
                        'description'
                    ]
                ]
            ],
        ]));
    }
}