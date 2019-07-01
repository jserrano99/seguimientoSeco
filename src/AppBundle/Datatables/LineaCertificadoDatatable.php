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
 * Class LineaCertificadoDatatable
 *
 * @package AppBundle\Datatables
 */
class LineaCertificadoDatatable extends AbstractDatatable
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
			->add('certificadoServicios.id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('encargo.numero', Column::class, ['title' => 'Código', 'width' => '25px'])
			->add('encargo.titulo', Column::class, ['title' => 'Titulo', 'width' => '850px', 'searchable' => true])
			->add('encargo.objetoEncargo.tipoObjeto.codigo', Column::class, ['title' => 'Tipo', 'width' => '50px', 'searchable' => true])
			->add('encargo.objetoEncargo.codigo', Column::class, ['title' => 'Objeto', 'width' => '50px', 'searchable' => true])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'excluirEncargo',
						'route_parameters' => [
							'lineaCertificado_id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Quitar Encargo del Certificado',
							'class' => 'btn btn-danger btn-xs',
							'role' => 'button'
						]],
				]])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\LineaCertificado';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'lineaCertificado_datatable';
	}

}
