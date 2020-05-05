<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\TrickRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoController extends AbstractController
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
     * @Route("/{id}",
     *      name="video_delete",
     *      methods={"DELETE"})
     * @isGranted("ROLE_USER")
     * 
     */

    public function deleteVideo(Request $request, Video $video, TrickRepository $trickRepository): Response
    {
        $trickId = $video->getTrick();
        $trick = $trickRepository->findOneByid($trickId);

        if ($this->isCsrfTokenValid('delete' . $video->getId(), $request->request->get('_token'))) {

            $this->em->remove($video);
            $this->em->flush();
        }
        return $this->redirectToRoute('trick_edit', [
            'id' => $trick->getId(),
            'slug' => $trick->getSlug(),
        ]);
    }

    /**
     * @Route("/{id}/editVideo/",
     *      name="edit_video",
     *      methods={"GET","POST"})
     * @isGranted("ROLE_USER")
     */
    public function editVideo(Request $request, Video $video, TrickRepository $trickRepository)
    {
        $trickId = $video->getTrick();
        $trick = $trickRepository->findOneByid($trickId);
        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->em->persist($video);
            $this->em->flush();

            return $this->redirectToRoute('trick_edit', [
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }
        return $this->render('trick/editVideo.html.twig', [
            'video' => $video,
            'form' => $form->createView(),
        ]);
    }
}
