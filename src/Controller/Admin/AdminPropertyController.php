<?php


namespace App\Controller\Admin;


use App\Entity\Property;
use App\Form\DataTransformer\ArrayToImageTransformer;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AdminPropertyController extends  AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;

    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var ArrayToImageTransformer
     */
    private $transformer;

    /**
     * AdminPropertyController constructor.
     *
     * @param PropertyRepository      $repository
     * @param ObjectManager           $om
     * @param ArrayToImageTransformer $transformer
     */
    public function __construct(PropertyRepository  $repository, ObjectManager $om,
                                ArrayToImageTransformer $transformer) {
        $this->repository = $repository;
        $this->transformer = $transformer;
        $this->om = $om;
    }

    /**
     * @Route("/admin", name="admin.property.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index() : Response {
        $properties = $this->repository->findAll();
        return $this->render('admin/property/index.html.twig', compact('properties'));
    }


    /**
     * Créer une nouvelle propriétée
     * @Route("/admin/property/create", name="admin.property.create")
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request) : Response {
        $property = new Property();
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $this->om->persist($property);
            $this->om->flush();
            $this->addFlash(
                'success',
                'Propriété crée avec succès'
            );
            return $this->redirectToRoute('property.index');
        }
        return $this->render('admin/property/create.html.twig',
            ['property'     => $property,
             'form'         => $form->createView(),
             'soldBoxe'     => false
            ]);
    }

    /**
     * Editer une propriétée
     * @Route("/admin/property/edit/{id}", name="admin.property.edit")
     *
     * @param Property $property
     * @param Request  $request
     *
     * @return Response
     */
    public function edit(Property $property, Request $request) : Response {
//        foreach ($property->getImages() as $image) {
//            $image->setImageFileName(
//                new File($this->getParameter('property_images').'/'.$image->getImageName()));
//        }
        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);

        if($form->isSubmitted() && ($form->isValid())) {
                $this->om->flush();
                $this->addFlash('success','Propriété modifiée avec succès');
                return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig',
            ['property'     => $property,
             'form'         => $form->createView(),
             'soldBoxe'     => true
            ]);
    }


    /**
     * @param Property $property
     * @param Request  $request
     *
     * @return Response
     * @Route("/admin/property/delete/{id}", name="admin.property.delete", methods={"DELETE"})
     */
    public function delete(Property $property, Request $request) : Response {

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-item' . $property->getId(), $submittedToken)) {
            $this->om->remove($property);
            $this->om->flush();
            $this->addFlash(
                'success',
                'Propriété supprimée avec succès'
            );
            return $this->redirectToRoute('admin.property.index');

        } else {
            $this->addFlash(
                'danger',
                'Erreur lors de la tentative de suppréssion.'
            );
            return $this->redirectToRoute('admin.property.index');
        }
    }

}