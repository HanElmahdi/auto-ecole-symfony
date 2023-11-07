<?php

namespace App\Controller;

use App\Entity\Instructeur;
use App\Form\InstructeurType;
use App\Repository\InstructeurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/instructeur')]
class InstructeurController extends AbstractController
{
    #[Route('/', name: 'app_instructeur_index', methods: ['GET'])]
    public function index(InstructeurRepository $instructeurRepository): Response
    {
        return $this->render('instructeur/index.html.twig', [
            'instructeurs' => $instructeurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_instructeur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $instructeur = new Instructeur();
        $form = $this->createForm(InstructeurType::class, $instructeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($instructeur);
            $entityManager->flush();

            return $this->redirectToRoute('app_instructeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instructeur/new.html.twig', [
            'instructeur' => $instructeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instructeur_show', methods: ['GET'])]
    public function show(Instructeur $instructeur): Response
    {
        return $this->render('instructeur/show.html.twig', [
            'instructeur' => $instructeur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_instructeur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Instructeur $instructeur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(InstructeurType::class, $instructeur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_instructeur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('instructeur/edit.html.twig', [
            'instructeur' => $instructeur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_instructeur_delete', methods: ['POST'])]
    public function delete(Request $request, Instructeur $instructeur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$instructeur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($instructeur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_instructeur_index', [], Response::HTTP_SEE_OTHER);
    }
}
