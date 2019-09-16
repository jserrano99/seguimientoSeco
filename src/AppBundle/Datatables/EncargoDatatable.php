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
 * Class EncargoDatatable
 *
 * @package AppBundle\Datatables
 */
class EncargoDatatable extends AbstractDatatable
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
            'state_save' => true
		]);

		$ObjetosEncargo = $this->getEntityManager()->getRepository("AppBundle:ObjetoEncargo")
			->createQueryBuilder('u')
			->orderBy('u.codigo', 'ASC')
			->getQuery()->getResult();
		$Estados = $this->getEntityManager()->getRepository("AppBundle:EstadoEncargo")
			->createQueryBuilder('u')
			->orderBy('u.codigo', 'ASC')
			->getQuery()->getResult();
		$Agrupaciones = $this->getEntityManager()->getRepository("AppBundle:Agrupacion")
			->createQueryBuilder('u')
			->orderBy('u.codigo', 'ASC')
			->getQuery()->getResult();

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('numero', Column::class, ['title' => 'Número', 'width' => '25px'])
			->add('nmRemedy', Column::class, ['title' => 'Remedy', 'width' => '30px', 'searchable' => true])
			->add('titulo', Column::class, ['title' => 'Titulo', 'searchable' => true])
			->add('agrupacion.codigo', Column::class, [
				'title' => 'Objeto',
				'width' => '80px',
				'default_content' =>'',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Agrupaciones, 'codigo', 'codigo'),
						'search_type' => 'eq']]])

			->add('objetoEncargo.codigo', Column::class, [
				'title' => 'Objeto',
				'width' => '100px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($ObjetosEncargo, 'codigo', 'codigo'),
						'search_type' => 'eq']]])
			->add('estadoActual.codigo', Column::class, [
				'title' => 'Estado Actual',
				'width' => '100px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Estados, 'codigo', 'codigo'),
						'search_type' => 'eq']]])

			->add('fcEstadoActual', DateTimeColumn::class, [
				'title' => 'F.Estado',
				'width' => '180px',
				'date_format' => 'DD/MM/YYYY HH:mm:SS',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => false,
				]],
			])
			->add('horasRealizadas', Column::class, ['title' => 'Horas', 'width' => '20px', 'searchable' => true])
			->add('coste', Column::class, ['title' => 'Coste', 'width' => '20px', 'searchable' => true])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'editEncargo',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar',
							'class' => 'btn btn-success btn-xs',
							'role' => 'button',
							'target' => 'blank'
						]],
					['route' => 'viewEncargoSeco',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-search',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Ver Encargo en SECO',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button',
							'target' => 'blank'
						]],
				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\Encargo';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'encargo_datatable';
	}

}
