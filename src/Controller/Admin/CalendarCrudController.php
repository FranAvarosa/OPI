<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\EventSubscriber\EasyAdminSubscriber;
use App\Repository\CalendarRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return[
        IdField::new('id')->hideOnForm(),
        TextField::new('title', 'Sujet'),
        DateTimeField::new('start', 'Heure de début'),
        DateTimeField::new('end', 'Heure de fin'),
        TextField::new('description'),
        ChoiceField::new('category', 'Categorie')->setChoices([
            'En Attente' => 'En Attente',
            'Arrêt Maladie' => 'Arrêt Maladie',
            'Jour férié' => 'Jour férié',
            'CT' => 'CT',
            'CA' => 'CA',
            'DP' => 'DP',
            'TA COMPT' => 'TA COMPT',
            'AEP' => 'AEP',
            'Absence' => 'Absence',
            'Evaluation' => 'Evaluation',
            'Formation' => 'Formation',
            'Coordination et préparation' => 'Coordination et preparation',
            'Action Institution et partenariat' => 'Action Institution et partenariat',
            'Animation éducative et sociale' => 'Animation educative et sociale',
            'Travail de rue' => 'Travail de rue',
            'Présence sociale' => 'Presence sociale',
            'Présence sociale hors local' => 'Presence sociale hors local',
        ]),
        ChoiceField::new('background_color', 'Confirmer la categorie')->setChoices([
            'En Attente' => '#CCCCFF',
            'Arrêt Maladie' => '#FFCCCC',
            'Jour férié' => '#FFCCCC',
            'CT' => '#FFCCCC',
            'CA' => '#FFCCCC',
            'DP' => '#FF9900',
            'TA COMPT' => '#6666CC',
            'AEP' => '#9999FF',
            'Absence' => '#FFCCCC',
            'Evaluation' => '#FF6666',
            'Formation' => '#33CCFF',
            'Coordination et préparation' => '#CC9933',
            'Action Institution et partenariat' => '#FFCCCC',
            'Animation éducative et sociale' => '#ECE9D8',
            'Travail de rue' => '#00CCCC',
            'Présence sociale' => '#FFCCCC',
            'Présence sociale hors local' => '#99CFD8',
        ]),
        AssociationField::new('User', 'À qui voulez vous attribuer cette tâche ?'),
        ];
    }

}
