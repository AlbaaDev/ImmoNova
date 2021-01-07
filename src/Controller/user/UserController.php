<?php


namespace App\Controller\user;


use App\Entity\User;
use App\Form\ChangePassword;
use App\Form\EmailResetFormType;
use App\Form\PasswordResetFormType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController  extends  AbstractController {

    /**
     * @var ObjectManager
     */
    private $om;
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * PropertyController constructor.
     *
     * @param ObjectManager                $om
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(EntityManagerInterface $om,
                                UserPasswordEncoderInterface $passwordEncoder) {
        $this->om = $om;
        $this->passwordEncoder = $passwordEncoder;
    }


    /**
     * @Route("/user/settings", name="user.settings")
     * @param Request $request
     *
     * @return Response
     */
    public function updateUserCredentials(Request $request) : Response {
        $user = $this->getUser();
        $passwordForm = $this->createForm(PasswordResetFormType::class, $user);
        $passwordForm->handleRequest($request);
        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $currentPassword = $request->request->get('password_reset_form')['currentPassword'];
            if ($this->passwordEncoder->isPasswordValid($user, $currentPassword)) {
                $newPassword = $request->request->get('password_reset_form')['newPassword']['first'];
                $newEncodedPassword = $this->passwordEncoder->encodePassword($user, $newPassword);
                $user->setPassword($newEncodedPassword);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $this->om->flush();
                $this->addFlash(
                    'success',
                    'Mot de passe changé avec succès.'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Mot de passe incorrect.'
                );
            }
        }
        $emailForm = $this->createForm(EmailResetFormType::class, $user);
        $emailForm->handleRequest($request);
        if ($emailForm->isSubmitted() && $emailForm->isValid()) {
            $emailInput = $request->request->get('email_reset_form')['email'];
            if($user->isEmailValid($emailInput)) {
                $newEmail = $user->getNewEmail();
                $user->setEmail($newEmail);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $this->om->flush();
                $this->addFlash(
                    'success',
                    'Email changé avec succès.'
                );
            } else {
                $emailForm->get('email')
                    ->addError(new FormError('Cette valeur doit être l\'adresse email actuelle de l\'utilisateur'));
            }
        }
        $this->getDoctrine()->getManager()->refresh($user);
        return  $this->render('user/settings.html.twig', ['user' => $user,
                                                      'passwordForm' => $passwordForm->createView(),
                                                      'emailForm' => $emailForm->createView()
                                                     ]);
    }

    /**
     * @Route("/user/updatePassword", name="user.updatePassword")
     * @return Response
     */
    public function updatePassword(Request $request) : Response {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
    }

    /**
     * @Route("/user/updateEmail", name="user.updateEmail")
     * @return Response
     */
    public function updateEmail() : Response {

    }

}