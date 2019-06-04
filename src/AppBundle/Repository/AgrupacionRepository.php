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
class AgrupacionRepository extends \Doctrine\orm\EntityRepository
{

	/**
	 * @param $codigo
	 * @return mixed
	 */
	public function findByCodigo($codigo)
	{
		$AgrupacionAll = $this->createQueryBuilder('u')
			->where('u.codigo = :codigo')
			->setParameter('codigo', $codigo)
			->getQuery()->getResult();

		if ($AgrupacionAll) {
			return $AgrupacionAll[0];
		} else {
			return null;
		}
	}

}
