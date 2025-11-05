<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/books", name="api_books_")
 */
class BookController extends AbstractController
{
    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="list", methods={"GET"})
     */
    public function index(BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAllWithRelations();
        
        $data = [];
        foreach ($books as $book) {
            $data[] = [
                'id' => $book->getId(),
                'title' => $book->getTitle(),
                'publicationYear' => $book->getPublicationYear(),
                'author' => [
                    'id' => $book->getAuthor()->getId(),
                    'name' => $book->getAuthor()->getName(),
                    'nationality' => $book->getAuthor()->getNationality(),
                ],
                'category' => [
                    'id' => $book->getCategory()->getId(),
                    'name' => $book->getCategory()->getName(),
                ],
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(int $id, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->findOneWithRelations($id);

        if (!$book) {
            return $this->json(['error' => 'Libro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'publicationYear' => $book->getPublicationYear(),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'name' => $book->getAuthor()->getName(),
                'nationality' => $book->getAuthor()->getNationality(),
            ],
            'category' => [
                'id' => $book->getCategory()->getId(),
                'name' => $book->getCategory()->getName(),
            ],
        ];

        return $this->json($data);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(
        Request $request,
        AuthorRepository $authorRepository,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $book = new Book();
        $book->setTitle($data['title'] ?? '');
        $book->setPublicationYear($data['publicationYear'] ?? 0);

        // Buscar y asignar autor
        if (isset($data['authorId'])) {
            $author = $authorRepository->find($data['authorId']);
            if (!$author) {
                return $this->json(['error' => 'Autor no encontrado'], Response::HTTP_BAD_REQUEST);
            }
            $book->setAuthor($author);
        }

        // Buscar y asignar categoría
        if (isset($data['categoryId'])) {
            $category = $categoryRepository->find($data['categoryId']);
            if (!$category) {
                return $this->json(['error' => 'Categoría no encontrada'], Response::HTTP_BAD_REQUEST);
            }
            $book->setCategory($category);
        }

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($book);
        $this->entityManager->flush();

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'publicationYear' => $book->getPublicationYear(),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'name' => $book->getAuthor()->getName(),
                'nationality' => $book->getAuthor()->getNationality(),
            ],
            'category' => [
                'id' => $book->getCategory()->getId(),
                'name' => $book->getCategory()->getName(),
            ],
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(
        int $id,
        Request $request,
        BookRepository $bookRepository,
        AuthorRepository $authorRepository,
        CategoryRepository $categoryRepository
    ): JsonResponse {
        $book = $bookRepository->find($id);

        if (!$book) {
            return $this->json(['error' => 'Libro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $book->setTitle($data['title']);
        }
        if (isset($data['publicationYear'])) {
            $book->setPublicationYear($data['publicationYear']);
        }

        // Actualizar autor si se proporciona
        if (isset($data['authorId'])) {
            $author = $authorRepository->find($data['authorId']);
            if (!$author) {
                return $this->json(['error' => 'Autor no encontrado'], Response::HTTP_BAD_REQUEST);
            }
            $book->setAuthor($author);
        }

        // Actualizar categoría si se proporciona
        if (isset($data['categoryId'])) {
            $category = $categoryRepository->find($data['categoryId']);
            if (!$category) {
                return $this->json(['error' => 'Categoría no encontrada'], Response::HTTP_BAD_REQUEST);
            }
            $book->setCategory($category);
        }

        $errors = $this->validator->validate($book);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'publicationYear' => $book->getPublicationYear(),
            'author' => [
                'id' => $book->getAuthor()->getId(),
                'name' => $book->getAuthor()->getName(),
                'nationality' => $book->getAuthor()->getNationality(),
            ],
            'category' => [
                'id' => $book->getCategory()->getId(),
                'name' => $book->getCategory()->getName(),
            ],
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id, BookRepository $bookRepository): JsonResponse
    {
        $book = $bookRepository->find($id);

        if (!$book) {
            return $this->json(['error' => 'Libro no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($book);
        $this->entityManager->flush();

        return $this->json(['message' => 'Libro eliminado correctamente'], Response::HTTP_OK);
    }
}
