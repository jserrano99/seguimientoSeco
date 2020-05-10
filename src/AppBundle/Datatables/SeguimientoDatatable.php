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
 * Class SeguimientoDatatable
 *
 * @package AppBundle\Datatables
 */
class SeguimientoDatatable extends AbstractDatatable
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
			'length_change' => true,
			'state_save' => false,
		]);


		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('codigo', Column::class, ['title' => 'Código', 'width' => '25px'])
			->add('descripcion', Column::class, ['title' => 'Descripción', 'width' => '450px', 'searchable' => true])
			->add('fechaInicio', DateTimeColumn::class, [
				'title' => 'F.Inicio',
				'width' => '80px',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => false,
				]],
			])
			->add('fechaFin', DateTimeColumn::class, [
				'title' => 'F.Fin',
				'width' => '80px',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => false,
				]],
			])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'width' => '80px',
				'actions' => [
					['route' => 'editSeguimiento',
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
					['route' => 'queryAgrupacionBySeguimiento',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-folder-open',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Agrupaciones en Seguimiento',
							'class' => 'btn btn-warning btn-xs',
							'role' => 'button'
						]],
					['route' => 'informeSeguimiento',
						'route_parameters' => ['id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-print',
						'attributes' => [
							'rel' => 'tooltip',
							'target' => '_blank',
							'title' => 'Informe de Seguimiento',
							'class' => 'btn btn-success btn-xs',
							'target' => 'blank',
							'role' => 'button']],
					['route' => 'exportarSeguimiento',
						'route_parameters' => ['id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-share',
						'attributes' => [
							'rel' => 'tooltip',
							'target' => '_blank',
							'title' => 'Exportar Seguimiento',
							'class' => 'btn btn-primary btn-xs',
							'target' => 'blank',
							'role' => 'button']]

				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\Seguimiento';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'agrupacion_datatable';
	}

}
