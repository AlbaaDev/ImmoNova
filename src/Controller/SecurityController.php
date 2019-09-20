<?php

namespace App\Controller;

use App\Repository\PropertyRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $session;

    /**
     * PropertyController constructor.
     *
     * @param PropertyRepository            $repository
     * @param SessionInterface              $session
     * @param ObjectManager                 $om
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(PropertyRepository $repository, SessionInterface $session,
                                ObjectManager $om,
                                AuthorizationCheckerInterface $authChecker) {

        $this->session = $session;
    }

    /**
     * @Route("/login", name="app_login", methods={"GET|POST"})
     * @param AuthenticationUtils $authenticationUtils
     * @param SessionInterface    $session
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->session->set('favoris', []);
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

}
