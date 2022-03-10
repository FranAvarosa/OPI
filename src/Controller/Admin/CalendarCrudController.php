<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ColorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class CalendarCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Calendar::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('title', 'Sujet'),
            DateTimeField::new('start', 'Heure de début'),
            TimeField::new('end', 'Heure de fin'),
            TextEditorField::new('description'),
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
            AssociationField::new('User', 'À qui voulez vous attribuer cette tâche ?'),
        ];
    }
}
