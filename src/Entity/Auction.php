<?php

namespace App\Entity;

use App\Repository\AuctionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: AuctionRepository::class)]
class Auction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['getAllAuction', 'createAuction', 'createOffer', 'getAllOffer'])]
    #[Assert\NotBlank(message: 'Id is required')]

    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['getAllAuction', 'createAuction', 'createOffer', 'getAllOffer'])]
    #[Assert\NotBlank(message: 'item_name is required')]
    #[Assert\Length(min: 5, minMessage: 'item_name doit au minimum avoir {{}} charactères')]

    private ?string $item_name = null;


    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['getAllAuction', 'createAuction'])]
    #[Assert\NotBlank(message: 'item_description is required')]
    #[Assert\Length(min: 5, minMessage: 'item_description doit au minimum avoir {{}} charactères')]

    private ?string $item_description = null;

    #[ORM\Column]
    #[Groups(['getAllAuction', 'createAuction', 'createOffer', 'getAllOffer'])]
    #[Assert\NotBlank(message: 'price is required')]
    #[Assert\Positive(message: 'price must be positive')]
    private ?float $price = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['getAllAuction', 'createAuction'])]
    #[Assert\NotBlank(message: 'min_bid is required')]
    #[Assert\Positive(message: 'min_bid must be positive')]

    private ?int $min_bid = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAllAuction', 'createAuction'])]

    private ?\DateTimeInterface $start_date = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['getAllAuction', 'createAuction'])]

    private ?\DateTimeInterface $end_date = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\OneToMany(mappedBy: 'auction', targetEntity: Offer::class)]
    private Collection $offers;

    #[ORM\OneToMany(mappedBy: 'auction', targetEntity: DownloadFiles::class)]
    #[Groups(['getAllAuction', 'createAuction'])]
    private Collection $downloadFiles;

    #[ORM\ManyToOne(inversedBy: 'auctions')]
    private ?User $user = null;



    public function __construct()
    {
        $this->offers = new ArrayCollection();
        $this->downloadFiles = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItemName(): ?string
    {
        return $this->item_name;
    }

    public function setItemName(string $item_name): static
    {
        $this->item_name = $item_name;

        return $this;
    }

    public function getItemDescription(): ?string
    {
        return $this->item_description;
    }

    public function setItemDescription(?string $item_description): static
    {
        $this->item_description = $item_description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getMinBid(): ?int
    {
        return $this->min_bid;
    }

    public function setMinBid(?int $min_bid): static
    {
        $this->min_bid = $min_bid;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }

    public function setEndDate(\DateTimeInterface $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return Collection<int, Offer>
     */
    public function getOffers(): Collection
    {
        return $this->offers;
    }

    public function addOffer(Offer $offer): static
    {
        if (!$this->offers->contains($offer)) {
            $this->offers->add($offer);
            $offer->setAuction($this);
        }

        return $this;
    }

    public function removeOffer(Offer $offer): static
    {
        if ($this->offers->removeElement($offer)) {
            // set the owning side to null (unless already changed)
            if ($offer->getAuction() === $this) {
                $offer->setAuction(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->item_name;
    }

    /**
     * @return Collection<int, DownloadFiles>
     */
    public function getDownloadFiles(): Collection
    {
        return $this->downloadFiles;
    }

    public function addDownloadFile(DownloadFiles $downloadFile): static
    {
        if (!$this->downloadFiles->contains($downloadFile)) {
            $this->downloadFiles->add($downloadFile);
            $downloadFile->setAuction($this);
        }

        return $this;
    }

    public function removeDownloadFile(DownloadFiles $downloadFile): static
    {
        if ($this->downloadFiles->removeElement($downloadFile)) {
            // set the owning side to null (unless already changed)
            if ($downloadFile->getAuction() === $this) {
                $downloadFile->setAuction(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getIsFinished(): bool
    {
        // Supposons que `endDate` est un objet \DateTimeImmutable représentant la fin de l'enchère
        return $this->end_date <= new \DateTimeImmutable();
    }
}
