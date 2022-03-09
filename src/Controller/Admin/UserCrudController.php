<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextEditorField::new('nom'),
            TextEditorField::new('prenom'),
            ChoiceField::new('service1', 'Service')->setChoices([
                'Quartier De Neuhof' => 'Quartier De Neuhof',
                'Quartier Cité de l\'Ill' => 'Quartier Cité de l\'Ill',
                'Quartier De Koenigshoffen' => 'Quartier De Koenigshoffen',
                'Quartier Montagne Verte' => 'Quartier Montagne Verte',
                'Quartier Ampère - Port du Rhin' => 'Quartier Ampère - Port du Rhin',
                'Quartier De l\'Elsau' => 'Quartier De l\'Elsau',
                'Equipe Focale' => 'Equipe Focale',
            ]),
            ChoiceField::new('service2', 'Service')->setChoices([
                'Quartier De Neuhof' => 'Quartier De Neuhof',
                'Quartier Cité de l\'Ill' => 'Quartier Cité de l\'Ill',
                'Quartier De Koenigshoffen' => 'Quartier De Koenigshoffen',
                'Quartier Montagne Verte' => 'Quartier Montagne Verte',
                'Quartier Ampère - Port du Rhin' => 'Quartier Ampère - Port du Rhin',
                'Quartier De l\'Elsau' => 'Quartier De l\'Elsau',
                'Equipe Focale' => 'Equipe Focale',
            ]),
        ];
    }
    
}
