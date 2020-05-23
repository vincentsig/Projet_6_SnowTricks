<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Service\FileUploader;
use App\Repository\TrickRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class ImageController extends AbstractController
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
     * @Route("/{id}",
     *      name="image_delete",
     *      methods={"DELETE"})
     * @isGranted("ROLE_USER")
     *
     */
    public function deleteImage(Request $request, Image $image, TrickRepository $trickRepository, FileUploader $uploader): Response
    {
        $filename = $image->getFilename();
        $trickId = $image->getTrick();
        $trick = $trickRepository->findOneByid($trickId);

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $this->em->remove($image);
            $this->em->flush();

            $uploader->removeFile($image, $filename);
        }

        return $this->redirectToRoute('trick_edit', [
            'id' => $trick->getId(),
            'slug' => $trick->getSlug(),
        ]);
    }

    /**
     * @Route("/{id}",
     *      name="cover_delete",
     *      methods={"DELETE"})
     * @isGranted("ROLE_USER")
     */
    public function deleteCover(Request $request, Image $image, FileUploader $uploader): Response
    {
        $filename = $image->getFilename();

        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $this->em->remove($image);
            $this->em->flush();

            $uploader->removeFile($image, $filename);
        }
        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/editImage/{id}",
     *      name="edit_image",
     *      methods={"GET","POST"})
     * @isGranted("ROLE_USER")
     */
    public function editImage(Request $request, Image $image, TrickRepository $trickRepository, FileUploader $fileUploader)
    {
        $trickId = $image->getTrick();
        $trick = $trickRepository->findOneByid($trickId);
        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tempfilename = $image->getFilename();
            $file = $image->getFile();
            $fileName = $fileUploader->upload($file, $image);
            $image->setFilename($fileName);

            $this->em->persist($image);
            $this->em->flush();

            $filesystem = new Filesystem();
            $filesystem->remove($fileUploader->getTargetDirectory() . '/' . $tempfilename);

            return $this->redirectToRoute('trick_edit', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }
        return $this->render('trick/editImage.html.twig', [
            'image' => $image,
            'form' => $form->createView(),
        ]);
    }
}
