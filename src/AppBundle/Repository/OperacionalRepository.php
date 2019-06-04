<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Repository;

/**
 * @author jluis_local
 */
class OperacionalRepository extends \Doctrine\orm\EntityRepository
{

	/**
	 * @param $descripcion
	 * @return mixed
	 */
	public function findByCodigo($descripcion)
	{
		$OperacionalAll = $this->createQueryBuilder('u')
			->where('u.descripcion = :descripcion')
			->setParameter('descripcion', $descripcion)
			->getQuery()->getResult();

		if ($OperacionalAll) {
			return $OperacionalAll[0];
		} else {
			return null;
		}
	}

}
