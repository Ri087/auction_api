<?php

namespace App\Entity;

use App\Repository\DownloadFilesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: DownloadFilesRepository::class)]
class DownloadFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $realName = null;

    #[ORM\Column(length: 255)]
    private ?string $publicPath = null;

    #[ORM\Column(length: 255)]
    private ?string $mineType = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;


    private ?File $file = null;

    #[ORM\Column(length: 255)]
    private ?string $realPath = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRealName(): ?string
    {
        return $this->realName;
    }

    public function setRealName(string $realName): static
    {
        $this->realName = $realName;

        return $this;
    }

    public function getPublicPath(): ?string
    {
        return $this->publicPath;
    }

    public function setPublicPath(string $publicPath): static
    {
        $this->publicPath = $publicPath;

        return $this;
    }

    public function getMineType(): ?string
    {
        return $this->mineType;
    }

    public function setMineType(string $mineType): static
    {
        $this->mineType = $mineType;

        return $this;
    }

    public function getSlug(): ?string
    {
        $slugger = new AsciiSlugger();
        $parseslug = $slugger->slug($this->slug . time());
        $this->slug = $parseslug . "." . $this->getFile()->getClientOriginalExtension();
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): static
    {
        $this->file = $file;
        $this->file->setName($file->getClientOriginalName());
        $this->file->setRealName($file->getFilename());
        $this->file->setMineType($file->getMimeType());
        $this->file->setPublicPath("./documents/pictures/");
        $this->file->setRealPath("documents/pictures/");
        $this->file->setSlug($file->getRealName());

        return $this;
    }

    public function getRealPath(): ?string
    {
        return $this->realPath;
    }

    public function setRealPath(string $realPath): static
    {
        $this->realPath = $realPath;

        return $this;
    }
}
