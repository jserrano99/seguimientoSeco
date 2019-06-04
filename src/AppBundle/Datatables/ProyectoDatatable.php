<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class ProyectoDatatable
 *
 * @package AppBundle\Datatables
 */
class ProyectoDatatable extends AbstractDatatable
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
			'individual_filtering' => false,
			'individual_filtering_position' => 'head',
			'order' => [[0, 'asc']],
			'order_cells_top' => true,
			'search_in_non_visible_columns' => true,
			'dom' => 'lfBtrip'
		]);

		$this->events->set([
			'xhr' => ['template' => 'fin.js.twig'],
			'pre_xhr' => ['template' => 'inicio.js.twig'],
			'search' => ['template' => 'search.js.twig'],
			'state_loaded' => ['template' => 'loaded.js.twig'],

		]);

		$this->features->set([
			'auto_width' => true,
			'ordering' => true,
			'length_change' => true
		]);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('codigo', Column::class, ['title' => 'Código', 'width' => '25px'])
			->add('descripcion', Column::class, ['title' => 'Descripción', 'width' => '850px', 'searchable' => true])
			->add('fcInicio', DateTimeColumn::class, [
				'title' => 'F.Inicio',
				'width' => '80px',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => true,
				]],
			])
			->add('fcFin', DateTimeColumn::class, [
				'title' => 'F.Fin',
				'width' => '80px',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => true,
				]],
			])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'editProyecto',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar Agrupación',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button'
						]],
					['route' => 'planificacionProyecto',
						'route_parameters' => [
							'pProyecto' => 'codigo'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-print',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Planificación de Encargos del Proyecto',
							'class' => 'btn btn-success btn-xs',
							'target' => 'blank',
							'role' => 'button']
					],
					['route' => 'queryProyectoAgrupacion',
						'route_parameters' => ['id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-folder-open',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Consulta Agrupaciones del Proyecto',
							'class' => 'btn btn-warning btn-xs',
							'target' => 'blank',
							'role' => 'button']
					]
				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\Proyecto';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'agrupacion_datatable';
	}

}
