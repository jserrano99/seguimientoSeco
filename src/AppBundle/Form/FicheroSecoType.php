<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Choice;

class FicheroSecoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('anyo', EntityType::class, [
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
            ->add('tipoObjeto', ChoiceType::class, [
                'label' => 'Tipo Objeto',
                'disabled' => false,
                'required' => false,
                'choices' => [
                    'Planificable'  => 1,
                    'No Planificable' => 2,
                    'Adaptaciones Menores' => 4,
                    'Todo' =>3],
                'attr' => ["class" => "form-control "]])

            ->add('generarFichero', SubmitType::class, [
				'label' => 'Generar Fichero',
				"attr" => ["class" => "btn btn-t btn-success"]]);

		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formFicheroSeco';
	}

}
