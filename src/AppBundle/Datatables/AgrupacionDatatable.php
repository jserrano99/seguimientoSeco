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
 * Class AgrupacionDatatable
 *
 * @package AppBundle\Datatables
 */
class AgrupacionDatatable extends AbstractDatatable
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
			'state_save' => true,
		]);

		$TipoAgrupacionAll = $this->getEntityManager()->getRepository("AppBundle:TipoAgrupacion")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$SeguimientoAll = $this->getEntityManager()->getRepository("AppBundle:Seguimiento")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$PosicionEcomicaAll = $this->getEntityManager()->getRepository("AppBundle:PosicionEconomica")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('codigo', Column::class, ['title' => 'Código', 'width' => '25px'])
			->add('descripcion', Column::class, ['title' => 'Descripción', 'width' => '450px', 'searchable' => true])
			->add('tipoAgrupacion.descripcion', Column::class, [
				'title' => 'Tipo Agrupación',
				'width' => '200px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($TipoAgrupacionAll, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('posicionEconomica.descripcion', Column::class, [
				'title' => 'Posición Economica',
				'width' => '300px',
				'default_content' => '',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($PosicionEcomicaAll, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('seguimiento.descripcion', Column::class, [
				'title' => 'Linea de Seguimiento',
				'width' => '200px',
				'default_content' => '',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($SeguimientoAll, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('fcInicio', DateTimeColumn::class, [
				'title' => 'F.Inicio',
				'width' => '80px',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => false,
				]],
			])
			->add('fcFin', DateTimeColumn::class, [
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
					['route' => 'editAgrupacion',
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
					['route' => 'queryEncargosAgrupacion',
						'route_parameters' => ['idAgrupacion' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-folder-open',
						'attributes' => [
							'rel' => 'tooltip',
							'target' => '_blank',
							'title' => 'Consulta Encargos de la Agrupación',
							'class' => 'btn btn-warning btn-xs',
							'target' => 'blank',
							'role' => 'button']
					],
					['route' => 'encargosAgrupacion',
						'route_parameters' => ['id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-print',
						'attributes' => [
							'rel' => 'tooltip',
							'target' => '_blank',
							'title' => 'Informe Encargos de la Agrupación',
							'class' => 'btn btn-success btn-xs',
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
		return 'AppBundle\Entity\Agrupacion';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'agrupacion_datatable';
	}

}
