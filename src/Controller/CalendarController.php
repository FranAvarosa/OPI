<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Controller\Service\CalendarColorService;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/calendar')]
class CalendarController extends AbstractController
{
    private $calendarColorService;

    public function __construct(CalendarColorService $calendarColorService)
    {
        $this->calendarColorService = $calendarColorService;
    }

    #[Route('/new', name: 'calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $calendar = new Calendar();
            $form = $this->createForm(CalendarType::class, $calendar);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // add start's day to end's time
                $date1 = $calendar->getStart();
                $date1Ymd = $calendar->getStart()->format('Y-m-d');
                $date1His = $calendar->getStart()->format('H:i:s');
                $date2His = $calendar->getEnd()->format('H:i:s');
                $dateString = $date1Ymd . ' ' . $date2His;
                $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

                // check if end is after start
                if ($dateEnd > $date1) {
                    $calendar->setBackgroundColor($this->calendarColorService->color($calendar->getCategory()));

                    // get current user events
                    $id = $this->getUser()->getId();
                    $allEvents = $calendarRepository->findBy(['User' => $id]);

                    // show message if more than 12h for the day
                    $this->isMoreThan12Hours($allEvents, $date1Ymd, $date2His, $date1His);
                    // check if event happens during another event
                    $this->isDuringAnotherEvent($allEvents, $date1Ymd, $date1His, $date2His);
                    //check for pause
                    $postedEventDuration = (strtotime($date2His) - strtotime($date1His)) / 3600;
                    if($postedEventDuration >= 6 and $postedEventDuration < 7) {
                        $this->addFlash('warning', 'Pensez à prendre une pause de 20 minutes après cet événement.');
                    } elseif ($postedEventDuration >= 7) {
                        $this->addFlash('warning', 'Pensez à prendre une pause de 30 minutes après cet événement.');
                    }

                    $calendar->setUser($this->getUser());
                    $calendar->setEnd($dateEnd);
                    $entityManager->persist($calendar);
                    $entityManager->flush();
                } else {
                    $this->addFlash('warning', 'L\'heure de fin ne peut pas avoir lieu avant l\'heure de début !');
                    return $this->redirectToRoute('calendar_new', [], Response::HTTP_SEE_OTHER);
                }

                $this->addFlash('success', 'Evénement créé avec succès.');
                return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('calendar/new.html.twig', [
                'calendar' => $calendar,
                'form' => $form,
            ]);
        } else {
            return $this->render('security/restricted.html.twig');
        }
    }

    #[Route('/{id}/edit', name: 'calendar_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Calendar $calendar, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository): Response
    {
        // check if logged in
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            // get current user infos
            $currentUserId = $this->getUser()->getId();
            $currentUserService = $this->getUser()->getService();
            $service1 = $currentUserService[0];
            if(isset($currentUserService[1])){
                $service2 = $currentUserService[1];
            } else {
                $service2 = '';
            }
            if(isset($currentUserService[2])){
                $service3 = $currentUserService[2];
            } else {
                $service3 = '';
            }

            // get event infos
            $attachedUserId = $calendar->getUser()->getId();
            $attachedUserService = implode($calendar->getUser()->getService());

            // check current user role
            $roleCheckerChef = $this->container->get('security.authorization_checker')->isGranted('ROLE_CHEFSERVICE');
            $roleCheckerAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

            // check if current user is allowed to edit
            if ($roleCheckerAdmin or
                $currentUserId == $attachedUserId or
                $service1 == $attachedUserService and $roleCheckerChef or
                $service2 == $attachedUserService and $roleCheckerChef or
                $service3 == $attachedUserService and $roleCheckerChef or
                $currentUserService == $attachedUserService and $roleCheckerChef) {
                $form = $this->createForm(CalendarType::class, $calendar);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {
                    // add start's day to end's time
                    $date1 = $calendar->getStart();
                    $date1Ymd = $calendar->getStart()->format('Y-m-d');
                    $date1His = $calendar->getStart()->format('H:i:s');
                    $date2His = $calendar->getEnd()->format('H:i:s');
                    $dateString = $date1Ymd . ' ' . $date2His;
                    $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

                    // check if end is after start
                    if ($dateEnd > $date1) {
                        // get attached user events
                        $allEvents = $calendarRepository->findBy(['User' => $attachedUserId]);

                        // send edit
                        $calendar->setEnd($dateEnd);
                        $calendar->setBackgroundColor($this->calendarColorService->color($calendar->getCategory()));
                        $entityManager->persist($calendar);
                        $entityManager->flush();

                        // check if event total is more than 12h
                        $this->isMoreThan12Hours($allEvents, $date1Ymd, $date2His, $date1His);
                        // check if event happens during another event
                        $this->isDuringAnotherEvent($allEvents, $date1Ymd, $date1His, $date2His);
                    } else {
                        $this->addFlash('warning', 'L\'heure de fin ne peut pas avoir lieu avant l\'heure de début !');
                        return $this->redirectToRoute('calendar_edit', ['id' => $calendar->getId()], Response::HTTP_SEE_OTHER);
                    }

                    $this->addFlash('success', 'Evénement édité avec succès.');
                    return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
                }
            } else {
                return $this->render('security/restricted.html.twig');
            }

            return $this->renderForm('calendar/edit.html.twig', [
                'calendar' => $calendar,
                'form' => $form,
            ]);
        } else {
            return $this->render('security/restricted.html.twig');
        }
    }

    #[Route('/{id}', name: 'calendar_delete', methods: ['POST'])]
    public function delete(Request $request, Calendar $calendar, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->request->get('_token'))) {
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Evénement supprimé avec succès.');
        return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
    }

    function isMoreThan12Hours($allEvents, $date1Ymd, $date2His, $date1His) {
        // organize array values
        $eventArray = [];
        foreach($allEvents as $events) {
            $eventArray[] = [
                'start' => $events->getStart()->format('Y-m-d'),
                'startHour' => $events->getStart()->format('H:i:s'),
                'end' => $events->getEnd()->format('Y-m-d'),
                'endHour' => $events->getEnd()->format('H:i:s'),
            ];
        }

        // sums all events hour durations for the day the event is created
        $sum = 0;
        for($i = 0; $i < count($eventArray); $i++) {
            if($eventArray[$i]['start'] == $date1Ymd) {
                $sum += (strtotime($eventArray[$i]['endHour']) - strtotime($eventArray[$i]['startHour'])) / 3600;
            }
        }
        // adds new event hour duration
        $sum += (strtotime($date2His) - strtotime($date1His)) / 3600;
        // show message if more than 12h for the day
        if($sum > 12) {
            $this->addFlash('warning', 'Attention, vous cumulez plus de 12 heures de travail ce jour-ci !');
        }

        return;
    }

    function isDuringAnotherEvent($allEvents, $date1Ymd, $date1His, $date2His) {
        // organize array values
        $simulEventArray = [];
        foreach($allEvents as $events) {
            $simulEventArray[] = [
                'start' => $events->getStart()->format('Y-m-d'),
                'startHour' => $events->getStart()->format('H:i:s'),
                'end' => $events->getEnd()->format('Y-m-d'),
                'endHour' => $events->getEnd()->format('H:i:s'),
            ];
        }

        for($j = 0; $j < count($simulEventArray); $j++) {
            if($simulEventArray[$j]['start'] == $date1Ymd) {
                if($simulEventArray[$j]['startHour'] > $date1His && $simulEventArray[$j]['startHour'] < $date2His) {
                    $this->addFlash('danger', 'Attention, un autre événement se tient durant ce créneau horaire.');
                    break;
                } else if($simulEventArray[$j]['endHour'] > $date1His && $simulEventArray[$j]['endHour'] < $date2His) {
                    $this->addFlash('danger', 'Attention, un autre événement se tient durant ce créneau horaire.');
                    break;
                } else if($simulEventArray[$j]['startHour'] < $date1His && $simulEventArray[$j]['endHour'] > $date2His) {
                    $this->addFlash('danger', 'Attention, un autre événement se tient durant ce créneau horaire.');
                    break;
                }
            }
        }

        return;
    }
}
