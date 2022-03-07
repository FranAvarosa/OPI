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
                    'En attente' => 'En attente',
                    'Travail de rue' => 'Travail de rue',
                    'Travail de nuit' => 'Travail de nuit'
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
