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
 * @ORM\Table(name="penalizaciones")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PenalizacionRepository")
 *
 */

class Penalizacion
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
	 * @var Indicador\null
	 *
	 * @ORM\ManyToOne(targetEntity="Indicador")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="indicador_id", referencedColumnName="id")
	 * })
	 */
	private $indicador;

    /**
     * @var CertificadoServicios\null
     *
     * @ORM\ManyToOne(targetEntity="CertificadoServicios")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="certificado_servicios_id", referencedColumnName="id")
     * })
     */
    private $certificadoServicios;


    /**
     * @var integer
     *
     * @ORM\Column(name="total_encargos", type="integer", nullable=true)*
     */
    private $totalEncargos;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_encargos_penalizados", type="integer", nullable=true)*
     */
    private $totalEncargosPenalizados;

    /**
     * @var integer
     *
     * @ORM\Column(name="total_cumplen", type="integer", nullable=true)*
     */
    private $totalCumplen;

    /**
     * @var float
     *
     * @ORM\Column(name="porcentaje", type="float", nullable=true)*
     */
    private $porcentaje;

    /**
     * @var float
     *
     * @ORM\Column(name="factor", type="float", nullable=true)*
     */
    private $factor;

    /**
     * @var float
     *
     * @ORM\Column(name="peso", type="float", nullable=true)*
     */
    private $peso;

    /**
	 * @var float
	 *
	 * @ORM\Column(name="importe", type="float", nullable=true)*
	 */

	private $importe;



    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set totalEncargos.
     *
     * @param int|null $totalEncargos
     *
     * @return Penalizacion
     */
    public function setTotalEncargos($totalEncargos = null)
    {
        $this->totalEncargos = $totalEncargos;

        return $this;
    }

    /**
     * Get totalEncargos.
     *
     * @return int|null
     */
    public function getTotalEncargos()
    {
        return $this->totalEncargos;
    }

    /**
     * Set totalCumplen.
     *
     * @param int|null $totalCumplen
     *
     * @return Penalizacion
     */
    public function setTotalCumplen($totalCumplen = null)
    {
        $this->totalCumplen = $totalCumplen;

        return $this;
    }

    /**
     * Get totalCumplen.
     *
     * @return int|null
     */
    public function getTotalCumplen()
    {
        return $this->totalCumplen;
    }

    /**
     * Set porcentaje.
     *
     * @param int|null $porcentaje
     *
     * @return Penalizacion
     */
    public function setPorcentaje($porcentaje = null)
    {
        $this->porcentaje = $porcentaje;

        return $this;
    }

    /**
     * Get porcentaje.
     *
     * @return int|null
     */
    public function getPorcentaje()
    {
        return $this->porcentaje;
    }

    /**
     * Set factor.
     *
     * @param int|null $factor
     *
     * @return Penalizacion
     */
    public function setFactor($factor = null)
    {
        $this->factor = $factor;

        return $this;
    }

    /**
     * Get factor.
     *
     * @return int|null
     */
    public function getFactor()
    {
        return $this->factor;
    }

    /**
     * Set importe.
     *
     * @param float|null $importe
     *
     * @return Penalizacion
     */
    public function setImporte($importe = null)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe.
     *
     * @return float|null
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set indicador.
     *
     * @param \AppBundle\Entity\Indicador|null $indicador
     *
     * @return Penalizacion
     */
    public function setIndicador(\AppBundle\Entity\Indicador $indicador = null)
    {
        $this->indicador = $indicador;

        return $this;
    }

    /**
     * Get indicador.
     *
     * @return \AppBundle\Entity\Indicador|null
     */
    public function getIndicador()
    {
        return $this->indicador;
    }

    /**
     * Set certificadoServicios.
     *
     * @param \AppBundle\Entity\CertificadoServicios|null $certificadoServicios
     *
     * @return Penalizacion
     */
    public function setCertificadoServicios(\AppBundle\Entity\CertificadoServicios $certificadoServicios = null)
    {
        $this->certificadoServicios = $certificadoServicios;

        return $this;
    }

    /**
     * Get certificadoServicios.
     *
     * @return \AppBundle\Entity\CertificadoServicios|null
     */
    public function getCertificadoServicios()
    {
        return $this->certificadoServicios;
    }

    /**
     * Set peso.
     *
     * @param float|null $peso
     *
     * @return Penalizacion
     */
    public function setPeso($peso = null)
    {
        $this->peso = $peso;

        return $this;
    }

    /**
     * Get peso.
     *
     * @return float|null
     */
    public function getPeso()
    {
        return $this->peso;
    }

    /**
     * Set totalEncargosPenalizados.
     *
     * @param int|null $totalEncargosPenalizados
     *
     * @return Penalizacion
     */
    public function setTotalEncargosPenalizados($totalEncargosPenalizados = null)
    {
        $this->totalEncargosPenalizados = $totalEncargosPenalizados;

        return $this;
    }

    /**
     * Get totalEncargosPenalizados.
     *
     * @return int|null
     */
    public function getTotalEncargosPenalizados()
    {
        return $this->totalEncargosPenalizados;
    }
}
