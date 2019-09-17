<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * 
 *
 * @ORM\Table(name="seguimiento")
 * @ORM\Entity
 */
class Seguimiento {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
     */
    private $fechaInicio;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
	 */
	private $fechaFin;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string",length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="codigo", type="string",length=15, nullable=false)
	 */
	private $codigo;

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
	 * @return Datetime
	 */
	public function getFechaInicio()
	{
		return $this->fechaInicio;
	}

	/**
	 * @param Datetime $fechaInicio
	 */
	public function setFechaInicio($fechaInicio)
	{
		$this->fechaInicio = $fechaInicio;
	}

	/**
	 * @return Datetime
	 */
	public function getFechaFin()
	{
		return $this->fechaFin;
	}

	/**
	 * @param Datetime $fechaFin
	 */
	public function setFechaFin($fechaFin)
	{
		$this->fechaFin = $fechaFin;
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
	 * @return string
	 */
	public function getCodigo()
	{
		return $this->codigo;
	}

	/**
	 * @param string $codigo
	 */
	public function setCodigo($codigo)
	{
		$this->codigo = $codigo;
	}

	public function __toString()
	{
		return $this->descripcion;
	}


}
