<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 27/05/2019
 * Time: 11:05
 */

namespace AppBundle\Form\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddMesFieldSubscriber implements EventSubscriberInterface
{

//	private $propertyPathToMes;
//
//	public function __construct($propertyPathToMes)
//	{
//		$this->propertyPathToMes = $propertyPathToMes;
//	}

	public static function getSubscribedEvents()
	{
		return array(
			FormEvents::PRE_SET_DATA => 'preSetData',
			FormEvents::PRE_SUBMIT => 'preSubmit'
		);
	}

	private function addMesForm($form, $anyo)
	{
		$formOptions = array(
			'class' => 'AppBundle:Mes',
			'label' => 'Mes',
			'attr' => array(
				'class' => 'class_select_municipio',
			)
//		,
//			'query_builder' => function (EntityRepository $repository) use ($anyo) {
//				$qb = $repository->createQueryBuilder('mes')
//					->innerJoin('mes.anyo', 'anyo')
//					->where('anyo = :anyo')
//					->setParameter('anyo', $anyo);
//
//				return $qb;
//			}
		);

		$form->add('anyo', EntityType::class, $formOptions);
	}

	public function preSetData(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		if (null === $data) {
			return;
		}

		$accessor = PropertyAccess::createPropertyAccessor();

		$mes = $accessor->getValue($data, 'anyo');
		$anyo = ($mes) ? $mes->getAnyo() : null;

		$this->addMesForm($form, $anyo);
	}

	public function preSubmit(FormEvent $event)
	{
		$data = $event->getData();
		$form = $event->getForm();

		$anyo = array_key_exists('anyo', $data) ? $data['anyo'] : null;

		$this->addMesForm($form, $anyo);
	}
}