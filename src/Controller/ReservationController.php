<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Stripe;

#[Route('/reservation')]
class ReservationController extends AbstractController
{
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, AuthorizationCheckerInterface $authorizationChecker): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($this->isGranted('ROLE_ETUDIANT')) {
                /** @var Etudiant $etudiant */
                $etudiant = $this->getUser()->getEtudiant();
                $reservation->setEtudiant($etudiant);
            }
            $amount = 0;
            switch ($reservation->getTypePermis()) {
                case 'Formule A':
                    $amount = 3300;
                    break;
                case 'Formule B':
                    $amount = 3550;
                    break;
                case 'Formule C':
                    $amount = 3800;
                    break;
                case 'Formule D':
                    $amount = 4050;
                    break;
            }
            $reservation->setAmount($amount);

            $entityManager->persist($reservation);

            try {
                try {
                    Stripe\Stripe::setApiKey($_ENV["STRIPE_SECRET"]);
                    Stripe\Charge::create ([
                            "amount" => $amount * 100,
                            "currency" => "mad",
                            "source" => $request->request->get('stripeToken'),
                            "description" => "Auto école Payment Test : ". $reservation->getTypePermis()
                    ]);
                    $reservation->setTransactionState("SUCCESS");
                    $entityManager->flush();
                    $this->addFlash(
                        'success',
                        'Réservation avec Payment succé'
                    );
                    // $this->addFlash('success', 'Opération réussie');
                } catch (Stripe\Exception\CardException $th) {
                    $this->addFlash(
                        'danger',
                        'Payment refusé  <b>'. $th->getMessage().'</b>'
                    );
                }
            } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $th) {
                if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
                    $this->addFlash('danger', "Une réservation est déja faite pour avec ce compte <b>".$reservation->getEtudiant()->getPrenom().' '.$reservation->getEtudiant()->getNom()).'</b>';
                } else if($authorizationChecker->isGranted('ROLE_ETUDIANT')) {
                    $this->addFlash('danger', "Vous avez dèja effectuer une réservation avec ce compte <b>".$etudiant->getEmail()).'</b>';
                } 
            }


            return $this->redirectToRoute('app_reservation_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            'stripe_key' => $_ENV["STRIPE_KEY"]
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{id}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
}
