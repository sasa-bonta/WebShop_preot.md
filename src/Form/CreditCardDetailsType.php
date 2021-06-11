<?php


namespace App\Form;


use App\Entity\CreditCardDetails;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreditCardDetailsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'attr' => ['pattern' => '[0-9]{4}[" "]?[0-9]{4}[" "]?[0-9]{4}[" "]?[0-9]{4}',
                    'placeholder' => '1111 1111 1111 1111',],
                'required' => false
            ])
            ->add('cvv', TextType::class, [
                'attr' => ['pattern' => '[0-9]{3}',
                    'placeholder' => '111',],
                'required' => false
            ])
            ->add('expiresAt',
                DateType::class, [
                'widget' => 'single_text',
                    'days' => [1],
                    // @fixme 11/06/2021 doesnt work
                    'years' => range(date("Y"), date("Y") + 12),
                    'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CreditCardDetails::class,
        ]);
    }
}