<?php

namespace App\Form;

use App\Entity\Etudiant;
use App\Entity\Instructeur;
use App\Entity\Reservation;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\NotBlank;

class ReservationType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Check the user's role
        $user = $this->security->getUser();
        $isAdmin = $this->security->isGranted('ROLE_ADMIN');

        $builder
            ->add('date_exam', DateType::class, [
                'required' => true,
                'constraints' => [
                    new  NotBlank([
                        'message' => 'Veuillez choisir la Date d\'examen'
                    ])
                ],
                'label'    => 'Date d\'examen',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => true
            ])
            ->add('hours', IntegerType::class, [
                'label'    => 'Heure de l\'examen',
                'required' => true,
                'attr'     => array(
                    'min'  => 1,
                    'max'  => 23,
                ),
                'constraints' => [
                    new  NotBlank([
                        'message' => 'Veuillez Inserer Heure de l\'examen'
                    ])
                ]
            ])
            ->add('type_permis', ChoiceType::class, [
                'label'    => 'Type de permis',
                'required' => true,
                'multiple' => false,
                'expanded' => true,
                'choices'  => [
                    'Formule (A) : 3300 MAD' => 'Formule A',
                    'Formule (B) : 3550 MAD' => 'Formule B',
                    'Formule (C) : 3800 MAD' => 'Formule C',
                    'Formule (D) : 4050 MAD' => 'Formule D'
                ],
                'constraints' => [
                    new  NotBlank([
                        'message' => 'Veuillez choisir au moins un permis'
                    ])
                ]
            ])

            ->add('instructeurs', EntityType::class, [
                'label'    => 'Instructeur',
                'required' => true,
                // looks for choices from this entity
                'class' => Instructeur::class,
                'placeholder' => 'Choisissez un Instructeur',
                // uses the User.username property as the visible option string
                'choice_label' => 'nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir une <b>Instructeur</b>',
                    ])
                ]
            ])
        ;
        if ($isAdmin) {
            // Only add this field if the user is an admin
            $builder
                ->add('etudiant', EntityType::class, [
                    'label'    => 'Etudiant',
                    'required' => true,
                    // looks for choices from this entity
                    'class' => Etudiant::class,
                    'placeholder' => 'Choisissez un Etudiant',
                    // uses the User.username property as the visible option string
                    'choice_label' => 'nom',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez choisir une <b>Etudiant</b>',
                        ])
                    ]
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
