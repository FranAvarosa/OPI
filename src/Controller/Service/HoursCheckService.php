<?php

namespace App\Controller\Service;

class HoursCheckService
{
    public function check($allEvents, $date1Ymd, $date1His, $date2His)
    {
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

        return $this;
    }
}
