<?php

namespace App\Controller\API;

use App\Entity\Question;
use App\Entity\Room;
use App\Repository\PlanRepository;
use App\Repository\QuestionRepository;
use App\Repository\ReplyRepository;
use App\Repository\RoomRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_dashboard_plans')]
class ApiDashboardController extends AbstractController
{
    private PlanRepository $planRepository;
    private RequestStack $requestStack;
    private TokenStorageInterface $tokenStorage;
    private JWTTokenManagerInterface $jwtManager;
    private UserRepository $userRepository;
    private QuestionRepository $questionRepository;
    private ReplyRepository $replyRepository;
    private EntityManagerInterface $entityManager;
    private RoomRepository $roomRepository;

    public function __construct(
        PlanRepository $planRepository,
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        JWTTokenManagerInterface $jwtManager,
        UserRepository $userRepository,
        QuestionRepository $questionRepository,
        ReplyRepository $replyRepository,
        EntityManagerInterface $entityManager,
        RoomRepository $roomRepository
    )
    {
        $this->planRepository = $planRepository;
        $this->requestStack = $requestStack;
        $this->tokenStorage = $tokenStorage;
        $this->jwtManager = $jwtManager;
        $this->userRepository = $userRepository;
        $this->questionRepository = $questionRepository;
        $this->replyRepository = $replyRepository;
        $this->entityManager = $entityManager;
        $this->roomRepository = $roomRepository;
    }

    #[Route('/plans', name: '_index', methods: ['GET'])]
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

    #[Route('plans/question/reply', name: '_reply', methods: ['GET'])]
    public function reply( SerializerInterface $serializer): JsonResponse
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
        $user = $this->userRepository->findAll();
        return $this->json($serializer->normalize($user, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'hasReplied',
                'plan' => [
                    'id',
                    'name',
                    'month',
                    'question' => [
                        'id',
                        'description',
                        'replies' => [
                            'description',
                            'monthCount',
                        ]
                    ]
                ]
            ],
        ]));
    }

    #[Route('/question/{id}/reply', name: '_reply_individualy', methods: ['POST'])]
    public function replyIndivi(Question $question, SerializerInterface $serializer, Request $request): JsonResponse
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
        $reply = $this->replyRepository->findBy(['person' => $user, 'Question' => $question]);
        foreach ($reply as $r) {
            $jsonData = $request->getContent();
            $data = json_decode($jsonData, true);
            $description = $data['description'];
            $r->setDescription($description);
            $this->entityManager->persist($r);
            $this->entityManager->flush();
        }

        return $this->json($serializer->normalize($reply, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'description'
            ],
        ]));
    }

    #[Route('/room/{id}/questions', name: 'room_question', methods: ['GET'])]
    public function roomQuestion(Room $room, SerializerInterface $serializer): JsonResponse
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
        $roomquestion = $this->userRepository->findBy(['room' => $room]);
        return $this->json($serializer->normalize($roomquestion, null, [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'hasReplied',
                'plan' => [
                    'id',
                    'name',
                    'month',
                    'question' => [
                        'id',
                        'description',
                        'replies' => [
                            'description',
                            'monthCount',
                        ]
                    ]
                ]
            ],
        ]));
    }
}