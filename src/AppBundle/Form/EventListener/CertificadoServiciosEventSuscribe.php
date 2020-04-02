<?php

namespace AppBundle\Form\EventListener;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;


class CertificadoServiciosEventSuscribe implements EventSubscriberInterface
{
	public static function getSubscribedEvents()
	{
		return [
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit',
			FormEvents::POST_SET_DATA => 'postSetData'
		];
	}

	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if ($data->getEstadoCertificado() == null) {
			$form
				->add('anyo', EntityType::class, [
					'label' => 'Año',
					'class' => 'AppBundle:Anyo',
					'mapped' => false,
					'required' => "required",
					'placeholder' => 'Seleccione Año ....',
					'attr' => ["class" => "form-control "]])
				->add('mes', EntityType::class, [
					'label' => 'Mes',
					'class' => 'AppBundle:Mes',
					'required' => "required",
					'placeholder' => 'Seleccione mes ....',
					'attr' => ["class" => "form-control "]])
				->add('aplicaPenalizacion', ChoiceType::class, [
					"label" => 'Aplicar Penalizaciones ',
					"required" => "required",
					"choices" => ["Si" => true,
						"No" => false],
					"attr" => ["class" => "form-control "
					]])
				->add('generarCertificado', SubmitType::class, [
					"attr" => ["class" => "btn btn-t btn-success"]]);
		} else {
			$form
				->add('id', TextType::class, [
					"label" => 'Identificador',
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control corto"
					]])
				->add('mes', EntityType::class, [
					'label' => 'Mes',
					'class' => 'AppBundle:Mes',
					'disabled' => true,
					'attr' => ["class" => "form-control "]])
				->add('descripcion', TextType::class, [
					"label" => 'Descripción',
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control medio"
					]])
				->add('estadoCertificado', EntityType::class, [
					'label' => 'Estado Certificado',
					'class' => 'AppBundle:EstadoCertificado',
					'disabled' => true,
					'attr' => ["class" => "form-control "]])
				->add('baseImponible', MoneyType::class, [
					"label" => 'Base Imponible', 'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]])
				->add('totalCuota', MoneyType::class, [
					"label" => 'Total Cuotas ',
					'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]])
				->add('cuotaIva', MoneyType::class, [
					"label" => 'Cuota IVA (21%) ', 'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]])
				->add('penalizacionAplicable', MoneyType::class, [
					"label" => 'Penalización Aplicable ', 'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]])
				->add('totalPenalizaciones', MoneyType::class, [
					"label" => 'Total Penalizaciones ', 'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]])
				->add('totalFacturaConIva', MoneyType::class, [
					"label" => 'Total Factura', 'grouping' => true,
					"required" => 'required',
					'disabled' => true,
					"attr" => ["class" => "form-control "
					]]);

			$form->add('queryEncargos', ButtonType::class, [
				'label' => 'Ver Encargos',
				"attr" => ["class" => "btn btn-t btn-success"]]);


			if ($data->getEstadoCertificado() == 'PENDIENTE') {
				$form->add('generarImportes', ButtonType::class, [
					'label' => 'Generar Importes',
					"attr" => ["class" => "btn btn-t btn-success"]]);
				$form->add('incluirEncargo', ButtonType::class, [
					'label' => 'Añadir Encargos',
					"attr" => ["class" => "btn btn-t btn-success"]]);
				$form->add('queryEncargos', ButtonType::class, [
					'label' => 'Ver  Encargos',
					"attr" => ["class" => "btn btn-t btn-success"]]);
			}

			if ($data->getEstadoCertificado() == 'GENERADO' or $data->getEstadoCertificado() == 'IMPRESO') {
				$form
					->add('generarImportes', ButtonType::class, [
						'label' => 'Generar Importes',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('imprimirCertificado', ButtonType::class, [
						'label' => 'Imprimir Certificado Servicios',
						"attr" => ["class" => "btn btn-t btn-primary"]])
                    ->add('imprimirActividadSCF', ButtonType::class, [
                        'label' => 'Imprimir Actividas Servicios Cuota Fija',
                        "attr" => ["class" => "btn btn-t btn-primary"]])
                    ->add('imprimirCertificadoActividad', ButtonType::class, [
						'label' => 'Imprimir Certificado Actividad',
						"attr" => ["class" => "btn btn-t btn-primary"]])
					->add('exportarReaperturas', ButtonType::class, [
						'label' => 'Exportar Reaperturas',
						"attr" => ["class" => "btn btn-t btn-warning"]])
					->add('revisionPenalizaciones', ButtonType::class, [
						'label' => 'Revisión Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-warning"]])
					->add('cargaRevisionPenalizaciones', ButtonType::class, [
						'label' => ' Carga Revisión Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('penalizaciones', ButtonType::class, [
						'label' => 'Imprimir Informe de Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-primary"]])
					->add('penalizacionesDetalle', ButtonType::class, [
						'label' => 'Detalle Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-primary"]])
					->add('incluirEncargo', ButtonType::class, [
						'label' => 'Añadir Encargos',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('queryEncargos', ButtonType::class, [
						'label' => 'Ver  Encargos',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('cerrarCertificado', ButtonType::class, [
						'label' => 'Cerrar Certificado Servicios',
						"attr" => ["class" => "btn btn-success"]])
					->add('enviarProveedor', ButtonType::class, [
						'label' => 'Enviado a Proveedor',
						"attr" => ["class" => "btn btn-success"]])
					->add('verHorasCuotaFija', ButtonType::class, [
						'label' => 'Horas Cuota Fija',
						"attr" => ["class" => "btn btn-t btn-success"]]);
			}

			if ($data->getEstadoCertificado() == 'CERRADO') {
				$form
					->add('imprimirCertificado', ButtonType::class, [
						'label' => 'Imprimir Certificado Servicios',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('imprimirCertificadoActividad', ButtonType::class, [
						'label' => 'Imprimir Certificado Actividad',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('penalizacionesDetalle', ButtonType::class, [
						'label' => 'Detalle Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-success"]])
					->add('penalizaciones', ButtonType::class, [
						'label' => 'Imprimir Informe de Penalizaciones',
						"attr" => ["class" => "btn btn-t btn-success"]]);
			}

			if ($data->getEstadoCertificado()->getId() == 3) {
				$form->add('abrirCertificado', ButtonType::class, [
					'label' => 'Abrir Certificado Servicios',
					"attr" => ["class" => "btn btn-warning"]]);
				$form->add('verHorasCuotaFija', ButtonType::class, [
					'label' => 'Horas Cuota Fija',
					"attr" => ["class" => "btn btn-success"]]);

			}


			if ($data->getEstadoCertificado() != 'CERRADO') {
				$form
					->add('eliminarCertificado', ButtonType::class, [
						'label' => 'Eliminar Certificado Servicios',
						"attr" => ["class" => "btn btn-t btn-danger"]]);
			}
		}
		if (null === $data) {
			return;
		}

		return;
	}

	/**
	 * @param FormEvent $event
	 */
	public function preSubmit(FormEvent $event)
	{
		$form = $event->getForm();
	}

	/**
	 * @param FormEvent $event
	 */
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
