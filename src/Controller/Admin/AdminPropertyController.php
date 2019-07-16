<?php


namespace App\Controller\Admin;


use App\Entity\Property;
use App\Form\PropertyType;
use App\Repository\PropertyRepository;;

use Doctrine\Common\Persistence\ObjectManager;
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
    private $em;


    /**
     * AdminPropertyController constructor.
     * @param PropertyRepository $repository
     * @param ObjectManager $em
     */
    public function __construct(PropertyRepository  $repository, ObjectManager $em) {


        $this->repository = $repository;
        $this->em = $em;
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
            $this->addFlash(
                'success',
                'Propriété crée avec succès'
            );
            $this->em->persist($property);
            $this->em->flush();
            return $this->redirectToRoute('admin.property.index');
        }

        return $this->render('admin/property/create.html.twig',
            [   'property'  => $property,
                'form'      => $form->createView()
            ]);
    }

    /**
     * Editer une propriétée
     * @Route("/admin/property/edit/{id}", name="admin.property.edit")
     * @param Property           $property
     * @param Request            $request
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function edit(Property $property, Request $request,
                         ValidatorInterface $validator) : Response {

        $form = $this->createForm(PropertyType::class, $property);
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $errors = $validator->validate($property);
            if(count($errors) > 0) {
                $errorsString = (String) $errors;
                return new Response($errorsString);
            }
        }
        if($form->isSubmitted() && $form->isValid()) {
            $this->em->flush();
            $this->addFlash(
                'success',
                'Propriété modifiée avec succès'
            );
            return $this->redirectToRoute('admin.property.index');
        }
        return $this->render('admin/property/edit.html.twig',
            ['property' => $property,
             'form' => $form->createView()
            ]);
    }

    /**
     * @param Property $property
     * @Route("/admin/property/delete/{id}", name="admin.property.delete")
     * @return Response
     */
    public function delete(Property $property, Request $request) : Response {

        $submittedToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-item', $submittedToken)) {
            $this->em->remove($property);
            $this->em->flush();
            $this->addFlash(
                'success',
                'Propriété supprimée avec succès'
            );
            return $this->redirectToRoute('admin.property.index');

        } else {
            $this->addFlash(
                'warning',
                'Erreur lors de la tentative de suppréssion.'
            );
            return $this->redirectToRoute('admin.property.index');
        }
    }

}