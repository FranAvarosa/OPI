<?php

namespace App\Form;

use App\Entity\Calendar;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CalendarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, array('label' => 'Titre'))
            ->add('start', DateTimeType::class, [
                'date_widget' => 'single_text',
                'input' => 'datetime'
            ])
            ->add('end', TimeType::class, [
                'input' => 'datetime'
            ])
            ->add('description')
            ->add('category', ChoiceType::class, [
                'choices'  => [
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
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Calendar::class,
        ]);
    }
}
