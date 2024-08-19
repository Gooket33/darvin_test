<?php
// src/Controller/JokeController.php

namespace App\Controller;

use App\Form\JokeFormType;
use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class JokeController extends AbstractController
{
    /**
     * @Route("/", name="joke_form")
     */
    public function index(Request $request, MailerInterface $mailer): Response
    {
        // Получение списка категорий через API
        $client = new Client(['base_uri' => 'https://api.chucknorris.io/']);
        $response = $client->request('GET', 'jokes/categories');
        $categories = json_decode($response->getBody()->getContents(), true);

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

            return $this->redirectToRoute('joke_success');
        }

        return $this->render('joke/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/success", name="joke_success")
     */
    public function success(): Response
    {
        return new Response('Шутка отправлена и записана в файл!');
    }
}
