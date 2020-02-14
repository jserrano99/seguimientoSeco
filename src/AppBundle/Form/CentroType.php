<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CentroType
 * @package AppBundle\Form
 */
class CentroType extends AbstractType
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
				"label" => 'Código de Centro',
				"required" => 'required',
				"attr" => ["class" => "form-control"
				]])
			->add('descripcion', TextType::class, [
				"label" => 'Descripción',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('centroUnificado', EntityType::class, [
				'label' => 'Centro',
				'class' => 'AppBundle:Centro',
				'required' => false,
				'placeholder' => 'Seleccione Centro Unificado...',
				'attr' => ["class" => "form-control"]])
			->add('valido', ChoiceType::class, [
				'required' => false,
				'choices' => [
					'Hospital MAGMA' => true,
					'' => null],
				'attr' => ["class" => "form-control"]
			])
			->add('Guardar', SubmitType::class, [
				"attr" => ["class" => "btn btn-t btn-success"]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Centro'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formCentro';
	}
}
