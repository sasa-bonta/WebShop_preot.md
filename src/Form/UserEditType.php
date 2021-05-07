<?php


namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\Length;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => new Length([
                    'min' => 1,
                    'max' => 25
                ])
            ])
            ->add('email', EmailType::class)
//            ->add('plainPassword', RepeatedType::class, [
//                'type' => PasswordType::class,
//                'first_options' => array('label' => 'Password'),
//                'second_options' => array('label' => 'Repeat Password'),
//                'required' => false,
//                'constraints' =>[
//                    new Blank(),
//                    new Length([
//                        'min' => 8,
//                        'max' => 255
//                    ])
//                ]
//            ])
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => "ROLE_USER",
                    'Admin' => "ROLE_ADMIN"
                ],
                'label' => 'Role'
            ]);

        //roles field data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) ? $rolesArray[0] : null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}