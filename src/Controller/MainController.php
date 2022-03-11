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
use App\Entity\User;
use PDO;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use function Symfony\Component\Mime\toString;

#[Route('/cal')]
class MainController extends AbstractController
{
    #[Route('/', name: 'main')]
    public function index(CalendarRepository $calendar, UserRepository $userRepository): Response
    {
        // check if logged in
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            $userId = $this->getUser()->getId();
            $service = $this->getUser()->getService();

            $service1 = $service[0];
            if(isset($service[1])){
                $service2 = $service[1];
            } else {
                $service2 = '';
            }

            $events = $calendar->findBy(['User' => $userId]);

            $planning = $this->getPlanningArray($events);

            return $this->render('main/index.html.twig', [
                'planning' => $planning,
                'list' => $userRepository->findAll(),
                'userService1' => $service1,
                'userService2' => $service2,
            ]);
        } else {
            return $this->render('security/restricted.html.twig');
        }
    }

    #[Route('/admin/', name: 'main_admin', methods: ['GET'])]
    public function indexAdmin(CalendarRepository $calendar, UserRepository $userRepository): Response
    {
        // check if current user is admin
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_ADMIN')){
            // get id from url and check if it exists
            $id = $_GET['id'];
            $idCheck = $userRepository->findBy(['id' => $id]);

            // if id doesn't exist, return own calendar
            if(empty($idCheck)){
                $userId = $this->getUser()->getId();
                $service = $this->getUser()->getService();
                $events = $calendar->findBy(['User' => $userId]);

                $planning = $this->getPlanningArray($events);

                return $this->render('main/index.html.twig', [
                    'planning' => $planning,
                    'list' => $userRepository->findAll(),
                    'userService' => $service,
                ]);
            } else {
                $events = $calendar->findBy(['User' => $id]);
                $planning = $this->getPlanningArray($events);

                return $this->render('main/index.html.twig', [
                    'planning' => $planning,
                    'list' => $userRepository->findAll(),
                    'calId' => $id,
                ]);
            }
        } else {
            return $this->render('security/restricted.html.twig');
        }
    }

    #[Route('/chef/', name: 'main_chef', methods: ['GET'])]
    public function indexChef(CalendarRepository $calendar, UserRepository $userRepository): Response
    {
        // check if current user is chefservice
        if($this->container->get('security.authorization_checker')->isGranted('ROLE_CHEFSERVICE')){
            $id = $_GET['id'];
            $service = $this->getUser()->getService();

            $service1 = $service[0];
            $service2 = $service[1];

            // user url id to get a matching user and find its service
            $userCheck = $userRepository->findBy(['id' => $id]);

            $userArray = [];
            foreach($userCheck as $userChecks){
                $userArray = [
                    'id' => $userChecks->getId(),
                    'service' => $userChecks->getService(),
                ];
            }

            if($service1 == implode($userArray['service']) || $service2 == implode($userArray['service']) || $service == $userArray['service']) {
                $events = $calendar->findBy(['User' => $id]);

                $planning = $this->getPlanningArray($events);

                return $this->render('main/index.html.twig', [
                    'planning' => $planning,
                    'list' => $userRepository->findAll(),
                    'calId' => $id,
                    'userService1' => $service1,
                    'userService2' => $service2,
                ]);
            } else {
                return $this->render('security/restricted.html.twig');
            }
        } else {
            return $this->render('security/restricted.html.twig');
        }
    }

    function getPlanningArray($events){
        $planning = [];
        foreach($events as $event){
            $planning[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'category' => $event->getCategory(),
            ];
        }

        return $planning;
    }
}
