<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use App\Repository\DefaultRepository;
use App\Repository\UserRepository;
use ContainerE4xO03e\getMaker_PhpCompatUtilService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\ValidatorService;
use PDO;

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
                'date_diff' => $event->getDateDiff(),
            ];
        }

//        $date = date("m Y");
//
//        $currentMonth = date_parse_from_format("m Y", $date);
//        $monthHours = $calendar->findBy(['start' => $currentMonth]);
//        $monthArray = [];
//        foreach ($monthHours as $monthHour){
//            $monthArray[] = [
//                'id' => $monthHour->getId(),
//                'start' => $monthHour->getStart()->format('m Y'),
//                'end' => $monthHour->getEnd()->format('Y-m-d H:i:s'),
////                'title' => $monthHour->getTitle(),
////                'description' => $monthHour->getDescription(),
////                'backgroundColor' => $monthHour->getBackgroundColor(),
//                'date_diff' => $monthHour->getDateDiff(),
//            ];
//        }

        return $this->render('main/index.html.twig', [
            'planning' => $planning,
            'list' => $userRepository->findAll()
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
                    'date_diff' => $event->getDateDiff(),
                ];
            }

            return $this->render('main/index.html.twig', [
                'planning' => $planning,
                'list' => $userRepository->findAll(),
                'calId' => $id,
            ]);
        } else {
            return $this->render('security/login.html.twig');
        }
    }
}
