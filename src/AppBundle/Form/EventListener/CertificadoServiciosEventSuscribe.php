<?php

namespace AppBundle\Form\EventListener;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class CertificadoServiciosEventSuscribe implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit',
			FormEvents::POST_SET_DATA => 'postSetData'
		);
	}

	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if ($data->getEstadoCertificado() == null) {
			$form->add('anyo', EntityType::class, array(
				'label' => 'Año',
				'class' => 'AppBundle:Anyo',
				'mapped' => false,
				'required' => "required",
				'placeholder' => 'Seleccione Año ....',
				'attr' => array("class" => "form-control")))
				->add('mes', EntityType::class, array(
					'label' => 'Mes',
					'class' => 'AppBundle:Mes',
					'required' => "required",
					'placeholder' => 'Seleccione mes ....',
					'attr' => array("class" => "form-control")))
				->add('aplicaPenalizacion', ChoiceType::class, array(
					"label" => 'Aplicar Penalizaciones ',
					"required" => "required",
					"choices" => array("Si" => true,
						"No" => false),
					"attr" => array("class" => "form-control"
					)))
				->add('generarCertificado', SubmitType::class, array(
					"attr" => array("class" => "btn btn-t btn-success")));
		} else {
			$form
				->add('id', TextType::class, array(
					"label" => 'Identificador',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))
				->add('mes', EntityType::class, array(
					'label' => 'Mes',
					'class' => 'AppBundle:Mes',
					'disabled' => true,
					'attr' => array("class" => "form-control")))
				->add('descripcion', TextType::class, array(
					"label" => 'Descripción',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))
				->add('estadoCertificado', EntityType::class, array(
					'label' => 'Estado Certificado',
					'class' => 'AppBundle:EstadoCertificado',
					'disabled' => true,
					'attr' => array("class" => "form-control")))
				->add('totalFactura', TextType::class, array(
					"label" => 'Base Imponible',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))
				->add('cuotaIva', TextType::class, array(
					"label" => 'Cuota IVA (21%)',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))->add('penalizacionAplicable', TextType::class, array(
					"label" => 'Penalización Aplicable',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))
				->add('totalPenalizaciones', TextType::class, array(
					"label" => 'Total Penalizaciones',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)))
				->add('totalFacturaConIva', TextType::class, array(
					"label" => 'Total Factura',
					"required" => 'required',
					'disabled' => true,
					"attr" => array("class" => "form-control"
					)));

			$form->add('queryEncargos', ButtonType::class, array(
				'label' => 'Ver Encargos',
				"attr" => array("class" => "btn btn-t btn-success")));


			if ($data->getEstadoCertificado() == 'PENDIENTE') {
				$form->add('generarImportes', ButtonType::class, array(
					'label' => 'Generar Importes',
					"attr" => array("class" => "btn btn-t btn-success")));
				$form->add('incluirEncargo', ButtonType::class, array(
					'label' => 'Añadir Encargos',
					"attr" => array("class" => "btn btn-t btn-success")));
			}

			if ($data->getEstadoCertificado() == 'GENERADO' or
				$data->getEstadoCertificado() == 'IMPRESO') {
				$form
					->add('generarImportes', ButtonType::class, array(
						'label' => 'Generar Importes',
						"attr" => array("class" => "btn btn-t btn-success")))
					->add('imprimirCertificado', ButtonType::class, array(
						'label' => 'Imprimir Certificado Servicios',
						"attr" => array("class" => "btn btn-t btn-success")))
					->add('imprimirCertificadoActividad', ButtonType::class, array(
						'label' => 'Imprimir Certificado Actividad',
						"attr" => array("class" => "btn btn-t btn-success")))
					->add('incluirEncargo', ButtonType::class, array(
						'label' => 'Añadir Encargos',
						"attr" => array("class" => "btn btn-t btn-success")))
					->add('cerrarCertificado', ButtonType::class, array(
						'label' => 'Cerrar Certificado Servicios',
						"attr" => array("class" => "btn btn-t btn-success")));
			}

			if ($data->getEstadoCertificado() == 'CERRADO') {
				$form
					->add('imprimirCertificado', ButtonType::class, array(
						'label' => 'Imprimir Certificado Servicios',
						"attr" => array("class" => "btn btn-t btn-success")))
					->add('imprimirCertificadoActividad', ButtonType::class, array(
						'label' => 'Imprimir Certificado Actividad',
						"attr" => array("class" => "btn btn-t btn-success")));
			}

			if ($data->getEstadoCertificado() != 'CERRADO') {
				$form
					->add('eliminarCertificado', ButtonType::class, array(
						'label' => 'Eliminar Certificado Servicios',
						"attr" => array("class" => "btn btn-t btn-danger")));
			}
		}
		if (null === $data) {
			return;
		}

		return;
	}

	public function preSubmit(FormEvent $event)
	{
		$form = $event->getForm();
	}

	public function postSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}


		return;
	}

}
