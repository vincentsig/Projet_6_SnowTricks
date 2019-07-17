<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class ImageController extends AbstractController{
 


    /**
     * @Route("/{id}", name="image_delete", methods={"DELETE"})
     */
    public function deleteImage(Request $request, Image $image, FileUploader $uploader): Response
    {
        $filename = $image->getFilename();

        if ($this->isCsrfTokenValid('delete'.$image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            $filesystem = new Filesystem();
            $filesystem->remove($uploader->getTargetDirectory() . '/' . $filename);
        }


        return $this->redirectToRoute('trick_index');
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


    
}



