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
class CentroRepository extends \Doctrine\orm\EntityRepository
{
	public function createAlphabeticalQueryBuilder()
	{
		return $this->createQueryBuilder('u')
			->orderBy('u.descripcion', 'ASC')
			->where("u.valido = true");
	}

}
