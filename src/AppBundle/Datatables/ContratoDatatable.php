<?php

namespace AppBundle\Datatables;

use NumberFormatter;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\NumberColumn;

use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Style;

/**
 * Class ContratoDatatable
 *
 * @package AppBundle\Datatables
 */
class ContratoDatatable extends AbstractDatatable
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
			'order' => [[2, 'asc']],
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
			'length_change' => true,
            'state_save' => true
		]);

		$formatter = new NumberFormatter("es_ES", NumberFormatter::CURRENCY);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
			->add('codigo', Column::class, ['title' => 'Código', 'width' => '20px', 'searchable' => false])
			->add('descripcion', Column::class, ['title' => 'Descripcion', 'width' => '1400px', 'searchable' => true])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'editContrato',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Editar Contrato',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar',
							'class' => 'btn btn-success btn-xs',
							'role' => 'button'],
					]
				]]);

	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\Contrato';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'contrato_datatable';
	}

}
