<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class PeriodoActualType extends AbstractType
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
                'placeholder' => 'Seleccione mes ....',
                'attr' => ["class" => "form-control "]])
            ->add('actualizarPeriodo', SubmitType::class, [
                'label' => 'Actualizar',
                "attr" => ["class" => "btn btn-t btn-success"]]);;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'formPeriodoActual';
    }

}
