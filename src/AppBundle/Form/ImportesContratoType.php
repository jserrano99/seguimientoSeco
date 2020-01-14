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

class ImportesContratoType extends AbstractType
{
	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder->add('id', HiddenType::class, [
			"label" => 'Identificador',
			"attr" => ["class" => "form-control"]]);
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
		$builder->add('cuotaFija', MoneyType::class, [
			"label" => 'Importe Cuota Fija ',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);
		$builder->add('cuotaFijaMensual', MoneyType::class, [
			"label" => 'Importe Cuota Fija Mensual ',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);
		$builder->add('cuotaVariable', MoneyType::class, [
			"label" => 'Importe Cuota Variable',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);
		$builder->add('cuotaTasada', MoneyType::class, [
			"label" => 'Importe Cuota Tasada',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);
		$builder->add('tarifaHora', MoneyType::class, [
			"label" => 'Tarifa Hora Media',
			"required" => true,
			'disabled' => false,
			'grouping' => true,
			"attr" => ["class" => "form-control"]]);
		$builder->add('tarifaHoraCs', MoneyType::class, [
			"label" => 'Tarifa Hora Consultor Senior',
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
			'data_class' => 'AppBundle\Entity\ImportesContrato'
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix()
	{
		return 'formImportesContratoEncargo';
	}

}
