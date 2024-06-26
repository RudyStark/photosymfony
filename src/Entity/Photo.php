<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\TimestampableTrait;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
#[ApiResource(normalizationContext: ['groups' => ['photo:read']], denormalizationContext: ['groups' => ['photo:write']])]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'title' => 'partial', 'slug' => 'exact'])]
#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['photo:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['photo:read', 'photo:write', 'tag:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?string $url = null;

    #[ORM\Column]
    #[Groups(['photo:read', 'photo:write'])]
    private ?float $price = null;

    #[Gedmo\Slug(fields: ['title'])]
    #[ORM\Column(length: 128, unique: true)]
    #[Groups(['photo:read'])]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['photo:read', 'photo:write'])]
    private ?array $metaInfo = null;

    private $file;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'photos')]
    #[Groups(['photo:read', 'photo:write'])]
    private Collection $tags;

    /**
     * @var Collection<int, OrderItem>
     */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'photo')]
    private Collection $orderItems;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->orderItems = new ArrayCollection();
        // par sécurité (pour le test API)
        $this->createdAt = new \DateTimeImmutable();
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

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        $this->url = $url;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getMetaInfo(): ?array
    {
        return $this->metaInfo;
    }

    public function setMetaInfo(?array $metaInfo): static
    {
        $this->metaInfo = $metaInfo;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }

        return $this;
    }

    public function removeTag(Tag $tag): static
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setPhoto($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getPhoto() === $this) {
                $orderItem->setPhoto(null);
            }
        }

        return $this;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    #[ORM\PrePersist]
    public function prePersist(): void
    {
        if (!$this->getCreatedAt()) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }
    }
}
