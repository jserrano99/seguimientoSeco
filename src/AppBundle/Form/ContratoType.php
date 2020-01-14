<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContratoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', TextType::class, [
				"label" => 'Identificador',
				"attr" => ["class" => "form-control"
				]])
			->add('codigo', TextType::class, [
				"label" => 'Código',
				"required" => 'required',
				"attr" => ["class" => "form-control"
				]])
			->add('descripcion', TextareaType::class, [
				"label" => 'Descripción',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
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
			->add('centroCosteCd', TextType::class, [
				"label" => 'Centro de Coste (Código)',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('centroCosteDs', TextType::class, [
				"label" => 'Centro de Coste (Descripción)',
				"required" => true,
				"attr" => ["class" => "form-control largo"
				]])
			->add('expediente', TextType::class, [
				"label" => 'Expediente',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('numeroPedido', TextType::class, [
				"label" => 'Número de Pedido',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('adjudicatario', TextareaType::class, [
				"label" => 'Adjudicatario',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('importeContrato', MoneyType::class, [
				"label" => 'Importe Contrato',
				"required" => true,
				'grouping' => true,
				"attr" => ["class" => "form-control"
				]])
			->add('importeAdjudicacion', MoneyType::class, [
				"label" => 'Importe Adjudicación',
				"required" => true,
				'grouping' => true,
				"attr" => ["class" => "form-control"
				]])
			->add('porcentajeBaja', PercentType::class, [
				"label" => 'Porcentaje Bajada',
				"required" => true,
				'scale' => 2,
				"attr" => ["class" => "form-control"
				]])

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
			'data_class' => 'AppBundle\Entity\Contrato'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formContrato';
	}
}
