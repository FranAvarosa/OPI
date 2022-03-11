<?php

namespace App\Controller\Admin;

use App\Entity\Calendar;
use App\Repository\CalendarRepository;
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

    public function createCalendar(Request $request, EntityManagerInterface $entityManager, CalendarRepository $calendar): Response
    {

            $calendar = new Calendar();
            $form = $this->createForm(CalendarType::class, $calendar);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // add start's day to end's time
                $date1 = $calendar->getStart();
                $date1Ymd = $calendar->getStart()->format('Y-m-d');
                $date2His = $calendar->getEnd()->format('H:i:s');
                $dateString = $date1Ymd . ' ' . $date2His;
                $dateEnd = date_create_from_format('Y-m-d H:i:s', $dateString);

                // check if end is not before start
                if ($dateEnd > $date1) {
                    $category = $calendar->getCategory();
                    $this->setBackgroundColors($category, $calendar);

                    $calendar->setUser($this->getUser());
                    $calendar->setEnd($dateEnd);
                    $entityManager->persist($calendar);
                    $entityManager->flush();
                }
            }
            $user = AssociationField::new('User', 'À qui voulez vous attribuer cette tâche ?');
            return $this->renderForm('calendar/new.html.twig', [
                'calendar' => $calendar,
                'form' => $form,
            ]);
    }

    function setBackgroundColors($category, $calendar){
        switch ($category) {
            case "En attente":
                $calendar->setBackgroundColor('#CCCCFF');
                break;
            case "Arrêt Maladie":
            case "Jour férié":
            case "CT":
            case "CA":
            case "Absence":
            case "Action Institution et partenariat":
            case "Présence sociale":
                $calendar->setBackgroundColor('#FFCCCC');
                break;
            case "DP":
                $calendar->setBackgroundColor('#FF9900');
                break;
            case "TA COMPT":
                $calendar->setBackgroundColor('#6666CC');
                break;
            case "AEP":
                $calendar->setBackgroundColor('#9999FF');
                break;
            case "Evaluation":
                $calendar->setBackgroundColor('#FF6666');
                break;
            case "Formation":
                $calendar->setBackgroundColor('#33CCFF');
                break;
            case "Coordination et préparation":
                $calendar->setBackgroundColor('#CC9933');
                break;
            case "Animation éducative et sociale":
                $calendar->setBackgroundColor('#ECE9D8');
                break;
            case "Travail de rue":
                $calendar->setBackgroundColor('#00CCCC');
                break;
            case "Présence sociale hors local":
                $calendar->setBackgroundColor('#99CFD8');
                break;
            default:
                $calendar->setBackgroundColor("#CCCCFF");
        }
    }

    public function configureFields(string $pageName): iterable
    {

        $id = IdField::new('id')->hideOnForm();
        $sujet = TextField::new('title', 'Sujet');
        $start = DateTimeField::new('start', 'Heure de début');
        $end = TimeField::new('end', 'Heure de fin');
        $description = TextField::new('description');
        $category = ChoiceField::new('category', 'Categorie')->setChoices([
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
        ]);
        $user = AssociationField::new('User', 'À qui voulez vous attribuer cette tâche ?');

        return [ $id, $sujet, $start, $end, $description, $category, $user];
    }

}
