<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class AddEncargoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('numeroEncargo', TextType::class, array(
			'label' => 'Número de Encargo',
			'required' => "required",
			'attr' => array("class" => "form-control")))

			->add('Guardar', SubmitType::class, array(
				"attr" => array("class" => "btn btn-t btn-success")));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formAddEncargo';
	}

}
