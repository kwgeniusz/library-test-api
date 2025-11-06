<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/authors", name="api_authors_")
 */
class AuthorController extends AbstractController
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
    public function index(AuthorRepository $authorRepository): JsonResponse
    {
        $authors = $authorRepository->findAll();
        
        $data = [];
        foreach ($authors as $author) {
            $data[] = [
                'id' => $author->getId(),
                'name' => $author->getName(),
                'nationality' => $author->getNationality(),
            ];
        }

        return $this->json($data);
    }

    /**
     * @Route("/nationalities", name="nationalities", methods={"GET"})
     */
    public function getNationalities(): JsonResponse
    {
        $nationalities = [
            'Argentino',
            'Boliviano',
            'Brasileño',
            'Chileno',
            'Colombiano',
            'Costarricense',
            'Cubano',
            'Dominicano',
            'Ecuatoriano',
            'Español',
            'Guatemalteco',
            'Hondureño',
            'Mexicano',
            'Nicaragüense',
            'Panameño',
            'Paraguayo',
            'Peruano',
            'Puertorriqueño',
            'Salvadoreño',
            'Uruguayo',
            'Venezolano',
            'Estadounidense',
            'Canadiense',
            'Británico',
            'Francés',
            'Alemán',
            'Italiano',
            'Portugués',
            'Japonés',
            'Chino',
            'Coreano',
            'Indio',
            'Ruso',
            'Otro'
        ];

        return $this->json($nationalities);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(int $id, AuthorRepository $authorRepository): JsonResponse
    {
        $author = $authorRepository->find($id);

        if (!$author) {
            return $this->json(['error' => 'Autor no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $author->getId(),
            'name' => $author->getName(),
            'nationality' => $author->getNationality(),
        ];

        return $this->json($data);
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $author = new Author();
        $author->setName($data['name'] ?? '');
        $author->setNationality($data['nationality'] ?? '');

        $errors = $this->validator->validate($author);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($author);
        $this->entityManager->flush();

        return $this->json([
            'id' => $author->getId(),
            'name' => $author->getName(),
            'nationality' => $author->getNationality(),
        ], Response::HTTP_CREATED);
    }

    /**
     * @Route("/{id}", name="update", methods={"PUT"})
     */
    public function update(int $id, Request $request, AuthorRepository $authorRepository): JsonResponse
    {
        $author = $authorRepository->find($id);

        if (!$author) {
            return $this->json(['error' => 'Autor no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $author->setName($data['name']);
        }
        if (isset($data['nationality'])) {
            $author->setNationality($data['nationality']);
        }

        $errors = $this->validator->validate($author);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->flush();

        return $this->json([
            'id' => $author->getId(),
            'name' => $author->getName(),
            'nationality' => $author->getNationality(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE"})
     */
    public function delete(int $id, AuthorRepository $authorRepository): JsonResponse
    {
        $author = $authorRepository->find($id);

        if (!$author) {
            return $this->json(['error' => 'Autor no encontrado'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($author);
        $this->entityManager->flush();

        return $this->json(['message' => 'Autor eliminado correctamente'], Response::HTTP_OK);
    }
}
