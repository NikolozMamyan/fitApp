<?php
namespace App\Form;

use App\Entity\UserData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class UserDataType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taille', NumberType::class, [
                'label' => 'Taille (cm)',
            ])
            ->add('poids', NumberType::class, [
                'label' => 'Poids (kg)',
            ])
            ->add('age', NumberType::class, [
                'label' => 'Âge',
            ])
            ->add('sexe', ChoiceType::class, [
                'choices' => [
                    'Homme' => 'homme',
                    'Femme' => 'femme',
                ],
                'label' => 'Sexe',
            ])
            ->add('objectif', ChoiceType::class, [
                'choices' => [
                    'Sèche' => 'sèche',
                    'Prise de masse' => 'prise de masse',
                ],
                'label' => 'Objectif',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserData::class,
        ]);
    }
}
