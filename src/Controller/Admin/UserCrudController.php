<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    private $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            EmailField::new('email'),
            TextEditorField::new('nom'),
            TextEditorField::new('prenom'),
            TextField::new('plainPassword', 'password')
                ->setFormType(PasswordType::class)
                ->setRequired($pageName === Crud::PAGE_NEW)
                ->onlyOnForms(),
            ChoiceField::new('service', 'Service')->allowMultipleChoices()->setChoices([
                'Quartier de Neuhof' => 'Quartier de Neuhof',
                'Quartier Cité de l\'Ill - Guirbarden' => 'Quartier Cité de l\'Ill - Guirbarden',
                'Quartier de Koenigshoffen' => 'Quartier de Koenigshoffen',
                'Quartier Montagne Verte' => 'Quartier Montagne Verte',
                'Quartier Ampère - Port du Rhin' => 'Quartier Ampère - Port du Rhin',
                'Quartier de l\'Elsau' => 'Quartier De l\'Elsau',
                'Equipe Focale' => 'Equipe Focale',
            ]),
        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function encodePassword(User $user)
    {
        if ($user->getPlainPassword() !== null) {
            $user->setSalt(base_convert(bin2hex(random_bytes(20)), 16, 36));
            // This is where you use UserPasswordEncoderInterface
            $user->setPassword($this->passwordEncoder->hashPassword($user, $user->getPlainPassword()));
        }
    }
}
