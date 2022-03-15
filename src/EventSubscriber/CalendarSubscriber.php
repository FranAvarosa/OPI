<?php

namespace App\EventSubscriber;

use App\Controller\Service\CalendarColorService;
use App\Entity\Calendar;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $calendarColorService;

    public function __construct(CalendarColorService $calendarColorService)
    {
        $this->calendarColorService = $calendarColorService;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['addColorCalendar'],
            BeforeEntityUpdatedEvent::class => ['updatedColorCalendar'],
        ];
    }

    public function addColorCalendar(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (($entity instanceof Calendar)) {
            $entity->setBackgroundColor($this->calendarColorService->color($entity->getCategory()));
        }
    }

    public function updatedColorCalendar(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if (($entity instanceof Calendar)) {
            $entity->setBackgroundColor($this->calendarColorService->color($entity->getCategory()));
        }
    }
}