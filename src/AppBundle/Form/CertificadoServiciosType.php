<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CertificadoServiciosType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('anyo', EntityType::class, array(
				'label' => 'Año',
				'class' => 'AppBundle:Anyo',
				'required' => "required",
				'placeholder' => 'Seleccione Año ....',
				'attr' => array("class" => "form-control")))
			->add('Mes', EntityType::class, array(
				'label' => 'Mes',
				'class' => 'AppBundle:Mes',
				'required' => "required",
				'placeholder' => 'Seleccione mes ....',
				'attr' => array("class" => "form-control")))
			->add('aplicarPenalizaciones', ChoiceType::class, array(
				"label" => 'Aplicar Penalizaciones ',
				"required" => "required",
				"choices" => array("Si" => true,
					"No" => false),
				"attr" => array("class" => "form-control"
				)))
			->add('Generar', SubmitType::class, array(
				"attr" => array("class" => "btn btn-t btn-success")));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'certificado_servicios';
	}

}
