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
class CertificadoServiciosRepository extends \Doctrine\orm\EntityRepository
{
	/**
	 * @param $Mes
	 * @return |null
	 */
	public function findByMes($Mes)
	{
		$CertificadoServiciosAll = $this->createQueryBuilder('u')
			->where('u.mes = :mes')
			->setParameter('mes', $Mes)
			->getQuery()->getResult();

		if ($CertificadoServiciosAll) {
			return $CertificadoServiciosAll[0];
		} else {
			return null;
		}
	}

}
