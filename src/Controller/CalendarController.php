<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\User;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\Mime\Header\getId;
use function Symfony\Component\String\toString;

#[Route('/calendar')]
class CalendarController extends AbstractController
{
    #[Route('/new', name: 'calendar_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendarRepository, UserRepository $user): Response
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

                // check if event total is more than 12h
                    // get current user events
                $id = $this->getUser()->getId();
                $allEvents = $calendarRepository->findBy(['User' => $id]);

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
                    $this->addFlash('warning', 'Attention, vous cumulez plus de 12 heures de travail aujourd\'hui !');
                }

                // check if end is not before start
                if ($dateEnd > $date1) {
                    $category = $calendar->getCategory();
                    $this->setBackgroundColors($category, $calendar);

                    $calendar->setUser($this->getUser());
                    $calendar->setEnd($dateEnd);
                    $entityManager->persist($calendar);
                    $entityManager->flush();
                } else {
                    return $this->redirectToRoute('calendar_edit', [], Response::HTTP_SEE_OTHER);
                }

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

            // get event infos
            $attachedUserId = $calendar->getUser()->getId();
            $attachedUserService = implode($calendar->getUser()->getService());

            // check current user role
            $roleCheckerChef = $this->container->get('security.authorization_checker')->isGranted('ROLE_CHEFSERVICE');
            $roleCheckerAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

            // check if current user is allowed to edit
            if ($roleCheckerAdmin or $currentUserId == $attachedUserId or $service1 == $attachedUserService and $roleCheckerChef or $service2 == $attachedUserService and $roleCheckerChef or $currentUserService == $attachedUserService and $roleCheckerChef) {
                $form = $this->createForm(CalendarType::class, $calendar);
                $form->handleRequest($request);

                $oldEventStart = $calendar->getStart()->format('H:i:s');
                $oldEventEnd = $calendar->getEnd()->format('H:i:s');

                if ($form->isSubmitted() && $form->isValid()) {
                    // add start's day to end's time
                    $date1 = $calendar->getStart();
                    $date1Ymd = $calendar->getStart()->format('Y-m-d');
                    $date1His = $calendar->getStart()->format('H:i:s');
                    $date2His = $calendar->getEnd()->format('H:i:s');
                    $dateString = $date1Ymd . ' ' . $date2His;
                    $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

                    // check if event total is more than 12h
                    // get attached user events
                    $allEvents = $calendarRepository->findBy(['User' => $attachedUserId]);

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

                    // substracts edited event hour duration
                    $sum -= (strtotime($oldEventEnd) - strtotime($oldEventStart)) / 3600;

                    // show message if more than 12h for the day
                    if($sum > 12) {
                        $this->addFlash('warning', 'Attention, vous cumulez plus de 12 heures de travail aujourd\'hui !');
                    }

                    if ($dateEnd > $date1) {
                        $category = $calendar->getCategory();
                        $this->setBackgroundColors($category, $calendar);

                        $calendar->setEnd($dateEnd);
                        $entityManager->persist($calendar);
                        $entityManager->flush();
                    } else {
                        return $this->redirectToRoute('calendar_edit', [], Response::HTTP_SEE_OTHER);
                    }

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

        return $this->redirectToRoute('main', [], Response::HTTP_SEE_OTHER);
    }

    function setBackgroundColors($category, $calendar){
        switch ($category) {
            case "En attente":
                $calendar->setBackgroundColor('#CCCCFF');
                break;
            case "Arrêt Maladie":
            case "Jour férié":
            case "CT":
            case "CA":
            case "Absence":
            case "Action Institution et partenariat":
            case "Présence sociale":
                $calendar->setBackgroundColor('#FFCCCC');
                break;
            case "DP":
                $calendar->setBackgroundColor('#FF9900');
                break;
            case "TA COMPT":
                $calendar->setBackgroundColor('#6666CC');
                break;
            case "AEP":
                $calendar->setBackgroundColor('#9999FF');
                break;
            case "Evaluation":
                $calendar->setBackgroundColor('#FF6666');
                break;
            case "Formation":
                $calendar->setBackgroundColor('#33CCFF');
                break;
            case "Coordination et préparation":
                $calendar->setBackgroundColor('#CC9933');
                break;
            case "Animation éducative et sociale":
                $calendar->setBackgroundColor('#ECE9D8');
                break;
            case "Travail de rue":
                $calendar->setBackgroundColor('#00CCCC');
                break;
            case "Présence sociale hors local":
                $calendar->setBackgroundColor('#99CFD8');
                break;
            default:
                $calendar->setBackgroundColor("#CCCCFF");
        }
    }
}
