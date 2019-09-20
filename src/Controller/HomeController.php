<?php

namespace  App\Controller;

use App\Entity\Property;
use App\Repository\PropertyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    private $session;

    /**
     * PropertyController constructor.
     *
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session) {

        $this->session = $session;
    }

    /**
     * @Route("/", name="index")
     * @param PropertyRepository $repository
     * @return Response
     */
    public function index(PropertyRepository $repository) : Response {
        $newProperties = $repository->findLatest();
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            if ($this->getUser()->getFavoris()) {
                $favorisFromUserOrSession = $this->getUser()->getFavoris()->getItems();
                $this->session->set('favoris', $favorisFromUserOrSession);
            } else {
                $favorisFromUserOrSession = [];
                $this->session->set('favoris', []);
            }
        } else {
            $this->session->set('favoris', []);
            $favorisFromUserOrSession = $this->session->get('favoris');
        }
        $favorisInDB = $this->getDoctrine()->getRepository(Property::class)
            ->findArray(array_values($favorisFromUserOrSession));

        return  $this->render('pages/index.html.twig', ['properties' => $newProperties,
                                                    'favoris'    => $favorisFromUserOrSession
                                                    ]);
    }

//    /**
//     * @Route("/liveMenuProperty", name="home.liveMenuProperty")
//     * @param PropertyRepository $repository
//     * @return Response
//     */
//    public function liveMenuProperty(PropertyRepository $repository, Request $req) : Response {
////        if ($req->isXmlHttpRequest()) {
//        $type  = $req->query->get('type');
//        $localisation  = $req->query->get('localisation');
//        $city  =  explode(",", $localisation);
//        $cp    =  explode(",", $localisation);
//        $price = $req->request->get('price');
//        if($type === 'achat' || $type === 'louer') {
//            $prps  = $repository->findProperties($type, $city[0], $cp[1], $price);
//        } else {
//            $prps  = $repository->estimate($type, $city, $cp);
//        }
//        return $this->redirect($this->generateUrl('property.list',
//            ['listProperties' => 'ok']
//        ));
//
//    }
}