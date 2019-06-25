<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\ImageType;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Repository\TrickRepository;
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
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $trick = new Trick();
        
        $trick->setCreatedAt( new \DateTime());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload images
            $files = $trick->getImageFiles();
            foreach($files as $file)
            {
                $fileName = $fileUploader->upload($file);
                
                $trick->AddImage($fileName);
            }
            //------------------------------------------
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }
        
        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="trick_show", methods={"GET", "POST"})
     */
    public function show(Request $request, Trick $trick): Response
    {
        $comment = new Comment();
       
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();
            if ($form->isSubmitted() && $form->isValid()) {
                $comment->setAuthor($user)
                        ->setCreatedAt(new \DateTime())
                        ->setTrick($trick);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
            return $this->redirectToRoute('trick_show', array('id' => $trick->getId()));
        }
        
        return $this->render('trick/show.html.twig', [
            'trick'     =>$trick,
            'comment'   =>$comment,
            'user'      =>$user,
            'form'      =>$form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="trick_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Trick $trick, Image $image, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload images
            $files = $trick->getImageFiles();
            foreach($files as $file)
            {
                
                
                $fileName = $fileUploader->upload($file);
                
                $trick->AddImage($fileName);
                
            }
            //------------------------------------------
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($trick);
            $entityManager->flush();

            return $this->redirectToRoute('trick_index');
        }
        
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }

   

    /**
     * @Route("/{id}/editImage", name="edit_image", methods={"GET","POST"})
     */
    public function editImage(Request $request, Trick $trick, Image $image, FileUploader $fileUploader)
    {

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload images
            $file = $image->getImageFiles();
      
                
                
                $fileName = $fileUploader->upload($file);
                
                $trick->AddImage($fileName);
                
        
        //------------------------------------------
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($trick);
        $entityManager->flush();

        return $this->redirectToRoute('trick_index');
        }
        
        return $this->render('trick/editImage.html.twig', [
            'trick' => $trick,
            'image' =>$image,
            'form' => $form->createView(),
        ]);
    }
    
  
    /**
     * @Route("/{image}", name="image_delete", methods={"DELETE"})
     */
    public function deleteImage(Request $request, Image $image): Response
    {
        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();
        }

        return $this->redirectToRoute('trick_index');
    }


}