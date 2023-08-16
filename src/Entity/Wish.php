<?php

namespace App\Entity;

use App\Repository\WishRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: WishRepository::class)]
class Wish
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('wishes:read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('wishes:read')]
    private ?string $title = null;

    #[ORM\Column(length: 500)]
    #[Groups('wishes:read')]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups('wishes:read')]
    private ?bool $isPublished = true;

    #[ORM\Column(length: 255)]
    private ?string $dateCreated = 'now' ;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $categories = null;

    #[ORM\ManyToOne(inversedBy: 'wishes')]
    private ?Author $authors = null;

    #[ORM\Column]
    #[Groups('wishes:read')]
    private ?bool $realise = null;

    public function __serialize() : array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->title = $data['title'];
        $this->description = $data['description'];
    }


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getDateCreated(): ?string
    {
        return $this->dateCreated;
    }

    public function setDateCreated(string $dateCreated): static
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getCategories(): ?Category
    {
        return $this->categories;
    }

    public function setCategories(?Category $categories): static
    {
        $this->categories = $categories;

        return $this;
    }

    public function getAuthors(): ?Author
    {
        return $this->authors;
    }

    public function setAuthors(?Author $authors): static
    {
        $this->authors = $authors;

        return $this;
    }

    public function isRealise(): ?bool
    {
        return $this->realise;
    }

    public function setRealise(bool $realise): static
    {
        $this->realise = $realise;

        return $this;
    }
}
