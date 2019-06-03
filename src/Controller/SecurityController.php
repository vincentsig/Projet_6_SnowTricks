<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Form\ForgottenPasswordType;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }



    /**
     * @Route("/login", name="app_login", methods={"GET", "POST"})
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user   
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error]);
    }

    
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        $this->addFlash('success','Vous êtes déconnecté');   
    }

    /**
    * @Route("/logout_message", name="logout_message")
    */
    public function logoutMessage()
    {
        $this->addFlash('success', 'Vous êtes déconnecté');
        return $this->redirectToRoute('home');
    }

    /**
    * @Route("/register", name="app_register")
    * @param User $user
    */
    public function register(Request $request, ObjectManager $em, UserPasswordEncoderInterface $encoder,\Swift_Mailer $mailer,
    TokenGeneratorInterface $tokenGenerator,LoginFormAuthenticator $authenticator,  GuardAuthenticatorHandler $guardHandler)
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        $token = $request->query->get('token');
        if($token){
        dump($token);
        $em = $this->getDoctrine()->getManager();
        $user = $this->repository->findOneByConfirmationToken($token);
        dump($user);    
           
            if ($user=== null)
            {
                $this->addFlash('success', 'Token Inconnu, veuillez créer un compte valide');
                return $this->redirectToRoute('home');
            }
            else
            {
                $em = $this->getDoctrine()->getManager();
                $user = $this->repository->findOneByConfirmationToken($token);
                $user->setCreatedAt( new \DateTime());
                $user->setConfirmationToken(null);
                $em->flush();
                $this->addFlash('success', 'votre compte à bien été validé');
                //login after validation of the ConfirmationToken
                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,          // the User object you just created
                    $request,
                    $authenticator, // authenticator whose onAuthenticationSuccess you want to use
                    'main'          // the name of your firewall in security.yaml
                );
            }
        }  
        elseif($form->isSubmitted() && $form->isValid())
        {
            $token = $tokenGenerator->generateToken();
            $user->setConfirmationToken($token);
           
            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            // generate a url for confirmation account
            $url = $this->generateUrl('app_register', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('SnowTricks : confirmation de votre compte'))
                ->setFrom('noreplya@server.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "Veuillez cliquer sur le lien pour valider votre compte : " . $url,
                    'text/html'
                );
 
            $mailer->send($message);
 
            $this->addFlash('success', 'Un email vous à été envoyé, veuillez cliquer sur le lien pour valider votre compte');

            return $this->redirectToRoute('home');
        }
        else
        {
            return $this->render('security/register.html.twig', [
                'form'  => $form->createView(),
                
                ]);  
        }
    }


    /**
    * @Route("/forgottenPassword", name="app_forgotten_password")
    * @param User $user
    */
    public function forgottenPassword(Request $request,ObjectManager $em,\Swift_Mailer $mailer,
        TokenGeneratorInterface $tokenGenerator): Response
    {
        

        $form = $this->createForm(ForgottenPasswordType::class);
        $form->handleRequest($request);
        
        
        
        if($form->isSubmitted() && $form->isValid())  
        {
            
            $email = $form->get('email')->getData();

            $em = $this->getDoctrine()->getManager();
            $user = $this->repository->findOneByEmail($email);
            /* @var $user User */
           
            if ($user === null) {
                
                $this->addFlash('success', 'Email Inconnu');
                return $this->redirectToRoute('app_login');
            }

            $token = $tokenGenerator->generateToken();

            try{
                $user->setResetToken($token);
                $em->flush();
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('home');
            }
            // generate url for reset the password
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
 
            $message = (new \Swift_Message('SnowTricks : reinitialisation mot de passe'))
                ->setFrom('noreplya@server.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    "Veuillez cliquer sur le lien pour réinitialiser votre mot de passe : " . $url,
                    'text/html'
                );
 
            $mailer->send($message);
 
            $this->addFlash('success', 'Un email vous à été envoyé, veuillez cliquer sur le lien pour réinitialiser votre mot de passe');
  
            return $this->redirectToRoute('home');
        }
                 
        return $this->render('security/forgotten_password.html.twig' ,[
            'form' => $form->createView()
            ]);
    }
    
    /**
     * @Route("/reset_password/{token}", name="app_reset_password")
     * @param User $user
     */
    public function resetPassword(Request $request, string $token, ObjectManager $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(ResetPasswordType::class);
      
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        $user = $this->repository->findOneByResetToken($token);  
      
        if($form->isSubmitted() && $form->isValid())  
        {
        
            $em = $this->getDoctrine()->getManager();
            $user = $this->repository->findOneByResetToken($token);
            
           
            if ($user=== null)
            {
                $this->addFlash('success', 'Token Inconnu');
                return $this->redirectToRoute('home');
            }
 
           
            $user->setResetToken(null);
            $user->setPassword($passwordEncoder->encodePassword($user, $form->get('password')->getData()));
            $em->flush();
 
            $this->addFlash('success', 'Mot de passe mis à jour');
 
            return $this->redirectToRoute('home');
           
        }else
        {
 
            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView(),
                'token' => $token]);
             
        }

    }



}
