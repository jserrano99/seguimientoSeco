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
 * Class ImportesContratoAnualidadDatatable
 *
 * @package AppBundle\Datatables
 */
class ImportesContratoAnualidadDatatable extends AbstractDatatable
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
            'state_save' => false
		]);

		$formatter = new NumberFormatter("es_ES", NumberFormatter::CURRENCY);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
			->add('contrato.codigo', Column::class, ['title' => 'Contrato', 'width' => '200px', 'searchable' => false])
			->add('anyo.descripcion', Column::class, ['title' => 'Anualidad', 'width' => '100px', 'searchable' => true])
			->add('posicionEconomica.descripcion', Column::class, ['title' => 'Posición Económica', 'width' => '100px', 'searchable' => true])
			->add('importe', NumberColumn::class, ['title' => 'Total Factura',
				'width' => '40px',
				'formatter' =>$formatter,
				'use_format_currency' => true,
				'currency' => 'EUR',
				'searchable' => true]

//			->add(null, ActionColumn::class, [
//				'title' => 'Acciones',
//				'actions' => [
//					['route' => 'editImportesContratoAnualidad',
//						'route_parameters' => [
//							'id' => 'id'],
//						'label' => 'Editar ImportesContratoAnualidad',
//						'icon' => 'glyphicon glyphicon-edit',
//						'attributes' => [
//							'rel' => 'tooltip',
//							'title' => 'Editar',
//							'class' => 'btn btn-success btn-xs',
//							'role' => 'button'],
//					]
//				]]
			);

	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\ImportesContratoAnualidad';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'contrato_datatable';
	}

}
