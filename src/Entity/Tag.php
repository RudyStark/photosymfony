<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ApiResource(normalizationContext: ['groups' => ['tag:read']], denormalizationContext: ['groups' => ['tag:write']])]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tag:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tag:read', 'tag:write', 'photo:read'])]
    private ?string $name = null;

    #[Gedmo\Slug(fields: ['name'], unique: true)]
    #[ORM\Column(length: 255)]
    #[Groups(['tag:read'])]
    private ?string $slug = null;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\ManyToMany(targetEntity: Photo::class, mappedBy: 'tags')]
    private Collection $photos;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->addTag($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            $photo->removeTag($this);
        }

        return $this;
    }
}
