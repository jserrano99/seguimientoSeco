<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AnotacionEncargoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', HiddenType::class, [
			"label" => 'Identificador',
			"attr" => ["class" => "form-control muycorto"]]);
		$builder->add('encargo', EntityType::class, [
			'label' => 'Encargo',
			'class' => 'AppBundle:Encargo',
			'disabled' => true,
			'attr' => ["class" => "form-control "]]);
		$builder->add('usuario', EntityType::class, [
			'label' => 'Usuario',
			'class' => 'AppBundle:Usuario',
			'disabled' => true,
			'attr' => ["class" => "form-control "]]);
		$builder->add('fecha', DateType::class, [
			"label" => 'Fecha',
			"required" => false,
			"disabled" => true,
			'widget' => 'single_text',
			'attr' => [
				'class' => 'form-control corto',
				'data-date-format' => 'dd-mm-yyyy',
				'data-class' => 'string']]);
		$builder->add('anotacion', TextareaType::class, [
			'label' => 'Anotación',
			'disabled' => false,
			'attr' => ["class" => "form-control "]]);

		$builder->add('Guardar', SubmitType::class, [
			"attr" => ["class" => "form-submit btn btn-t btn-success"]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formAnotacionEncargo';
	}

}
