<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PropertyRepository")
 * @UniqueEntity("title")
 */
class Property
{

    CONST HEAT =  [
        0 => 'Electrique',
        1 => 'Gaz'
    ];

    CONST TYPE = [
        0 => 'Appartement',
        1 => 'Maison'
    ];

    CONST MODE = [
        0 => 'Achat',
        1 => 'Louer'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     * @Assert\Length(min = 2)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     * @Assert\Range(
            min = 10,
            max = 400,
            )
     */
    private $surface;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     *
     */
    private $rooms;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     */
    private $bedrooms;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     */
    private $floor;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $heat;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $type;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero
     */
    private $mode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min = 2)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Assert\Length(min = 4)
     */
    private $adress;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @Assert\Type("bool")
     */
    private $sold = false;

    /**
     *
     * @ORM\Column(type="datetime")
     *
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Positive
     * @Assert\Regex("/^[0-9]{5}$/")
     */
    private $postalcode;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Image", mappedBy="property", cascade={"persist", "remove"})
     */
    private $images;


    public function __construct() {
        $this->created_at = new \DateTime();
        $this->images  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug() : string  {
        return (new Slugify())->slugify($this->title);
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

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): self
    {
        $this->rooms = $rooms;

        return $this;
    }

    public function getBedrooms(): ?int
    {
        return $this->bedrooms;
    }

    public function setBedrooms(int $bedrooms): self
    {
        $this->bedrooms = $bedrooms;

        return $this;
    }

    public function getFloor(): ?int
    {
        return $this->floor;
    }

    public function setFloor(int $floor): self
    {
        $this->floor = $floor;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormatedPrice() : string {
        return number_format($this->price, 0, '', ' ');
    }

    public function getHeat(): ?int
    {
        return $this->heat;
    }

    public function getHeatType() : string {
        return self::HEAT[$this->heat];
    }

    public function setHeat(int $heat): self
    {
        $this->heat = $heat;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getSold(): ?bool
    {
        return $this->sold;
    }

    public function setSold(bool $sold): self
    {
        $this->sold = $sold;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getPostalcode(): ?string
    {
        return $this->postalcode;
    }

    public function setPostalcode(string $postalcode): self
    {
        $this->postalcode = $postalcode;

        return $this;
    }


    /**
     * @return Collection
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * @param Image $image
     *
     * @return Property
     */
    public function addImage(Image $image) {

        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProperty($this);
        }
        return $this;
    }

    /**
     * Remove image
     *
     * @param Image $image
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
        $image->setProperty(null);
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
                $image->setAdvert(null);
        }
    }


    /**
     * @return int
     */
    public function getImageSize(): ?int {
        return $this->imageSize;
    }



//    /**
//     * @param int $imageSize
//     */
//    public function setImageSize(?int $imageSize): void {
//        $this->imageSize = $imageSize;
//    }
//
    public function setImages($images) {
        $this->images = $images;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type): void {
        $this->type = $type;
    }

    /**
     * @param mixed $mode
     *
     * @return Property
     */
    public function setMode($mode) {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMode() : ?string {
        return $this->mode;
    }
}