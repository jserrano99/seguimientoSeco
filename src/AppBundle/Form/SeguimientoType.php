<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeguimientoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', HiddenType::class, [
				"label" => 'Identificador',
				"attr" => ["class" => "form-control"
				]])
			->add('codigo', TextType::class, [
				"label" => 'Código',
				"required" => 'required',
				"attr" => ["class" => "form-control"
				]])
			->add('descripcion', TextType::class, [
				"label" => 'Descripción',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('fechaInicio', DateType::class, [
				"label" => 'Fecha Inicio',
				"required" => false,
				"disabled" => false,
				'widget' => 'single_text',
				'attr' => [
					'class' => 'form-control corto',
					'data-date-format' => 'dd-mm-yyyy',
					'data-class' => 'string']])
			->add('fechaFin', DateType::class, [
				"label" => 'Fecha Fin',
				"required" => false,
				"disabled" => false,
				'widget' => 'single_text',
				'attr' => [
					'class' => 'form-control corto',
					'data-date-format' => 'dd-mm-yyyy',
					'data-class' => 'string']])
			->add('Guardar', SubmitType::class, [
					"attr" => ["class" => "btn btn-t btn-success"]
				]

			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Seguimiento'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formSeguimiento';
	}
}
