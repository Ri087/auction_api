<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\DownloadFilesRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
    private ?string $realname = null;

    #[ORM\Column(length: 255)]
    private ?string $realpath = null;

    #[ORM\Column(length: 255)]
    private ?string $publicpath = null;

    #[ORM\Column(length: 255)]
    private ?string $mimeType = null;

    private ?File $file = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'downloadFiles')]
    private ?Auction $auction_id = null;
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

    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    public function setFile(UploadedFile $file): static
    {
        $this->file = $file;

        $this->setName($file->getClientOriginalName());
        $this->setRealname($file->getClientOriginalName());
        $this->setMimeType($file->getClientMimeType());

        $this->setPublicpath("./documents/pictures");
        $this->setRealpath("documents/pictures");
        $this->setSlug($this->getRealname());
        $file->move(
            $this->getPublicPath(),
            $this->getSlug()
        );

        return $this;
    }

    public function getRealname(): ?string
    {
        return $this->realname;
    }

    public function setRealname(string $realname): static
    {
        $this->realname = $realname;

        return $this;
    }

    public function getRealpath(): ?string
    {
        return $this->realpath;
    }

    public function setRealpath(string $realpath): static
    {
        $this->realpath = $realpath;

        return $this;
    }

    public function getPublicpath(): ?string
    {
        return $this->publicpath;
    }

    public function setPublicpath(string $publicpath): static
    {
        $this->publicpath = $publicpath;

        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }
    public function setSlug(string $slug): static
    {
        $slugger = new AsciiSlugger();
        $parseslug = $slugger->slug($slug . time());
        $this->slug = $parseslug . "." . $this->getFile()->getClientOriginalExtension();

        return $this;
    }

    public function getAuctionId(): ?Auction
    {
        return $this->auction_id;
    }

    public function setAuctionId(?Auction $auction_id): static
    {
        $this->auction_id = $auction_id;

        return $this;
    }
}
