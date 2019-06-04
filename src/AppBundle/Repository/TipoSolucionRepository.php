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
class TipoSolucionRepository extends \Doctrine\orm\EntityRepository
{

	/**
	 * @param $descripcion
	 * @return mixed
	 */
	public function findByCodigo($descripcion)
	{
		$TipoSolucionAll = $this->createQueryBuilder('u')
			->where('u.descripcion = :descripcion')
			->setParameter('descripcion', $descripcion)
			->getQuery()->getResult();

		if ($TipoSolucionAll) {
			return $TipoSolucionAll[0];
		} else {
			return null;
		}
	}

}
