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

/**
 * Class UsuarioRemedyType
 * @package AppBundle\Form
 */
class UsuarioRemedyType extends AbstractType
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
			->add('login', TextType::class, [
				"label" => 'Login',
				"required" => 'required',
				"attr" => ["class" => "form-control"
				]])
			->add('apellidos', TextType::class, [
				"label" => 'Apellidos',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('nombre', TextType::class, [
				"label" => 'Nombre',
				"required" => true,
				"attr" => ["class" => "form-control"
				]])
			->add('centro', EntityType::class, [
				'label' => 'Centro',
				'class' => 'AppBundle:Centro',
				'required' => true,
				'placeholder' => 'Seleccione Centro...',
				'attr' => ["class" => "form-control"]])
			->add('Guardar', SubmitType::class, [
				"attr" => ["class" => "btn btn-t btn-success"]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\UsuarioRemedy'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formUsuarioRemedy';
	}
}
