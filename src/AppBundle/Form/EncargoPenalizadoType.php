<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EncargoPenalizadoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('id', TextType::class, [
				"label" => 'Identificador',
				"required" => 'required',
				'disabled' => true,
				"attr" => ["class" => "form-control muycorto"
				]])
			->add('indicador', EntityType::class, [
				'label' => 'Indicador',
				'class' => 'AppBundle:Indicador',
				'disabled' => true,
				'attr' => ["class" => "form-control muycorto"]])
			->add('diasRetrasoValoracion', TextType::class, [
				'label' => 'Días Retraso Valoracion',
				'disabled' => false,
				'required' => false,
				'attr' => ["class" => "form-control muycorto"]])
			->add('diasRetrasoEntrega', TextType::class, [
				'label' => 'Días Retraso Entrega',
				'disabled' => false,
				'required' => false,
				'attr' => ["class" => "form-control corto"]])
			->add('diasPrevistosEjecucion', TextType::class, [
				'label' => 'Días Previstos Ejecución',
				'disabled' => false,
				'required' => false,
				'attr' => ["class" => "form-control corto"]])
			->add('diasEjecucion', TextType::class, [
				'label' => 'Días Ejecución',
				'disabled' => false,
				'required' => false,
				'attr' => ["class" => "form-control corto"]])
			->add('eliminada', ChoiceType::class, [
				'label' => 'Eliminada Penalización',
				'disabled' => false,
				'required' => false,
				'choices' => [
					'Penalización Eliminada' => true,
					'' => null],
				'attr' => ["class" => "form-control "]])
			->add('Guardar', SubmitType::class, [
				"attr" => ["class" => "btn btn-t btn-success"]])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\EncargoPenalizado'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formEncargoPenalizado';
	}
}
