<?php

namespace App\Entity;

use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="El nombre es requerido")
     * @Assert\Regex(
     *     pattern="/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s\.\-']+$/u",
     *     message="El nombre solo puede contener letras, espacios y caracteres especiales (. - ')"
     * )
     * @Assert\Length(
     *      min=2,
     *      max=255,
     *      minMessage="El nombre debe tener al menos {{ limit }} caracteres",
     *      maxMessage="El nombre no puede tener más de {{ limit }} caracteres"
     * )
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="La nacionalidad es requerida")
     * @Assert\Choice(
     *     choices={"Argentino", "Boliviano", "Brasileño", "Chileno", "Colombiano", "Costarricense", "Cubano", "Dominicano", "Ecuatoriano", "Español", "Guatemalteco", "Hondureño", "Mexicano", "Nicaragüense", "Panameño", "Paraguayo", "Peruano", "Puertorriqueño", "Salvadoreño", "Uruguayo", "Venezolano", "Estadounidense", "Canadiense", "Británico", "Francés", "Alemán", "Italiano", "Portugués", "Japonés", "Chino", "Coreano", "Indio", "Ruso", "Otro"},
     *     message="La nacionalidad seleccionada no es válida"
     * )
     */
    private $nationality;

    /**
     * @ORM\OneToMany(targetEntity=Book::class, mappedBy="author")
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getNationality(): ?string
    {
        return $this->nationality;
    }

    public function setNationality(string $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->removeElement($book)) {
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }
}
