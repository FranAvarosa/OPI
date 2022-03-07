<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/new', name: 'calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date1 = $calendar->getStart();

            $date1Ymd = $calendar->getStart()->format('Y-m-d');
            $date2His = $calendar->getEnd()->format('H:i:s');
            $dateString = $date1Ymd . ' ' . $date2His;
            $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

            if($dateEnd > $date1){
                $category = $calendar->getCategory();
                switch($category) {
                    case "En attente";
                        $calendar->setBackgroundColor('#b7b7b7');
                        break;
                    case "Travail de rue";
                        $calendar->setBackgroundColor('#eac159');
                        break;
                    case "Travail de nuit";
                        $calendar->setBackgroundColor('#bf82dd');
                        break;
                    default:
                        $calendar->setBackgroundColor("#b7b7b7");
                }

                $calendar->setUser($this->getUser());
                $calendar->setEnd($dateEnd);
                $entityManager->persist($calendar);
                $entityManager->flush();
            } else {
                return $this->redirectToRoute('404', [], Response::HTTP_SEE_OTHER);
            }



            return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'calendar_show', methods: ['GET'])]
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    #[Route('/{id}/edit', name: 'calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date1 = $calendar->getStart();

            $date1Ymd = $calendar->getStart()->format('Y-m-d');
            $date2His = $calendar->getEnd()->format('H:i:s');
            $dateString = $date1Ymd . ' ' . $date2His;
            $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

            if($dateEnd > $date1){
                $category = $calendar->getCategory();
                switch($category) {
                    case "En attente";
                        $calendar->setBackgroundColor('#b7b7b7');
                        break;
                    case "Travail de rue";
                        $calendar->setBackgroundColor('#eac159');
                        break;
                    case "Travail de nuit";
                        $calendar->setBackgroundColor('#bf82dd');
                        break;
                    default:
                        $calendar->setBackgroundColor("#b7b7b7");
                }

                $calendar->setUser($this->getUser());
                $calendar->setEnd($dateEnd);
                $entityManager->persist($calendar);
                $entityManager->flush();
            } else {
                return $this->redirectToRoute('404', [], Response::HTTP_SEE_OTHER);
            }

            return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
    }
}
