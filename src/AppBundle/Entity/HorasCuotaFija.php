<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * HorasCuotaFija
 *
 * @ORM\Table(name="view_horas_cuota_fija",
 *           )
 * @ORM\Entity(readOnly=true)
 */
class HorasCuotaFija {


    /**
     * @var integer
     *
     * @ORM\Column(name="CertificadoServiciosId", type="integer", nullable=false)
     */
    private $CertificadoServiciosId;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="CertificadoServiciosDs", type="string", length=255, nullable=false)
	 */
	private $CertificadoServiciosDs;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="PeriodoDs", type="string", length=255, nullable=false)
	 */
	private $PeriodoDs;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ObjetoEncargoDs", type="string", length=255, nullable=false)
	 */
	private $ObjetoEncargoDs;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="TipoObjetoCd", type="string", length=255, nullable=false)
	 */
	private $TipoObjetoCd;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="TipoObjetoDs", type="string", length=255, nullable=false)
	 */
	private $TipoObjetoDs;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="NumeroEncargos", type="integer", nullable=false)
	 */
	private $NumeroEncargos;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="HorasRealizadas", type="float", nullable=false)
	 */
	private $HorasRealizadas;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="HorasComprometidas", type="float", nullable=false)
	 */
	private $HorasComprometidas;

	/**
	 * @return int
	 */
	public function getCertificadoServiciosId()
	{
		return $this->CertificadoServiciosId;
	}

	/**
	 * @return string
	 */
	public function getCertificadoServiciosDs()
	{
		return $this->CertificadoServiciosDs;
	}

	/**
	 * @return string
	 */
	public function getPeriodoDs()
	{
		return $this->PeriodoDs;
	}

	/**
	 * @return string
	 */
	public function getObjetoEncargoDs()
	{
		return $this->ObjetoEncargoDs;
	}

	/**
	 * @return string
	 */
	public function getTipoObjetoCd()
	{
		return $this->TipoObjetoCd;
	}

	/**
	 * @return string
	 */
	public function getTipoObjetoDs()
	{
		return $this->TipoObjetoDs;
	}

	/**
	 * @return int
	 */
	public function getNumeroEncargos()
	{
		return $this->NumeroEncargos;
	}

	/**
	 * @return float
	 */
	public function getHorasRealizadas()
	{
		return $this->HorasRealizadas;
	}

	/**
	 * @return float
	 */
	public function getHorasComprometidas()
	{
		return $this->HorasComprometidas;
	}



}

