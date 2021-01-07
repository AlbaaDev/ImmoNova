<?php

namespace  App\Controller;

use App\Entity\Favoris;
use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PropertyController extends  AbstractController {

    /**
     * @var PropertyRepository
     */
    private $repository;

    private $session;

    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;
    /**
     * @var PropertyAccess
     */
    private $propertyAccess;


    /**
     * PropertyController constructor.
     *
     * @param PropertyRepository            $repository
     * @param SessionInterface              $session
     * @param ObjectManager                 $om
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(PropertyRepository $repository, SessionInterface $session,
        EntityManagerInterface $om, AuthorizationCheckerInterface $authChecker) {

        $this->repository = $repository;
        $this->session = $session;
        $this->om = $om;
        $this->authChecker = $authChecker;
    }

    /**
     * @Route("/proprietes", name="property.index")
     * @param PropertyRepository $repository
     *
     * @return Response
     */
    public function index(PropertyRepository $repository) : Response {
        $properties = $repository->findAllVisible();
        return  $this->render('property/index.html.twig', [
                                                        'current_menu' => 'properties',
                                                        'properties'   => $properties
                                                      ]);
    }

    /**
     * @Route("/louer", name="property.louer")
     * @param PropertyRepository $repository
     *
     * @return Response
     */
    public function louer(PropertyRepository $repository) : Response {
        $properties = $repository->findAllVisible();
        return $this->render('property/index.html.twig',
            ['current_menu' => 'louer',
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

    /**
     * @Route("/proprietes", name="property.list")
     * @param Request $request
     *
     * @return Response
     */
    public function list(Request $request) : Response {
        $prps = $request->query->get('listProperties');
        return  $this->render('property/list.html.twig', ['properties' => $prps]);
    }

    /**
     * @Route("/proprietes/ajouterFavoris/{id}", name="property.ajouterFavoris")
     * @param Property $property
     *
     * @param Request  $request
     *
     * @return Response
     */
    public function ajouterFavoris(Property $property, Request $request, PropertyRepository $repository) : Response {
        if ($request->isXmlHttpRequest()) {
            if(!$this->session->has('favoris'))
                $this->session->set('favoris', []);
            $favorisSession = $this->session->get('favoris');
            if (!in_array($property->getId(), $favorisSession)) {
                $favorisSession[] = $property->getId();
            }
            $this->session->set('favoris', $favorisSession);
            if($this->getUser()->getfavoris()) {
                $this->getUser()->getfavoris()->setItems($favorisSession);
                $newUserFavorisObject = $this->getUser()->getFavoris();
                $newUserFavoris = $newUserFavorisObject->getItems();
                $this->om->persist($newUserFavorisObject);
                $this->om->flush();
            } else {
                $favorisToDB = new Favoris($favorisSession, session_id(), $this->getUser());
                $newUserFavoris = $favorisToDB->getItems();
                $this->getUser()->setFavoris($favorisToDB);
                $this->om->persist($favorisToDB);
                $this->om->flush();
            }
            $this->session->set('favoris',  $newUserFavoris);
            $favoris = $this->getDoctrine()->getRepository(Property::class)
                ->findArray(array_values($newUserFavoris));
            $properties = $repository->findAllVisible();
            $nbFavoris = count($newUserFavoris);
            return new JsonResponse(['nbFavoris'  => $nbFavoris ]);
        }
    }

    /**
     * @Route("/proprietes/favoris/", name="property.showFavoris")
     * @param Request $request
     *
     * @return Response
     */
    public function showFavoris() : Response {
        if(!$this->session->has('favoris'))
            $this->session->set('favoris', []);
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if($this->getUser()->getfavoris()) {
                $favorisFromUser = $this->getUser()->getfavoris()->getItems();
                $this->session->set('favoris', $favorisFromUser);
            } else {
                $favorisSession = $this->session->get('favoris');
                $favorisToDB = new Favoris($favorisSession, session_id(), $this->getUser());
                $newUserFavoris = $favorisToDB->getItems();
                $this->session->set('favoris', $newUserFavoris);
                $this->getUser()->setFavoris($favorisToDB);
                $this->om->persist($favorisToDB);
                $this->om->flush();
            }
        }
        $favorisFromSession  = $this->session->get('favoris');
        $properties = $this->getDoctrine()->getRepository(Property::class)
            ->findArray(array_values($favorisFromSession));
        return  $this->render('user/favoris.html.twig', ['properties' => $properties]);
    }

    /**
     * @Route("/proprietes/clear/", name="property.clear")
     * @return Response
     */
    public function clear() : Response {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->getUser()->getFavoris()->setItems(null);
        }
        $this->session->set('favoris', []);
        $this->om->flush();
        return  $this->render('user/favoris.html.twig');
    }

    /**
     * @Route("/proprietes/removeFavoris/{id}", name="property.removeFavoris")
     * @param $id
     *
     * @return Response
     */
    public function removeFavoris($id, Request $request) : Response {
        if ($request->isXmlHttpRequest()) {
            $userSession = $this->session->get('favoris');
            if ($this->getUser()) {
                $userFavoris = $this->getUser()->getFavoris()->getItems();
                if (($key = array_search($id, $userFavoris)) !== false) {
                    unset($userFavoris[$key]);
                }
                $this->getUser()->getFavoris()->setItems($userFavoris);
            }
            if (($key = array_search($id, $userSession)) !== false) {
                unset($userSession[$key]);
                $this->session->set('favoris', $userSession);
            }
            $this->om->flush();
            $nbFavoris = count($userFavoris);

            $propertyAccess = PropertyAccess::createPropertyAccessor();
            $tableData = "";
            if($nbFavoris == 0) {
                 $tableData .= '<td style="width: 100%" class="text-center">Aucun favoris. </td>';
            } else {
                $properties = $this->getDoctrine()->getRepository(Property::class)
                    ->findArray(array_values($userFavoris));
                foreach ($properties as $property) {
                    $url = $this->generateUrl('property.show',
                                            ['id'   => $propertyAccess->getValue($property, 'id'),
                                             'slug' => $propertyAccess->getValue($property, 'slug')]);
                    $tableData .=
                        '<tr>
                             <td>'.$propertyAccess->getValue($property, 'title').'</td>'
                            .'<td>'.$propertyAccess->getValue($property, 'adress').'</td>'
                            .'<td>'.$propertyAccess->getValue($property, 'price').'</td>'
                            .'<td id="parent'.$propertyAccess->getValue($property, 'id').'">'
                                .'<a class="btn ml-2 btnFavoris"
                                     data-id="'.$propertyAccess->getValue($property, 'id').'"
                                     data-type="removeFavoris">
                                        <i class="far fa-trash-alt"></i>
                                 </a>'
                                .'<a class="link ml-2" href="'.$url.'">
                                    <i class="far fa-eye"></i>
                                 </a>
                            </td>
                        </tr>';
                }
            }

            return new JsonResponse(['nbFavoris' => $nbFavoris, 'tableData' => $tableData]);
        }
        $favorisFromSession  = $this->session->get('favoris');
        $properties = $this->getDoctrine()->getRepository(Property::class)
            ->findArray(array_values($favorisFromSession));
        return  $this->render('user/favoris.html.twig', ['properties' => $properties]);
    }

    /**
     * @Route("/proprietes/searchProperties/", name="property.searchProperties")
     * @param Request $request
     *
     * @return void
     */
    public function searchProperties(Request $request) {
        $localisation = explode(",", $request->get('localisation'));
        $city = $localisation[0];
        $type = $request->get('type');
        $price = $request->get('price');
        $mode = $request->get('mode');

        $properties = $this->om->getRepository(Property::class)
            ->findProperties($type, $city, $price, $mode);
        if (!$properties) {
            $this->addFlash('warning','Aucun résultat ne correspond à votre recherche');
            return $this->redirectToRoute('index');
        }

        return  $this->render('property/list.html.twig', ['properties' => $properties]);
    }
}