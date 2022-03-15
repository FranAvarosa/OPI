<?php

namespace App\Controller\Service;

class CalendarColorService
{
    public function color(string $category)
    {
        switch ($category) {
            case 'En attente':
                $color = '#CCCCFF';
                break;
            case 'Arrêt Maladie':
            case 'Jour férié':
            case 'CT':
            case 'CA':
            case 'Absence':
            case 'Action Institution et partenariat':
            case 'Présence sociale':
                $color = '#FFCCCC';
                break;
            case 'DP':
                $color = '#FF9900';
                break;
            case 'TA COMPT':
                $color = '#6666CC';
                break;
            case 'AEP':
                $color = '#9999FF';
                break;
            case 'Evaluation':
                $color = '#FF6666';
                break;
            case 'Formation':
                $color = '#33CCFF';
                break;
            case 'Coordination et préparation':
                $color = '#CC9933';
                break;
            case 'Animation éducative et sociale':
                $color = '#ECE9D8';
                break;
            case 'Travail de rue':
                $color = '#00CCCC';
                break;
            case 'Présence sociale hors local':
                $color = '#99CFD8';
                break;
            default:
                $color = '#CCCCFF';
        }

        return $color;
    }
}