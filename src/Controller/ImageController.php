<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Service\FileUploader;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class ImageController extends AbstractController
{



    /**
     * @Route("/{id}", name="image_delete", methods={"DELETE"})
     */
    public function deleteImage(Request $request, Image $image, FileUploader $uploader): Response
    {
        $filename = $image->getFilename();


        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            $filesystem = new Filesystem();
            $filesystem->remove($uploader->getTargetDirectory() . '/' . $filename);
        }


        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/{id}", name="cover_delete", methods={"DELETE"})
     */
    public function deleteCover(Request $request, Image $image, Trick $trick, FileUploader $uploader): Response
    {
        $filename = $image->getFilename();


        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($image);
            $entityManager->flush();

            $filesystem = new Filesystem();
            $filesystem->remove($uploader->getTargetDirectory() . '/' . $filename);
        }


        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/{id}/editImage/", name="edit_image", methods={"GET","POST"})
     */
    public function editImage(Request $request, Image $image, FileUploader $fileUploader)
    {

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //upload images
            $tempfilename = $image->getFilename();

            $file = $image->getFile();

            $fileName = $fileUploader->upload($file);

            $image->setFilename($fileName);



            //------------------------------------------
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($image);
            $entityManager->flush();

            $filesystem = new Filesystem();
            $filesystem->remove($fileUploader->getTargetDirectory() . '/' . $tempfilename);

            return $this->redirectToRoute('home');
        }

        return $this->render('trick/editImage.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }
}
