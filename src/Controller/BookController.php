<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use App\Service\BookGenerator;

final class BookController extends AbstractController
{
    #[Route('/', name: 'book_home')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig');
    }

    #[Route('/books', name: 'get_books', methods: ['GET'])]
    public function getBooks(Request $request, BookGenerator $generator): JsonResponse
    {
        $locale = $request->query->get('region', 'en_US');
        $seed = (int) $request->query->get('seed', 0);
        $likes = (float) $request->query->get('likes', 0);
        $reviews = (float) $request->query->get('reviews', 0);
        $page = (int) $request->query->get('page', 1);

        $books = $generator->generateBooks($locale, $seed, $page, $likes, $reviews);

        return $this->json($books);
    }
}
