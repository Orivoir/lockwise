<?php

namespace App\Form;

use App\Entity\Account;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plateform' , TextType::class , [
                'required' => false ,
                'attr' => [
                    'autoFocus' => true ,
                    'autocomplete' => 'off' 
                ]
            ] )
            ->add('password' , PasswordType::class , [
                'required' => false ,
                'attr' => [
                    'autoFocus' => true ,
                    'autocomplete' => 'off' 
                ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Account::class,
        ]);
    }
}
