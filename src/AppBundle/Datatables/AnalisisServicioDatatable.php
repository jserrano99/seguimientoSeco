<?php

namespace AppBundle\Datatables;

use NumberFormatter;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\NumberColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Style;


/**
 * Class AnalisisServicioDatatable
 *
 * @package AppBundle\Datatables
 */
class AnalisisServicioDatatable extends AbstractDatatable
{

	/**
	 * {@inheritdoc}
	 */
	public function buildDatatable(array $options = [])
	{
		$this->language->set([
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
			'state_save' => false,
		]);

		$AnyoAll = $this->getEntityManager()->getRepository("AppBundle:Anyo")
			->createQueryBuilder('u')
			->orderBy('u.id', 'ASC')
			->getQuery()->getResult();

		$MesAll = $this->getEntityManager()->getRepository("AppBundle:Mes")
			->createQueryBuilder('u')
			->orderBy('u.id', 'ASC')
			->getQuery()->getResult();

		$CriticidadAll = $this->getEntityManager()->getRepository("AppBundle:Criticidad")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$AplicacionAll = $this->getEntityManager()->getRepository("AppBundle:Aplicacion")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$ObjetoEncargoAll = $this->getEntityManager()->getRepository("AppBundle:ObjetoEncargo")
			->createQueryBuilder('u')
			->where('u.tipoObjeto = 1')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();

		$formatter = new NumberFormatter("es_ES", NumberFormatter::DECIMAL);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('mes.anyo.descripcion', Column::class, [
				'title' => 'Año',
				'width' => '200px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($AnyoAll, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('mes.descripcion', Column::class, [
				'title' => 'Periodo',
				'width' => '200px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($MesAll, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('totalEntradas', NumberColumn::class, ['title' => 'Total Factura',
				'width' => '40px',
				'formatter' =>$formatter,
				'searchable' => false])

			->add(null, ActionColumn::class, [
					'title' => 'Acciones',
					'width' => '80px',
					'actions' => [
						['route' => 'queryAnalisisServicioPeriodo',
							'route_parameters' => [
								'id' => 'id'],
							'label' => '',
							'icon' => 'glyphicon glyphicon-find',
							'attributes' => [
								'rel' => 'tooltip',
								'title' => 'Ver Detalle',
								'class' => 'btn btn-primary btn-xs',
								'role' => 'button'
							]
						]
					]
				]
			);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\AnalisisServicio';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'AnalisisServicio_datatable';
	}

}
