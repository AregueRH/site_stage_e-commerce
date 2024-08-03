<?php

namespace App\Form;

use App\Entity\Produits;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType as SearchFieldType;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // SearchFieldType est spécifiquement conçu pour les champs de recherche. Il est généralement utilisé pour des fonctionnalités de recherche dans les formulaires. Il peut également inclure des comportements par défaut adaptés à la recherche (comme la suggestion automatique ou des ajustements spécifiques au comportement des champs de recherche).
        $builder
            ->add('nom', SearchFieldType::class, [ 
                'required' => false,
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Rechercher par nom']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
