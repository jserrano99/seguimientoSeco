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
class AplicacionRepository extends \Doctrine\orm\EntityRepository
{

	/**
	 * @param $codigo
	 * @return mixed
	 */
	public function findByCodigo($codigo)
	{
		$AplicacionAll = $this->createQueryBuilder('u')
			->where('u.codigo = :codigo')
			->setParameter('codigo', $codigo)
			->getQuery()->getResult();

		if ($AplicacionAll) {
			return $AplicacionAll[0];
		} else {
			return null;
		}
	}

}
