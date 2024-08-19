<?php
// src/Controller/HomeController.php

namespace App\Controller;

use App\Form\JokeFormType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Получение списка категорий через API
        $client = new Client(['base_uri' => 'https://api.chucknorris.io/']);
        $response = $client->request('GET', 'jokes/categories');
        $categories = json_decode($response->getBody()->getContents(), true);

        // Создание формы с передачей категорий в опции
        $form = $this->createForm(JokeFormType::class, null, ['categories' => array_combine($categories, $categories)]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            // Получение случайной шутки по категории
            $jokeResponse = $client->request('GET', 'jokes/random', [
                'query' => ['category' => $data['category']]
            ]);
            $joke = json_decode($jokeResponse->getBody()->getContents(), true)['value'];

            // Отправка письма
            $email = (new Email())
                ->from('no-reply@example.com')
                ->to($data['email'])
                ->subject("Случайная шутка из {$data['category']}")
                ->text($joke);

            $mailer->send($email);

            // Запись шутки в файл
            file_put_contents('/home/gooket/DarvinTest/jokes.txt', $joke . PHP_EOL, FILE_APPEND);

            // Возвращаем JSON-ответ
            return new JsonResponse(['message' => 'Шутка отправлена и записана в файл!'], Response::HTTP_OK);
        }

        // Рендеринг формы на главной странице
        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
