<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class CargaFicheroDatatable
 *
 * @package AppBundle\Datatables
 */
class CargaFicheroDatatable extends AbstractDatatable
{

	/**
	 * {@inheritdoc}
	 */
	public function buildDatatable(array $options = array())
	{
		$this->language->set(array(
//            'cdn_language_by_locale' => true
			'language' => 'es'
		));

		$this->ajax->set(array());

		$this->options->set(array(
			'classes' => Style::BOOTSTRAP_4_STYLE,
			'stripe_classes' => ['strip1', 'strip2', 'strip3'],
			'individual_filtering' => true,
			'individual_filtering_position' => 'head',
			'order' => array(array(0, 'desc')),
			'order_cells_top' => true,
			'search_in_non_visible_columns' => true,
			'dom' => 'lBfrtip',

		));

		$this->features->set(array(
			'auto_width' => true,
			'ordering' => true,
			'length_change' => true,
			'state_save' => false
		));

		$this->events->set([
			'xhr' => ['template' => 'fin.js.twig'],
			'pre_xhr' => ['template' => 'inicio.js.twig'],
			'search' => ['template' => 'search.js.twig'],
			'state_loaded' => ['template' => 'loaded.js.twig'],

		]);


		$this->columnBuilder
			->add('id', Column::class, array('title' => 'Id', 'width' => '15px', 'searchable' => false))
			->add('fichero', Column::class, array('title' => 'Fichero', 'visible' => false))
			->add('fechaCarga', DateTimeColumn::class, array('title' => 'Fecha Proceso', 'width' => '150px',
				'date_format' => 'DD/MM/YYYY HH:MM:ss',
				'filter' => array(DateRangeFilter::class, array(
					'cancel_button' => false,
				)),
			))
			->add('usuario.codigo', Column::class, array(
				'title' => 'Usuario', 'width' => '20px'
			))
			->add('descripcion', Column::class, array(
				'title' => 'Descripción' ,'width' => '220px'
			))
			->add('numeroRegistros', Column::class, array(
				'title' => 'Nº Registros','width' => '20px'
			))
			->add('numeroRegistrosCargados', Column::class, array(
				'title' => 'Registros Cargados','width' => '20px'
			))
			->add('ficheroLog.nombreFichero', Column::class, array(
				'title' => 'Fichero Log',
				'default_content' => ''
			))
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'descargaLog',
						'route_parameters' => ['id' => 'id'],
						'label' => 'Log',
						'icon' => 'glyphicon glyphicon-download',
						'render_if' => function ($row) {
							if ($row["ficheroLog"] != null)
								return true;
						},
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Descarga Log de Ejecución',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button']
					],
					['route' => 'deleteCargaFichero',
						'route_parameters' => ['id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-trash',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Borrar',
							'class' => 'btn btn-danger btn-xs',
							'role' => 'button']
					]
				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\CargaFichero';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'cargafichero_datatable';
	}

}
