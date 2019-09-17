<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 10/04/2019
 * Time: 17:20
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Table(name="agrupacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgrupacionRepository")
 *
 */
class Agrupacion
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="codigo", type="integer",nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fc_inicio", type="datetime", nullable=true)
	 */
	private $fcInicio;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fc_fin", type="datetime", nullable=true)
	 */
	private $fcFin;

	/**
	 * @var TipoAgrupacion\null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoAgrupacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_agrupacion_id", referencedColumnName="id")
	 * })
	 */
	private $tipoAgrupacion;

	/**
	 * @var PosicionEconomica \null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PosicionEconomica")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="posicion_economica_id", referencedColumnName="id")
	 * })
	 */
	private $posicionEconomica;

	/**
	 * @var Seguimiento \null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Seguimiento")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="seguimiento_id", referencedColumnName="id")
	 * })
	 */
	private $seguimiento;


	public function __toString()
	{
		// TODO: Implement __toString() method.
		return $this->codigo . ' ' . $this->descripcion;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function getCodigo()
	{
		return $this->codigo;
	}

	/**
	 * @param int $codigo
	 */
	public function setCodigo($codigo)
	{
		$this->codigo = $codigo;
	}

	/**
	 * @return string
	 */
	public function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * @return Datetime
	 */
	public function getFcInicio()
	{
		return $this->fcInicio;
	}

	/**
	 * @param Datetime $fcInicio
	 */
	public function setFcInicio($fcInicio)
	{
		$this->fcInicio = $fcInicio;
	}

	/**
	 * @return Datetime
	 */
	public function getFcFin()
	{
		return $this->fcFin;
	}

	/**
	 * @param Datetime $fcFin
	 */
	public function setFcFin($fcFin)
	{
		$this->fcFin = $fcFin;
	}

	/**
	 * @return TipoAgrupacion\null
	 */
	public function getTipoAgrupacion()
	{
		return $this->tipoAgrupacion;
	}

	/**
	 * @param TipoAgrupacion\null $tipoAgrupacion
	 */
	public function setTipoAgrupacion($tipoAgrupacion)
	{
		$this->tipoAgrupacion = $tipoAgrupacion;
	}

	/**
	 * @return PosicionEconomica
	 */
	public function getPosicionEconomica()
	{
		return $this->posicionEconomica;
	}

	/**
	 * @param PosicionEconomica $posicionEconomica
	 */
	public function setPosicionEconomica($posicionEconomica)
	{
		$this->posicionEconomica = $posicionEconomica;
	}

	/**
	 * @return Seguimiento
	 */
	public function getSeguimiento()
	{
		return $this->seguimiento;
	}

	/**
	 * @param Seguimiento $seguimiento
	 */
	public function setSeguimiento($seguimiento)
	{
		$this->seguimiento = $seguimiento;
	}


}
