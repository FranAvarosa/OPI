<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use ContainerE4xO03e\getMaker_PhpCompatUtilService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/cal', name: 'main')]
    public function index(CalendarRepository $calendar, UserRepository $user): Response
    {
        $userId = $this->getUser()->getId();
        $events = $calendar->findBy(['User' => $userId]);
        $planning = [];
        foreach($events as $event){
            $planning[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }

        return $this->render('main/index.html.twig', [
            'planning' => $planning
        ]);
    }
}
