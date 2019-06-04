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
 * Class UsuarioDatatable
 *
 * @package AppBundle\Datatables
 */
class UsuarioDatatable extends AbstractDatatable
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
			'length_change' => true
		]);

		$this->columnBuilder
			->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
			->add('nombre', Column::class, ['title' => 'Nombre', 'width' => '400px', 'searchable' => true])
			->add('fcAlta', DateTimeColumn::class, [
				'title' => 'Fecha Alta',
				'date_format' => 'DD/MM/YYYY',
				'filter' => [DateRangeFilter::class, [
					'cancel_button' => true,
				]],
			])
			->add('perfil', Column::class, [
				'title' => 'Perfil',
				'filter' => [SelectFilter::class,
					['search_type' => 'eq',
						'multiple' => false,
						'select_options' => [
							'' => 'Todo',
							'Usuario' => 'ROLE_USER',
							'Administrador' => 'ROLE_ADMIN',
							'Control de Costes' => 'ROLE_COSTES'],
						'cancel_button' => false
					],
				],
			])
			->add(null, ActionColumn::class, [
				'title' => 'Acciones',
				'actions' => [
					['route' => 'editUsuario',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Editar',
						'icon' => 'glyphicon glyphicon-edit',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Editar',
							'class' => 'btn btn-primary btn-xs',
							'role' => 'button'
						]],
					['route' => 'deleteUsuario',
						'route_parameters' => [
							'id' => 'id'],
						'label' => 'Eliminar',
						'icon' => 'glyphicon glyphicon-trash',
						'attributes' => [
							'rel' => 'tooltip',
							'title' => 'Eliminar',
							'class' => 'btn btn-danger btn-xs',
							'role' => 'button'],
						'confirm' => true,
						'confirm_message' => 'Confirmar la Eliminación de Usuario'],
				]]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getEntity()
	{
		return 'AppBundle\Entity\Usuario';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'usuario_datatable';
	}

}
