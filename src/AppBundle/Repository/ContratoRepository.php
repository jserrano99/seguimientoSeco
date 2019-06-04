<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Repository;

/**
 *
 * @author jluis_local
 */
class ContratoRepository extends \Doctrine\orm\EntityRepository {
	
	/**
	 * @param $codigo
	 * @return mixed
	 */
    public function findByCodigo($codigo) {
	    $ContratoAll = $this->createQueryBuilder('u')
		    ->where('u.codigo = :codigo')
		    ->setParameter('codigo',$codigo)
		    ->getQuery()->getResult();
	    if ($ContratoAll) {
	    	$Contrato = $ContratoAll[0];
		    return $Contrato;
	    } else
	    	return null;

    }

}
