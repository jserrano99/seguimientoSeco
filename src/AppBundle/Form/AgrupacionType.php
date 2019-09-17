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

class AgrupacionType extends AbstractType
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
			->add('tipoAgrupacion', EntityType::class, [
				'label' => 'TipoAgrupacion',
				'class' => 'AppBundle:TipoAgrupacion',
				'required' => "required",
				'placeholder' => 'Seleccione Tipo de Agrupacion....',
				'attr' => ["class" => "form-control"]
			])
			->add('posicionEconomica', EntityType::class, [
				'label' => 'Posición Economica ',
				'class' => 'AppBundle:PosicionEconomica',
				'required' => false,
				'placeholder' => 'SeleccionePosición Posición Económica...',
				'attr' => ["class" => "form-control"]
			])
			->add('fcInicio', DateType::class, [
				"label" => 'Fecha Inicio',
				"required" => false,
				"disabled" => false,
				'widget' => 'single_text',
				'attr' => [
					'class' => 'form-control corto',
					'data-date-format' => 'dd-mm-yyyy',
					'data-class' => 'string']])
			->add('fcFin', DateType::class, [
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
			'data_class' => 'AppBundle\Entity\Agrupacion'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formAgrupacion';
	}
}
