<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code')
            ->add('name', TextType::class, [
                'constraints' => new NotBlank(['message' => 'Product name cannot be blank'])
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Tools' => 'tools',
                    'Supplies' => 'supplies',
                    'Cars' => 'cars',
                    'Toys' => 'toys',
                ],])
            ->add('description')
            ->add('price', NumberType::class, [
                'invalid_message' => "price must be number",
                'scale' => 2,
                'constraints' => [new Positive()],
            ])
            ->add('img_path', null, [
                'required' => false,
                'empty_data' => '/assets/main/images/no-image.png',
            ])
            ->add('availableAmount', IntegerType::class, [
                'required' => false,
                'empty_data' => 0,
                'constraints' => [new Positive()],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
