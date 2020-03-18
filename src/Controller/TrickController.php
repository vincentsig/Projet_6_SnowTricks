<?php

namespace App\Controller;


use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Service\FileUploader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
{
    /**
     * @Route("/", name="trick_index", methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="trick_new", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $trick = new Trick();
        $trick->setCreatedAt(new \DateTime());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            //upload images
            $files = $trick->getImageFiles();
            foreach ($files as $file) {
                $fileName = $fileUploader->upload($file);

                $trick->AddImage($fileName);
            }
            //------------------------------------------

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();



            return $this->redirectToRoute('home');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),

        ]);
    }

    /**
     * @Route("/show/{id}/{slug}", name="trick_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Trick $trick): Response
    {

        $comment = new Comment();

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);


        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($user)
                ->setCreatedAt(new \DateTime())
                ->setTrick($trick);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('trick_show', array(
                'id' => $trick->getId(),
                'category' => $trick->getCategory(),
                'slug' => $trick->getSlug()

            ));
        }

        return $this->render('trick/show.html.twig', [
            'trick'     => $trick,
            'comment'   => $comment,
            'user'      => $user,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{id}/{slug}", name="trick_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Trick $trick, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload images
            $files = $trick->getImageFiles();
            foreach ($files as $file) {

                $fileName = $fileUploader->upload($file);
                $trick->AddImage($fileName);
            }

            //------------------------------------------
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="trick_delete", methods={"DELETE","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteTrick(Request $request, Trick $trick, ImageRepository $imageRepository, FileUploader $uploader): Response
    {

        $images = $imageRepository->findByTrick($trick->getId());


        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($trick);
            $entityManager->flush();
        }

        $filesystem = new Filesystem();
        foreach ($images as $image) {

            $filename = $image->getFilename();
            $filesystem->remove($uploader->getTargetDirectory() . '/' . $filename);
        }


        return $this->redirectToRoute('home');
    }

    /**
     * Get the 5 next comments in the database and create a Twig file with them that will be displayed via Javascript
     * 
     * @Route("/{id}/{start}", name="loadMoreComments", requirements={"start": "\d+"})
     */
    public function loadMoreComments(TrickRepository $trickRepository, $id, $start = 5)
    {
        $trick = $trickRepository->findOneByid($id);

        return $this->render('trick/loadMoreComments.html.twig', [
            'trick' => $trick,
            'start' => $start
        ]);
    }
}
