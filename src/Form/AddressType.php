<?php


namespace App\Form;


use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', CountryType::class)
            ->add('state', TextType::class, [
                'attr' => ['pattern' => '[a-zA-Z" "]*',
                    'oninvalid' => "setCustomValidity('This field must contain only letters and space')"
                ]
            ])
            ->add('city', TextType::class, [
                'attr' => ['pattern' => '[a-zA-Z" "]*',
                    'oninvalid' => "setCustomValidity('This field must contain only letters and space')"
                ]
            ])
            ->add('address')
            ->add('recipient', TextType::class, [
                'attr' => [
                    'placeholder' => 'Firstname Lastname',
                    'pattern' => '[a-zA-Z\-\']{1,15}[" "]{1}[a-zA-Z\-\']{1,15}',
                    'oninvalid' => "setCustomValidity('Enter your Firstname Lastname')"
                ]
            ])
            ->add('phoneNumber', TextType::class, [
                'attr' => [
                    'placeholder' => '+373 760 32 200', // настоящии номер анонимных наркоманов 🤣
                    'pattern' => '[+]?[0-9" "]{5,25}',
                    'oninvalid' => "setCustomValidity('The phone number must contain only + (optional), numbers, and spaces (optional)')"
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}