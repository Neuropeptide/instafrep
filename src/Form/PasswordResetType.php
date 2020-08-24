<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PasswordResetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('current_password',PasswordType::class, [
                'mapped' => false
            ])
            ->add('password',RepeatedType::class, [
                'first_name'=> 'new_password',
                'first_options'=>['label'=>'Password'],
                'second_name'=> 'confirm_password',
                'second_options'=>['label'=>'Repeat Password'],
                'type' => PasswordType::class,
                'mapped'=>false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                        ]),
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                        'maxMessage' => 'Your password should be at most {{ limit }} characters',
                    ])
                ]
            ])
            ->add("submit",SubmitType::class)
        ;
    }
}
