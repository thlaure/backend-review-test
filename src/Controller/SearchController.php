<?php

namespace App\Controller;

use App\Dto\SearchInput;
use App\Repository\ReadEventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SearchController
{
    public function __construct(
        private ReadEventRepository $readEventRepository,
        private SerializerInterface $serializer,
    ) {
    }

    #[Route(path: '/api/search', name: 'api_search', methods: ['GET'])]
    public function searchCommits(#[MapQueryString] SearchInput $searchInput): JsonResponse
    {
        $countByType = $this->readEventRepository->countByType($searchInput);

        $data = [
            'meta' => [
                'totalEvents' => $this->readEventRepository->countAll($searchInput),
                'totalPullRequests' => $countByType['pullRequest'] ?? 0,
                'totalCommits' => $countByType['commit'] ?? 0,
                'totalComments' => $countByType['comment'] ?? 0,
            ],
            'data' => [
                'events' => $this->readEventRepository->getLatest($searchInput),
                'stats' => $this->readEventRepository->statsByTypePerHour($searchInput),
            ],
        ];

        return new JsonResponse($data);
    }
}
