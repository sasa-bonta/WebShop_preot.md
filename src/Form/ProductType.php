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
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', TextType::class, [
                'constraints' => new NotBlank(['message' => 'Product code cannot be blank'])
            ])
            ->add('name', TextType::class, [
                'constraints' => new NotBlank(['message' => 'Product name cannot be blank'])
            ])
            ->add('category', ChoiceType::class, [
                'constraints' => new NotBlank(['message' => 'Category cannot be blank']),
                'choices' => [
                    'Tools' => 'tools',
                    'Supplies' => 'supplies',
                    'Cars' => 'cars',
                    'Toys' => 'toys',
                ],])
            ->add('description', TextType::class, [
                'constraints' => new NotBlank(['message' => 'Description cannot be blank'])
            ])
            ->add('price', NumberType::class, [
                'invalid_message' => "price must be number",
                'scale' => 2,
                'constraints' => [new Positive(), new NotBlank(['message' => 'Price cannot be blank'])],
            ])
            ->add('img_path', null, [
                'required' => false,
                'empty_data' => 'no-image.png',
            ])
            ->add('availableAmount', IntegerType::class, [
                'empty_data' => 0,
                'constraints' => [new GreaterThanOrEqual(0)],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
