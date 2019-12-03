<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Repository;

use AppBundle\Entity\Agrupacion;
use Doctrine\DBAL\DBALException;
use Doctrine\orm\EntityRepository;

/**
 * @author jluis_local
 */
class EncargoRepository extends EntityRepository
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

	/**
	 * @param Agrupacion $Agrupacion
	 * @return mixed
	 * @throws DBALException
	 */
	public function findActivosByAgrupacion (Agrupacion $Agrupacion) {
		$conection = $this->getEntityManager()->getConnection();
		$sentencia = " select * from view_encargos_activos where agrupacionId = :id";

		$stmt = $conection->prepare($sentencia);
		$params = [];
		$params[":id"] = $Agrupacion->getId();
		$stmt->execute($params);
		$Encargos = $stmt->fetchAll();

		return ($Encargos);
	}

	/**
	 * @return mixed[]
	 * @throws DBALException
	 */
	public  function findPla (){
		$conection = $this->getEntityManager()->getConnection();
		$sentencia = " select encargoId from view_encargos_pla";

		$stmt = $conection->prepare($sentencia);
		$params = [];
		$stmt->execute($params);
		$Encargos = $stmt->fetchAll();

		return ($Encargos);

	}

}
