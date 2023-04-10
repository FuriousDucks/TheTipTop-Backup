<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom',
                    'class' => 'form-control',
                ],
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                    'class' => 'form-control',
                ],
            ])
            ->add('dateOfBirth', TextType::class, [
                'label' => 'Date de naissance',
                'attr' => [
                    'placeholder' => 'Date de naissance',
                    'class' => 'form-control datepicker',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre date de naissance',
                    ]),
                    new Regex([
                        'pattern' => '/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[012])\/(19|20)\d\d$/',
                        'message' => 'Votre date de naissance doit être au format JJ/MM/AAAA',
                    ]),
                ],
            ])
            ->add('phone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Téléphone',
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez renseigner votre numéro de téléphone',
                    ]),
                    new Length([
                        'min' => 10,
                        'minMessage' => 'Votre numéro de téléphone doit faire au moins {{ limit }} caractères',
                    ]),
                    new Regex([
                        'pattern' => '/^0[1-9]([-. ]?[0-9]{2}){4}$/',
                        'message' => 'Votre numéro de téléphone doit être au format 0600000000',
                    ]),
                ],
            ])
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email',
                    'attr' => [
                        'placeholder' => 'Email',
                        'class' => 'form-control',
                    ],
                    'required' => false,
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez renseigner votre email',
                        ]),
                        new Email([
                            'message' => 'Veuillez renseigner un email valide',
                        ]),
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
        ]);
    }
}
