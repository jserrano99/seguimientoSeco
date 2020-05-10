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
 * @ORM\Table(name="analisis_servicio_periodo")
 * @ORM\Entity()
 *
 */
class AnalisisServicioPeriodo
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
     * @ORM\Column(name="total_entradas", type="integer",nullable=false)
     */
    private $totalEntradas;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_cerrados", type="integer",nullable=false)
     */
    private $totalCerrados;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_cancelados", type="integer",nullable=false)
     */
    private $totalCancelados;

    /**
     * @var integer
     *
     * @ORM\Column(name="saldo", type="integer",nullable=false)
     */
    private $saldo;

    /**
     * @var float
     *
     * @ORM\Column(name="esfuerzo_total", type="float",nullable=false)
     */
    private $esfuerzoTotal;

    /**
     * @var float
     *
     * @ORM\Column(name="esfuerzo_medio", type="float",nullable=false)
     */
    private $esfuerzoMedio;

    /**
     * @var float
     *
     * @ORM\Column(name="tiempo_total_resolucion", type="float",nullable=false)
     */
    private $tiempoTotalResolucion;

    /**
     * @var float
     *
     * @ORM\Column(name="tiempo_medio_resolucion", type="float",nullable=false)
     */
    private $tiempoMedioResolucion;

    /**
     * @var AnalisisServicio \null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AnalisisServicio")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analisis_servicio_id", referencedColumnName="id")
     * })
     */
    private $analisisServicio;

    /**
     * @var ObjetoEncargo \null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ObjetoEncargo")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="objeto_encargo_id", referencedColumnName="id")
     * })
     */
    private $objetoEncargo;

    /**
     * @var Criticidad \null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Criticidad")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="criticidad_id", referencedColumnName="id")
     * })
     */
    private $criticidad;

    /**
     * @var Aplicacion \null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Aplicacion")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="aplicacion_id", referencedColumnName="id")
     * })
     */
    private $aplicacion;

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
	public function getTotalEntradas()
	{
		return $this->totalEntradas;
	}

	/**
	 * @param int $totalEntradas
	 */
	public function setTotalEntradas($totalEntradas)
	{
		$this->totalEntradas = $totalEntradas;
	}

	/**
	 * @return int
	 */
	public function getTotalCerrados()
	{
		return $this->totalCerrados;
	}

	/**
	 * @param int $totalCerrados
	 */
	public function setTotalCerrados($totalCerrados)
	{
		$this->totalCerrados = $totalCerrados;
	}

	/**
	 * @return int
	 */
	public function getTotalCancelados()
	{
		return $this->totalCancelados;
	}

	/**
	 * @param int $totalCancelados
	 */
	public function setTotalCancelados($totalCancelados)
	{
		$this->totalCancelados = $totalCancelados;
	}

	/**
	 * @return int
	 */
	public function getSaldo()
	{
		return $this->saldo;
	}

	/**
	 * @param int $saldo
	 */
	public function setSaldo($saldo)
	{
		$this->saldo = $saldo;
	}

	/**
	 * @return float
	 */
	public function getEsfuerzoTotal()
	{
		return $this->esfuerzoTotal;
	}

	/**
	 * @param float $esfuerzoTotal
	 */
	public function setEsfuerzoTotal($esfuerzoTotal)
	{
		$this->esfuerzoTotal = $esfuerzoTotal;
	}

	/**
	 * @return float
	 */
	public function getEsfuerzoMedio()
	{
		return $this->esfuerzoMedio;
	}

	/**
	 * @param float $esfuerzoMedio
	 */
	public function setEsfuerzoMedio($esfuerzoMedio)
	{
		$this->esfuerzoMedio = $esfuerzoMedio;
	}

	/**
	 * @return float
	 */
	public function getTiempoTotalResolucion()
	{
		return $this->tiempoTotalResolucion;
	}

	/**
	 * @param float $tiempoTotalResolucion
	 */
	public function setTiempoTotalResolucion($tiempoTotalResolucion)
	{
		$this->tiempoTotalResolucion = $tiempoTotalResolucion;
	}

	/**
	 * @return float
	 */
	public function getTiempoMedioResolucion()
	{
		return $this->tiempoMedioResolucion;
	}

	/**
	 * @param float $tiempoMedioResolucion
	 */
	public function setTiempoMedioResolucion($tiempoMedioResolucion)
	{
		$this->tiempoMedioResolucion = $tiempoMedioResolucion;
	}

	/**
	 * @return AnalisisServicio
	 */
	public function getAnalisisServicio()
	{
		return $this->analisisServicio;
	}

	/**
	 * @param AnalisisServicio $analisisServicio
	 */
	public function setAnalisisServicio($analisisServicio)
	{
		$this->analisisServicio = $analisisServicio;
	}

	/**
	 * @return ObjetoEncargo
	 */
	public function getObjetoEncargo()
	{
		return $this->objetoEncargo;
	}

	/**
	 * @param ObjetoEncargo $objetoEncargo
	 */
	public function setObjetoEncargo($objetoEncargo)
	{
		$this->objetoEncargo = $objetoEncargo;
	}

	/**
	 * @return Criticidad
	 */
	public function getCriticidad()
	{
		return $this->criticidad;
	}

	/**
	 * @param Criticidad $criticidad
	 */
	public function setCriticidad($criticidad)
	{
		$this->criticidad = $criticidad;
	}

	/**
	 * @return Aplicacion
	 */
	public function getAplicacion()
	{
		return $this->aplicacion;
	}

	/**
	 * @param Aplicacion $aplicacion
	 */
	public function setAplicacion($aplicacion)
	{
		$this->aplicacion = $aplicacion;
	}


}
