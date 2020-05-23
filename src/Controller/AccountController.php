<?php

namespace App\Controller;

use App\Form\AccountType;
use App\Service\FileUploader;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
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
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/index",
     *      name="account_index",
     *      methods={"GET","POST"})
     */
    public function index(Request $request, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        $form = $this->createForm(AccountType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('avatar')->getData();
            if ($file) {
                $avatarfile = $profile->getAvatarFileName();
                $filename = $fileUploader->upload($file, $profile);
                $profile->setAvatarFileName($filename);
                $fileUploader->removeFile($profile, $avatarfile);
            }

            $this->em->persist($profile);
            $this->em->flush();

            return $this->redirectToRoute('home');
        }
        return $this->render('account/index.html.twig', [
            'profile' => $profile,
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
