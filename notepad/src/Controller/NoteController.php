<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Entity\Note;
use App\Repository\NoteRepository;
use App\Form\NoteType;

class NoteController extends AbstractController {

    #[Route('/notes', name: 'notes.all')]
    public function notes(NoteRepository $noteRepository, AuthenticationUtils $authenticationUtils): Response {

        $lastUsername = $authenticationUtils->getLastUsername();
        $notes = $noteRepository->findBy(array('email'=>$lastUsername));

        return $this->render('note/index.html.twig', [
            'notes'=>$notes
        ]);
    }

    #[Route('/create', name: 'create')]
    public function notes_create(Request $request, AuthenticationUtils $authenticationUtils): Response {

        $note = new Note();
        $lastUsername = $authenticationUtils->getLastUsername();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $em = $this->getDoctrine()->getManager();
            $note->setEmail($lastUsername);
            $em->persist($note);
            $em->flush();

            return $this->redirect($this->generateUrl('notes.all'));
        }

        return $this->render('note/create.html.twig', [
            'form' =>$form->createView()
        ]); 
    }
}