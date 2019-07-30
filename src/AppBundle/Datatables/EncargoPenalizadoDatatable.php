<?php

namespace AppBundle\Datatables;

use NumberFormatter;
use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\NumberColumn;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;

use Sg\DatatablesBundle\Datatable\Style;


/**
 * Class EncargoPenalizadoDatatable
 *
 * @package AppBundle\Datatables
 */
class EncargoPenalizadoDatatable extends AbstractDatatable
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


        $formatter = new NumberFormatter("es_ES", NumberFormatter::CURRENCY);

        $Indicadores = $this->em->getRepository("AppBundle:Indicador")->findAll();

        $this->columnBuilder
            ->add('id', Column::class, ['title' => 'Id', 'width' => '20px', 'searchable' => false])
            ->add('certificadoServicios.id', Column::class, ['title' => 'Indicador',
                'width' => '400px',
                'searchable' => false,
                'visible' => false])
            ->add('indicador.codigo', Column::class,
                ['title' => 'Indicador',
                    'width' => '40px',
                    'searchable' => true,
                    'filter' => [SelectFilter::class, [
                        'multiple' => false,
                        'select_options' => ['' => 'Todo'] + $this->getOptionsArrayFromEntities($Indicadores, 'codigo', 'codigo'),
                        'search_type' => 'eq']]])
            ->add('encargo.numero', Column::class, ['title' => 'Número', 'width' => '40px', 'searchable' => true])
            ->add('encargo.nmRemedy', Column::class, ['title' => 'Remedy', 'width' => '40px', 'searchable' => true])
            ->add('encargo.titulo', Column::class, ['title' => 'Mes', 'width' => '800px', 'searchable' => true])
            ->add('eliminada', Column::class,
                ['title' => 'Eliminada',
                    'width' => '20px',
                    'searchable' => true,
                    'filter' => [SelectFilter::class, ['search_type' => 'eq',
                        'multiple' => false,
                        'select_options' => [
                            '' => 'Todo',
                            true => 'Si',
                            null => 'No'],
                        'cancel_button' => false],
                    ]])
            ->add(null, ActionColumn::class, [
                'title' => 'Acciones',
                'actions' => [
                    ['route' => 'quitarPenalizacion',
                        'route_parameters' => [
                            'id' => 'id'],
                        'label' => 'Quitar Penalización',
                        'icon' => 'glyphicon glyphicon-trash',
                        'render_if' => function ($row) {
                            if ($row['eliminada'] == '')
                                return true;
                        },
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => 'Eliminar Penalización',
                            'class' => 'btn btn-danger btn-xs',
                            'role' => 'button'],
                    ],
                    ['route' => 'activarPenalizacion',
                        'route_parameters' => [
                            'id' => 'id'],
                        'label' => 'Activar Penalización',
                        'icon' => 'glyphicon glyphicon-edit',
                        'render_if' => function ($row) {
                            if ($row['eliminada'] == true)
                                return true;
                        },
                        'attributes' => [
                            'rel' => 'tooltip',
                            'title' => 'Activar Penalización',
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'],

                    ]]]);

    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'AppBundle\Entity\EncargoPenalizado';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'encargoPenalizado_datatable';
    }

}
