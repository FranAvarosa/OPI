<?php

namespace App\EventSubscriber;

use App\Entity\Calendar;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;

class CalendarSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['ajoutcolor'],
            BeforeEntityDeletedEvent::class => ['beforeEntityDeletedEvent'],
            BeforeEntityUpdatedEvent::class => ['beforeEntityUpdatedEvent'],
        ];
    }

    public function ajoutcolor(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $category = $entity->getCategory();

        if (($entity instanceof Calendar)){
            switch ($category) {
                case "En attente":
                    $entity->setBackgroundColor('#CCCCFF');
                    break;
                case "Arrêt Maladie":
                case "Jour férié":
                case "CT":
                case "CA":
                case "Absence":
                case "Action Institution et partenariat":
                case "Présence sociale":
                    $entity->setBackgroundColor('#FFCCCC');
                    break;
                case "DP":
                    $entity->setBackgroundColor('#FF9900');
                    break;
                case "TA COMPT":
                    $entity->setBackgroundColor('#6666CC');
                    break;
                case "AEP":
                    $entity->setBackgroundColor('#9999FF');
                    break;
                case "Evaluation":
                    $entity->setBackgroundColor('#FF6666');
                    break;
                case "Formation":
                    $entity->setBackgroundColor('#33CCFF');
                    break;
                case "Coordination et préparation":
                    $entity->setBackgroundColor('#CC9933');
                    break;
                case "Animation éducative et sociale":
                    $entity->setBackgroundColor('#ECE9D8');
                    break;
                case "Travail de rue":
                    $entity->setBackgroundColor('#00CCCC');
                    break;
                case "Présence sociale hors local":
                    $entity->setBackgroundColor('#99CFD8');
                    break;
                default:
                    $entity->setBackgroundColor("#CCCCFF");
            }
        }
    }

    public function beforeEntityDeletedEvent(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance(); 
        if ($entity instanceof Calendar){
            
        }
    }

    public function beforeEntityUpdatedEvent(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $category = $entity->getCategory();

        if (($entity instanceof Calendar)){
            switch ($category) {
                case "En attente":
                    $entity->setBackgroundColor('#CCCCFF');
                    break;
                case "Arrêt Maladie":
                case "Jour férié":
                case "CT":
                case "CA":
                case "Absence":
                case "Action Institution et partenariat":
                case "Présence sociale":
                    $entity->setBackgroundColor('#FFCCCC');
                    break;
                case "DP":
                    $entity->setBackgroundColor('#FF9900');
                    break;
                case "TA COMPT":
                    $entity->setBackgroundColor('#6666CC');
                    break;
                case "AEP":
                    $entity->setBackgroundColor('#9999FF');
                    break;
                case "Evaluation":
                    $entity->setBackgroundColor('#FF6666');
                    break;
                case "Formation":
                    $entity->setBackgroundColor('#33CCFF');
                    break;
                case "Coordination et préparation":
                    $entity->setBackgroundColor('#CC9933');
                    break;
                case "Animation éducative et sociale":
                    $entity->setBackgroundColor('#ECE9D8');
                    break;
                case "Travail de rue":
                    $entity->setBackgroundColor('#00CCCC');
                    break;
                case "Présence sociale hors local":
                    $entity->setBackgroundColor('#99CFD8');
                    break;
                default:
                    $entity->setBackgroundColor("#CCCCFF");
            }
        }
    }
}