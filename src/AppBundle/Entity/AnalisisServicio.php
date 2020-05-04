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
 * @ORM\Table(name="analisis_servicio")
 * @ORM\Entity()
 *
 */
class AnalisisServicio
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
     * @ORM\Column(name="tiempo_resolucion", type="float",nullable=false)
     */
    private $tiempoResolucion;

    /**
     * @var float
     *
     * @ORM\Column(name="tiempo_medio_resolucion", type="float",nullable=false)
     */
    private $tiempoMedioResolucion;

    /**
     * @var Mes \null
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mes")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="mes_id", referencedColumnName="id")
     * })
     */
    private $mes;

    /**
     * @var string
     *
     * @ORM\Column(name="estado", type="string", nullable=true)
     */
    private $estado;

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
    public function getTiempoResolucion()
    {
        return $this->tiempoResolucion;
    }

    /**
     * @param float $tiempoResolucion
     */
    public function setTiempoResolucion($tiempoResolucion)
    {
        $this->tiempoResolucion = $tiempoResolucion;
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
     * @return Mes
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * @param Mes $mes
     */
    public function setMes($mes)
    {
        $this->mes = $mes;
    }

    /**
     * @return string
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param string $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }


}
