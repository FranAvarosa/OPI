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
use function Symfony\Component\Mime\Header\getId;
use function Symfony\Component\String\toString;

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
        $currentUserId = $this->getUser()->getId();
        $currentUserService = $this->getUser()->getService();
        $attachedUserId = $calendar->getUser()->getId();
        $attachedUserService = $calendar->getUser()->getService();
        $roleCheckerChef = $this->container->get('security.authorization_checker')->isGranted('ROLE_CHEFSERVICE');
        $roleCheckerAdmin = $this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN');

        if($roleCheckerAdmin or $currentUserId == $attachedUserId or $currentUserService == $attachedUserService and $roleCheckerChef) {
            $form = $this->createForm(CalendarType::class, $calendar);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $date1 = $calendar->getStart();

                $date1Ymd = $calendar->getStart()->format('Y-m-d');
                $date2His = $calendar->getEnd()->format('H:i:s');
                $dateString = $date1Ymd . ' ' . $date2His;
                $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

                if ($dateEnd > $date1) {
                    $category = $calendar->getCategory();
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
