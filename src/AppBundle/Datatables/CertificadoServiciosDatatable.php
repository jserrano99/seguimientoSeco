<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class CertificadoServiciosDatatable
 *
 * @package AppBundle\Datatables
 */
class CertificadoServiciosDatatable extends AbstractDatatable
{

	/**
	 * {@inheritdoc}
	 */
	public function buildDatatable(array $options = [])
	{
		$this->language->set([
			//'cdn_language_by_locale' => true
			//'language_by_locale' => true
			'language' => 'es'

		]);

		$this->ajax->set([]);

		$this->options->set([
			'classes' => Style::BOOTSTRAP_4_STYLE,
			'stripe_classes' => ['strip1', 'strip2', 'strip3'],
			'individual_filtering' => true,
			'individual_filtering_position' => 'head',
			'order' => [[0, 'asc']],
			'order_cells_top' => true,
			'search_in_non_visible_columns' => true,
			'dom' => 'lBtrip'
		]);
//		$this->events->set([
//			'xhr' => ['template' => 'fin.js.twig'],
//			'pre_xhr' => ['template' => 'inicio.js.twig'],
//			'search' => ['template' => 'search.js.twig'],
//			'state_loaded' => ['template' => 'loaded.js.twig'],
//
//		]);

		$this->features->set([
			'auto_width' => false,
			'ordering' => true,
			'length_change' => true
		]);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
			->add('descripcion', Column::class, ['title' => 'Descripcion', 'width' => '400px', 'searchable' => true])
			->add('mes.anyo.descripcion', Column::class, ['title' => 'Año', 'width' => '40px', 'searchable' => true])
			->add('mes.descripcion', Column::class, ['title' => 'Mes', 'width' => '40px', 'searchable' => true])
			->add('totalFacturaConIva', Column::class, ['title' => 'Total Factura', 'width' => '40px', 'searchable' => true])
			->add('estadoCertificado.descripcion', Column::class, ['title' => 'Estado Certificado', 'width' => '100px', 'searchable' => true])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'generarImportes',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Generar Importes',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar',
							'class' => 'btn btn-success btn-xs',
							'role' => 'button'],
						'confirm' => true,
						'confirm_message' => 'Confirmar la Generación  de Certificado Servicios'],
					['route' => 'imprimirCertificadoServicios',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Imprimir',
						'icon' => 'glyphicon glyphicon-print',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Imprimir',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button']
					],
					['route' => 'certificadoActividad',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Certificado de Actividad',
						'icon' => 'glyphicon glyphicon-print',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Imprimir',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button']
					],
					['route' => 'deleteCertificadoServicios',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Eliminar',
						'icon' => 'glyphicon glyphicon-trash',
						'render_if' => function ($row) {
							if ($row['estadoCertificado'] != 'FACTURADO')
								return true;
						},
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Eliminar',
							'class' => 'btn btn-danger btn-xs',
							'role' => 'button'],
						'confirm' => true,
						'confirm_message' => 'Confirmar la Eliminación de Certificado Servicios'],
				]]);

	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\CertificadoServicios';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'usuario_datatable';
	}

}
