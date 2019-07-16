<?php

namespace  App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PropertyController extends  AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * PropertyController constructor.
     * @param PropertyRepository $repository
     * @param ObjectManager $em
     */
    public function __construct(PropertyRepository $repository, ObjectManager $em) {

        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/proprietes", name="property.index")
     */
    public function index(PropertyRepository $repository) : Response {
        $properties = $repository->findAll();
        return $this->render('property/index.html.twig',
            ['current_menu' => 'properties',
             'properties'   => $properties
            ]);
    }

    /**
     * @Route("/proprietes/{slug}-{id}", name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @return Response
     */
    public function show(Property $property, string $slug) : Response {
        $prpSlug = $property->getSlug();
        if($prpSlug !== $slug) {
            return $this->redirectToRoute('property.show',
                ['id'   => $property->getId(),
                    'slug' => $prpSlug],
                301
            );
        }
        return $this->render('property/show.html.twig',
            ['property'     => $property,
             'current_menu' => 'properties'
            ]);
    }
}