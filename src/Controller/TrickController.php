<?php

namespace App\Controller;

use DateTime;
use App\Entity\Trick;
use App\Entity\Comment;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Service\FileUploader;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/trick")
 */
class TrickController extends AbstractController
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
     * @Route("/",
     *      name="trick_index",
     *      methods={"GET"})
     */
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new",
     *      name="trick_new",
     *      methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function new(Request $request, FileUploader $fileUploader): Response
    {
        $trick = new Trick();

        $trick->setCreatedAt(new DateTime());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $files = $trick->getImageFiles();
            if ($files) {
                foreach ($files as $file) {
                    $fileName = $fileUploader->upload($file, $trick);
                    $trick->AddImage($fileName);
                }
            }
            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Votre Trick à bien été enregistré');

            return $this->redirectToRoute('home');
        }
        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{slug}/{id}",
     *      name="trick_show",
     *      methods={"GET", "POST"})
     */
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository): Response
    {
        $user = $this->getUser();
        $comment = new Comment();
        $lastcomments = $commentRepository->findLastComment($trick->getId());

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($user)
                ->setTrick($trick);

            $this->em->persist($comment);
            $this->em->flush();
            $this->addFlash('success', 'Votre commentaire à été publié');
            return $this->redirectToRoute('trick_show', [
                'id' => $trick->getId(),
                'category' => $trick->getCategory(),
                'slug' => $trick->getSlug()
            ]);
        }
        return $this->render('trick/show.html.twig', [
            'trick'     => $trick,
            'lastcomments' => $lastcomments,
            'comment'   => $comment,
            'user'      => $user,
            'form'      => $form->createView(),
        ]);
    }

    /**
     * @Route("/edit/{slug}/{id}",
     *      name="trick_edit",
     *      methods={"GET","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Trick $trick, FileUploader $fileUploader): Response
    {

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $trick->SetUpdatedAt(new DateTime());
            $files = $trick->getImageFiles();
            foreach ($files as $file) {
                $fileName = $fileUploader->upload($file, $trick);
                $trick->AddImage($fileName);
            }
            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Votre Trick à bien été modifié');

            return $this->redirectToRoute('trick_show', [
                'trick' => $trick,
                'id' => $trick->getId(),
                'slug' => $trick->getSlug(),
            ]);
        }
        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'id' => $trick->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete",
     *      name="trick_delete",
     *      methods={"DELETE","POST"})
     * @IsGranted("ROLE_USER")
     */
    public function deleteTrick(Request $request, Trick $trick, ImageRepository $imageRepository, FileUploader $uploader): Response
    {
        $images = $imageRepository->findByTrick($trick->getId());

        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $this->em->remove($trick);
            $this->em->flush();
        }
        $uploader->removeAllFiles($images);

        return $this->redirectToRoute('home');
    }

    /**
     * Get the 5 next comments in the database and create a Twig file with them that will be displayed via Javascript
     *
     * @Route("/{id}/{start}",
     *      name="loadMoreComments",
     *      requirements={"start": "\d+"})
     */
    public function loadMoreComments(CommentRepository $commentRepository, $id, $start = 5)
    {
        $lastcomments = $commentRepository->findLastComment($id);
        return $this->render('trick/loadMoreComments.html.twig', [
            'lastcomments' => $lastcomments,
            'start' => $start
        ]);
    }
}
