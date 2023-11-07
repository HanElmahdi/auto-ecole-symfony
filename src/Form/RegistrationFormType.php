<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entez votre <b>email</b>',
                    ]),
                    new  Email([
                        'message' => 'Veuillez entrez un <b>Email</b> valide'
                    ])
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => "Accepter les terms",
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => "Nom",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entez votre <b>Nom</b>',
                    ]),
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => "Prénom",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entez votre <b>Prénom</b>',
                    ]),
                ]
            ])
            ->add('phone', TelType::class, [
                'label' => "Téléphone",
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrez votre <b>Téléphone</b>',
                    ]),
                    new Length([
                        'minMessage' => 'Veuillez entrez votre <b>Téléphone</b> valide',
                        'maxMessage' => 'Veuillez entrez votre <b>Téléphone</b> valide',
                        'min' => 10,
                        'max' => 15,
                    ]),
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => "Mot de passe",
                'required' => false,
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
