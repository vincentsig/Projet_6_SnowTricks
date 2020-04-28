<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/account")
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @var ObjectManager
     */
    private $em;

    public function __construct(ObjectManager $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/index", name="account_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        $user = $this->getUser();

        return $this->render('account/index.html.twig', [
            'user' => $user,

        ]);
    }

    /**
     * @Route("/edit", name="account_edit", methods={"GET","POST"})
     */


    public function edit(Request $request, FileUploader $fileUploader): Response
    {

        $user = $this->getUser();
        $profile = $user->getProfile();
        $form = $this->createForm(AccountType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //upload avatar
            $file = $form->get('avatar')->getData();
            $filename = $fileUploader->upload($file, $profile);
            //set Avatar to null to avoid serialization of File
            $profile->setAvatar(null);
            $profile->setAvatarFileName($filename);
            //------------------------------------------
            $this->em->persist($profile);
            $this->em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('account/edit.html.twig', [
            'profile' => $profile,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
