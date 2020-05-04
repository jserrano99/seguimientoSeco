<?php

namespace AppBundle\Form;

use AppBundle\AppBundle;
use AppBundle\Entity\Mes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class PeriodoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('anyo', EntityType::class, [
                'label' => 'Año',
                'class' => 'AppBundle:Anyo',
                'mapped' => false,
                'required' => "required",
                'placeholder' => 'Seleccione Año ....',
                'attr' => ["class" => "form-control "]])
            ->add('mes', EntityType::class, [
                'label' => 'Mes',
                'class' => 'AppBundle:Mes',
                'required' => "required",
                'choice_value' => function ($mes) {
                    return $mes ? $mes->getId() : '';
                },
                'placeholder' => 'Seleccione mes ....',
                'attr' => ["class" => "form-control "]])
            ->add('generar', SubmitType::class, [
                'label' => 'Generar',
                "attr" => ["class" => "btn btn-t btn-success"]]);;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'formPeriodo';
    }

}
