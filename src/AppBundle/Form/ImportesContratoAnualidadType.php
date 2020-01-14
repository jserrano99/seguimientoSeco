<?php

namespace AppBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportesContratoAnualidadType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', HiddenType::class, [
			"label" => 'Identificador',
			"attr" => ["class" => "form-control muycorto"]]);
		$builder->add('contrato', EntityType::class, [
			'label' => 'Contrato',
			'class' => 'AppBundle:Contrato',
			'disabled' => true,
			'attr' => ["class" => "form-control"]]);
		$builder->add('anyo', EntityType::class, [
			'label' => 'Anualidad',
			'class' => 'AppBundle:Anyo',
			'disabled' => true,
			'attr' => ["class" => "form-control "]]);
		$builder->add('posicionEconomica', EntityType::class, [
			'label' => 'Posición Económica',
			'class' => 'AppBundle:PosicionEconomica',
			'disabled' => true,
			'attr' => ["class" => "form-control"]]);
		$builder->add('importe', MoneyType::class, [
			"label" => 'Importe',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);

		$builder->add('Guardar', SubmitType::class, [
			"attr" => ["class" => "form-submit btn btn-t btn-success"]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => 'AppBundle\Entity\ImportesContratoAnualidad'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formImportesContratoAnualidadEncargo';
	}

}
