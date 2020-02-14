<?php

namespace AppBundle\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Style;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;

/**
 * Class UsuarioRemedyDatatable
 *
 * @package AppBundle\Datatables
 */
class UsuarioRemedyDatatable extends AbstractDatatable
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

		$this->events->set([
			'xhr' => ['template' => 'fin.js.twig'],
			'pre_xhr' => ['template' => 'inicio.js.twig'],
			'search' => ['template' => 'search.js.twig'],
			'state_loaded' => ['template' => 'loaded.js.twig'],

		]);

		$this->features->set([
			'auto_width' => false,
			'ordering' => true,
			'length_change' => true
		]);

		$Centros = $this->getEntityManager()->getRepository("AppBundle:Centro")
			->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->where('u.valido = true')
			->getQuery()->getResult();


		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
			->add('login', Column::class, ['title' => 'Login', 'width' => '80px', 'searchable' => true])
			->add('apellidos', Column::class, ['title' => 'Apellidos', 'width' => '250px', 'searchable' => true])
			->add('nombre', Column::class, ['title' => 'Nombre', 'width' => '150px', 'searchable' => true])
			->add('centro.descripcion', Column::class, [
				'title' => 'Centro',
				'width' => '415px',
				'default_content' =>'',
				'filter' => [SelectFilter::class,
					[
						'multiple' => false,
						'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Centros, 'descripcion', 'descripcion'),
						'search_type' => 'eq']]])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'editUsuarioRemedy',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button'
						]],
					['route' => 'deleteUsuarioRemedy',
						'route_parameters' => [
							'id' => 'id'],
						'label' => '',
						'icon' => 'glyphicon glyphicon-trash',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Eliminar',
							'class' => 'btn btn-danger btn-xs',
							'role' => 'button'],
						'confirm' => true,
						'confirm_message' => 'Confirmar la Eliminación de Usuario Remedy'],
				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\UsuarioRemedy';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'usuarioRemedy_datatable';
	}

}
