<?php


namespace App\Form\DataTransformer;


use App\Entity\Image;
use App\Entity\Property;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ArrayToImageTransformer implements DataTransformerInterface {

    private $entityManager;
    private $om;
    public function __construct(EntityManagerInterface $entityManager, EntityManagerInterface $om)
    {
        $this->entityManager = $entityManager;
        $this->om = $om;
    }

    /**
     * Transforms an object image to a string (number).
     *
     * @param $image
     *
     * @return string
     */
    public function transform($image)
    {
        return $image;
    }

    /**
     * Transforms an array to an object (issue).
     *
     * @param array $arrayImages
     *
     * @return array
     * @throws \Exception
     */
    public function reverseTransform($arrayImages)
    {
        $imageCollection = [];
        foreach ($arrayImages as $elem) {
            $image = new Image();
            $image->setImageFile($elem['image']);
            $image->setImageSize($elem['image']->getClientSize());
            $image->setUpdatedAt(new \DateTime());

            $imageCollection[] = $image;

            dump($imageCollection);
        }
        return  $imageCollection;
    }

}