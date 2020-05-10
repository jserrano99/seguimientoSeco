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
 * Class RemedyDatatable
 *
 * @package AppBundle\Datatables
 */
class RemedyDatatable extends AbstractDatatable
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
            'state_save' => false
		]);

		$Centros = $this->getEntityManager()->getRepository("AppBundle:Centro")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->getQuery()->getResult();
		$Aplicaciones = $this->getEntityManager()->getRepository("AppBundle:Aplicacion")
			->createQueryBuilder('u')
			->orderBy('u.codigo', 'ASC')
			->getQuery()->getResult();
		$UsuariosRemedy = $this->getEntityManager()->getRepository("AppBundle:UsuarioRemedy")
			->createQueryBuilder('u')
			->orderBy('u.login', 'ASC')
			->getQuery()->getResult();

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px'])
			->add('numero', Column::class, ['title' => 'Remedy', 'width' => '30px', 'searchable' => true])
			->add('descripcionProblema', Column::class, ['title' => 'Titulo', 'searchable' => true])
			->add('estado', Column::class, ['title' => 'Estado', 'width' => '30px', 'searchable' => true])
			->add('aplicacion.codigo', Column::class, [
				'title' => 'Aplicación',
				'width' => '80px',
				'default_content' =>'',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Aplicaciones, 'codigo', 'codigo'),
						'search_type' => 'eq']]])
			->add('centro.descripcion', Column::class, [
				'title' => 'Centro',
				'width' => '100px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Centros, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add('usuarioRemedy.login', Column::class, [
				'title' => 'Usuario Remedy',
				'width' => '100px',
				'filter' => [SelectFilter::class,
					[
						'multiple' => true,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($UsuariosRemedy, 'login', 'login'),
						'search_type' => 'eq']]])

			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'incidentesByRemedy',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-search',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Ver Encargos',
							'class' => 'btn btn-success btn-xs',
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
		return 'AppBundle\Entity\Remedy';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'remedy_datatable';
	}

}
