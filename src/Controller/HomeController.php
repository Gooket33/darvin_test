<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Form\JokeFormType;
use App\Service\JokeApiService;
use App\Service\JokeMailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $jokeApiService;
    private $jokeMailerService;

    public function __construct(JokeApiService $jokeApiService, JokeMailerService $jokeMailerService)
    {
        $this->jokeApiService = $jokeApiService;
        $this->jokeMailerService = $jokeMailerService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request): Response
    {
        $categories = $this->jokeApiService->getCategories();
        $form = $this->createForm(JokeFormType::class, null, ['categories' => array_combine($categories, $categories)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $joke = $this->jokeApiService->getRandomJoke($data['category']);
            $this->jokeMailerService->sendJokeEmail($data['email'], $joke, $data['category']);

            file_put_contents('/home/gooket/DarvinTest/jokes.txt', $joke . PHP_EOL, FILE_APPEND);

            return new JsonResponse(['message' => 'Шутка отправлена и записана в файл!'], Response::HTTP_OK);
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

