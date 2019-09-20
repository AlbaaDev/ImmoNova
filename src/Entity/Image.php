<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $imageName;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="property_images", fileNameProperty="imageName")
     * @Assert\Image(
     *     mimeTypes = {"image/jpeg", "image/jpg"},
     *     mimeTypesMessage = "Format du fichier invalide.Format acceptés : jpeg ou jpg ")
     */
    private $imageFile;

    /**
     * @ManyToOne(targetEntity="Property", inversedBy="images")
     * @ORM\JoinColumn(name="property_id", referencedColumnName="id")
     */
    private $property;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $imageSize;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;


    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $imageName
     *
     * @return Image
     */
    public function setImageName(?string $imageName): Image {
        $this->imageName = $imageName;
        return $this;
    }

    /**
     * @return string
     */
    public function getImageName(): ?string {
        return $this->imageName;
    }

    /**
     * @param File|null $imageFile
     *
     * @return Image
     * @throws \Exception
     */
    public function setImageFile(?File $imageFile): Image {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File {
        return $this->imageFile;
    }

    /**
     * @param mixed $property
     *
     * @return Image
     */
    public function setProperty($property) {
        $this->property = $property;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * @param int $imageSize
     *
     * @return Image
     */
    public function setImageSize(int $imageSize): Image {
        $this->imageSize = $imageSize;
        return $this;
    }

    /**
     * @return int
     */
    public function getImageSize(): int {
        return $this->imageSize;
    }

    public function setImageFilename($imageName)
    {
        $this->imageName = $imageName;

        return $this;
    }

}
