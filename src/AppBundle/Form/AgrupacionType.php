<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
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
			->add('id', TextType::class, array(
				"label" => 'Identificador',
				"attr" => array("class" => "form-control"
				)))
			->add('codigo', TextType::class, array(
				"label" => 'Código',
				"required" => 'required',
				"attr" => array("class" => "form-control"
				)))
			->add('descripcion', TextType::class, array(
				"label" => 'Descripción',
				"required" => 'required',
				"attr" => array("class" => "form-control"
				)))
			->add('tipoAgrupacion', EntityType::class, array(
				'label' => 'TipoAgrupacion',
				'class' => 'AppBundle:TipoAgrupacion',
				'required' => "required",
				'placeholder' => 'Seleccione Tipo de Agrupacion....',
				'attr' => array("class" => "form-control")
			))
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
		$resolver->setDefaults(array(
			'data_class' => 'AppBundle\Entity\Agrupacion'
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formAgrupacion';
	}
}
