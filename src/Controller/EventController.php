<?php

namespace App\Controller;

use App\Dto\EventInput;
use App\Repository\ReadEventRepository;
use App\Repository\WriteEventRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EventController
{
    public function __construct(
        private WriteEventRepository $writeEventRepository,
        private ReadEventRepository $readEventRepository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route(path: '/api/event/{id}/update', name: 'api_commit_update', methods: ['PUT'])]
    public function update(Request $request, int $id): Response
    {
        $eventInput = $this->serializer->deserialize($request->getContent(), EventInput::class, 'json');

        $errors = $this->validator->validate($eventInput);

        if (0 < count($errors)) {
            return new JsonResponse(
                ['message' => $errors->get(0)->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }

        if (false === $this->readEventRepository->exist($id)) {
            return new JsonResponse(
                ['message' => sprintf('Event identified by %d not found !', $id)],
                Response::HTTP_NOT_FOUND
            );
        }

        try {
            $this->writeEventRepository->update($eventInput, $id);
        } catch (\Exception $exception) {
            return new Response(null, Response::HTTP_SERVICE_UNAVAILABLE);
        }

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}
