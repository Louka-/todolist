<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Todo;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TodoFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        //dd($options);
        $builder
            ->add('title', TextType::class, [
                'label'=> "Un titre",
                'empty_data' => '',
                'attr'=> [
                    'placeholder'=> "entrer le titre ici"
                ]
            ])
            ->add('content', TextareaType::class, [
                'label'=> "contenu?",
                'empty_data' => '',
                'attr'=> [
                    'placeholder'=> "entrez le contenu"
                ]
            ]);
        if($options['data']->getId()!=null){
            $builder->add('date_for', DateType::class, [
                'label'=> "à faire pour : ",
                'years'=> ['2021', '2022'],
                'format'=> 'dd MM yyyy',
            ]);

        }else{
            $builder->add('date_for', DateType::class, [
                'label'=> "à faire pour : ",
                'years'=> ['2021', '2022'],
                'format'=> 'dd MM yyyy',
                'data'=> new \DateTime('now', new \DateTimeZone('Europe/Paris'))
            ]);

        }
            $builder->add('category', EntityType::class, [
                'label'=>"Quelle catégorie?",
                'class'=> Category::class,
                "choice_label"=> "name"
            ])
            ->add('Valider', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Todo::class,
            'attr'=> [
                'novalidate'=>'novalidate'
            ]
        ]);
    }
}
