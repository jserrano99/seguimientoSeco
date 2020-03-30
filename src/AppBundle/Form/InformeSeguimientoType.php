<?php

namespace AppBundle\Form;

use AppBundle\Repository\CentroRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class InformeSeguimientoType extends AbstractType
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
				'required' => "required",
				'placeholder' => 'Seleccione Año ....',
				'attr' => ["class" => "form-control"]])
			->add('mes', EntityType::class, [
				'label' => 'Mes',
				'class' => 'AppBundle:Mes',
				'required' => "required",
				'placeholder' => 'Seleccione mes ....',
				'attr' => ["class" => "form-control"]])

			->add('Generar', SubmitType::class, [
				"attr" => ["class" => "btn btn-t btn-success"]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formInformeSeguimiento';
	}

}
