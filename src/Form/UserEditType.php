<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'Nom d\'utilisateur',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'required' => false,
                // 'mapped' indique que ce champ n'est pas directement lié à une propriété de l'entité User.on va le récupérer et l'envoyer au controller pour que ce dernier le transfert dans la BDD et donc execute le changement de mdp.
                'mapped' => false,
                // 'attr' : Cet attribut HTML indique au navigateur qu'il s'agit d'un champ pour un nouveau mot de passe, ce qui peut améliorer la sécurité et l'expérience utilisateur en désactivant l'auto-complétion pour ce champ particulier.
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
