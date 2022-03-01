<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Repository\UserRepository;
use ContainerE4xO03e\getMaker_PhpCompatUtilService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cal')]
class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CalendarRepository $calendar, UserRepository $userRepository): Response
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
            'planning' => $planning,
            'list' => $userRepository->findAll(),
        ]);
    }

    #[Route('/admin/', name: 'main_admin', methods: ['GET'])]
    public function indexAdmin(CalendarRepository $calendar, UserRepository $userRepository): Response
    {
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            $id = $_GET['id'];
            $events = $calendar->findBy(['User' => $id]);
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
                'planning' => $planning,
                'list' => $userRepository->findAll(),
            ]);
        } else {
            return $this->render('security/login.html.twig');
        }
    }
}
