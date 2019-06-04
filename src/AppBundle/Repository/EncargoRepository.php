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
class EncargoRepository extends \Doctrine\orm\EntityRepository
{

	/**
	 * @param $numero
	 * @return mixed
	 */
	public function findByNumero($numero)
	{
		$EncargoAll = $this->createQueryBuilder('u')
			->where('u.numero = :numero')
			->setParameter('numero', $numero)
			->getQuery()->getResult();

		if ($EncargoAll) {
			return $EncargoAll[0];
		} else {
			return null;
		}
	}



}
