<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Book
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $year;

    /**
     * @ORM\ManyToMany(targetEntity=Author::class, inversedBy="books")
     */
    private $authors;

//    /**
//     * @Assert\File(maxSize="6000000")
//     */
//    public $file;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getYear(): ?\DateTimeInterface
    {
        return $this->year;
    }

    public function setYear(?\DateTimeInterface $year): self
    {
        $this->year = $year;

        return $this;
    }

    /**
     * @return Collection<int, Author>
     */
    public function getAuthors(): Collection
    {
        return $this->authors;
    }

    public function addAuthor(Author $author): self
    {
        if (!$this->authors->contains($author)) {
            $this->authors[] = $author;
        }

        return $this;
    }

    public function removeAuthor(Author $author): self
    {
        $this->authors->removeElement($author);

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile|null $file
     */
    public function setFile(UploadedFile $file = null)
    {
        if ($file)
        {
            $filesystem = new Filesystem();
            if ($this->getImage() && $filesystem->exists('image/'.$this->getImage()))
                $filesystem->remove('image/' . $this->getImage());

            $this->setImage($file->getClientOriginalName());
            $path = 'image/';
            $file->move(
                $path,
                $file->getClientOriginalName()
            );
        }
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

//    /**
//     * @ORM\PreUpdate
//     */
//    public function lifecycleFileUpload(): void
//    {
//        if ($this->file)
//        {
//            $this->setImage(null);
//            $this->createFile();
//        }
//    }
//    /**
//     * @ORM\PrePersist
//     */
//    public function lifecycleFileCreate(): void
//    {
//        $this->setDescription("creat");
//        if ($this->file)
//            $this->createFile();
//        else
//            $this->setImage(null);
//
//    }
//    public function createFile()
//    {
//        $path = 'image';
//        $this->setImage($this->file->getClientOriginalName());
//        $this->getFile()->move(
//            $path,
//            $this->file->getClientOriginalName()
//        );
//    }
}
