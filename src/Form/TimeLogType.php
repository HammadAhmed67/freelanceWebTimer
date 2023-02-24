<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TimeLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
           $builder
               ->add('startTime', DateTimeType::class, [
                   'widget' => 'single_text',
                   'html5' => false,
               ])
               ->add('endTime', DateTimeType::class, [
                   'widget' => 'single_text',
                   'html5' => false,
               ])
               ->add('duration', HiddenType::class, [
                   'attr' => ['readonly' => true],
               ])
               ->add('project', EntityType::class, [
                   'class' => Project::class,
                   'choice_label' => 'name',
                   'placeholder' => 'Select a project',
                   'required' => true,
               ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
