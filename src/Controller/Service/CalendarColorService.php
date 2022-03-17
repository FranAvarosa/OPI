<?php

namespace App\Controller\Service;

class CalendarColorService
{
    public function color(string $category)
    {
        switch ($category) {
            case 'En attente':
                $color = '#666666';
                break;
            case 'Arrêt Maladie':
            case 'Jour férié':
            case 'CT':
            case 'CA':
            case 'Absence':
            case 'Action Institution et partenariat':
            case 'Présence sociale':
                $color = '#ff6666';
                break;
            case 'DP':
                $color = '#b36b00';
                break;
            case 'TA COMPT':
                $color = '#4040bf';
                break;
            case 'AEP':
                $color = '#8080ff';
                break;
            case 'Evaluation':
                $color = '#ff3333';
                break;
            case 'Formation':
                $color = '#0099cc';
                break;
            case 'Coordination et préparation':
                $color = '#b88a2e';
                break;
            case 'Animation éducative et sociale':
                $color = '#ab9f54';
                break;
            case 'Travail de rue':
                $color = '#008080';
                break;
            case 'Présence sociale hors local':
                $color = '#46a8b9';
                break;
            default:
                $color = '#666666';
        }

        return $color;
    }
}