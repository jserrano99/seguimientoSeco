<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EncargoType extends AbstractType
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
				"attr" => ["class" => "form-control muycorto"
				]])
			->add('numero', TextType::class, [
				"label" => 'Número',
				"required" => 'required',
				"attr" => ["class" => "form-control"
				]])
			->add('coste', MoneyType::class, [
				"label" => 'Coste',
				"required" => false,
				"attr" => ["class" => "form-control"
				]])

			->add('titulo', TextType::class, [
				"label" => 'Título',
				"required" => 'required',
				"attr" => ["class" => "form-control largo"
				]])
			->add('objetoEncargo', EntityType::class, [
				'label' => 'Objeto Encargo',
				'class' => 'AppBundle:ObjetoEncargo',
				'disabled' => false,
				'attr' => ["class" => "form-control "]])
			->add('estadoActual', EntityType::class, [
				'label' => 'Estado Encargo',
				'class' => 'AppBundle:EstadoEncargo',
				'disabled' => false,
				'attr' => ["class" => "form-control "]])
			->add('agrupacion', EntityType::class, [
				'label' => 'Agrupacion',
				'class' => 'AppBundle:Agrupacion',
				'disabled' => false,
				'attr' => ["class" => "form-control "]])
			->add('criticidad2', EntityType::class, [
				'label' => 'Criticidad',
				'class' => 'AppBundle:Criticidad',
				'disabled' => false,
				'attr' => ["class" => "form-control "]])
			->add('descripcion', TextareaType::class, [
				'label' => 'Descripcion',
				'disabled' => true,
				'attr' => ["class" => "form-control "]])
			->add('solucionUsuario', TextareaType::class, [
				'label' => 'Solución Usuario',
				'disabled' => true,
				'attr' => ["class" => "form-control "]])
			->add('solucionTecnica', TextareaType::class, [
				'label' => 'Solución Técnica',
				'disabled' => true,
				'attr' => ["class" => "form-control "]])
			->add('remedy', EntityType::class, [
				'label' => 'Nº Remedy',
				'class' => 'AppBundle:Remedy',
				'disabled' => true,
				'attr' => ["class" => "form-control "]])
			->add('Guardar', SubmitType::class, [
				"attr" => ["class" => "btn btn-t btn-success"]
			])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\Encargo'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formEncargo';
	}
}
