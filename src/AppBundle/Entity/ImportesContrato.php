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
 * @ORM\Table(name="importes_contrato")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImportesContratoRepository")
 *
 */

class ImportesContrato
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
	 * @var Contrato
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contrato")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="contrato_id", referencedColumnName="id")
	 * })
	 */
	private $contrato;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_fija", type="float", nullable=true)
	 */
	private $cuotaFija;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_fija_mensual", type="float", nullable=true)
	 */
	private $cuotaFijaMensual;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_variable", type="float", nullable=true)
	 */
	private $cuotaVariable;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_tasada", type="float", nullable=true)
	 */
	private $cuotaTasada;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="tarifa_hora", type="float", nullable=true)
	 */
	private $tarifaHora;




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
     * Set fcInicio.
     *
     * @param \DateTime|null $fcInicio
     *
     * @return ImportesContrato
     */
    public function setFcInicio($fcInicio = null)
    {
        $this->fcInicio = $fcInicio;

        return $this;
    }

    /**
     * Get fcInicio.
     *
     * @return \DateTime|null
     */
    public function getFcInicio()
    {
        return $this->fcInicio;
    }

    /**
     * Set fcFin.
     *
     * @param \DateTime|null $fcFin
     *
     * @return ImportesContrato
     */
    public function setFcFin($fcFin = null)
    {
        $this->fcFin = $fcFin;

        return $this;
    }

    /**
     * Get fcFin.
     *
     * @return \DateTime|null
     */
    public function getFcFin()
    {
        return $this->fcFin;
    }

    /**
     * Set cuotaFija.
     *
     * @param float|null $cuotaFija
     *
     * @return ImportesContrato
     */
    public function setCuotaFija($cuotaFija = null)
    {
        $this->cuotaFija = $cuotaFija;

        return $this;
    }

    /**
     * Get cuotaFija.
     *
     * @return float|null
     */
    public function getCuotaFija()
    {
        return $this->cuotaFija;
    }

    /**
     * Set cuotaFijaMensual.
     *
     * @param float|null $cuotaFijaMensual
     *
     * @return ImportesContrato
     */
    public function setCuotaFijaMensual($cuotaFijaMensual = null)
    {
        $this->cuotaFijaMensual = $cuotaFijaMensual;

        return $this;
    }

    /**
     * Get cuotaFijaMensual.
     *
     * @return float|null
     */
    public function getCuotaFijaMensual()
    {
        return $this->cuotaFijaMensual;
    }

    /**
     * Set cuotaVariable.
     *
     * @param float|null $cuotaVariable
     *
     * @return ImportesContrato
     */
    public function setCuotaVariable($cuotaVariable = null)
    {
        $this->cuotaVariable = $cuotaVariable;

        return $this;
    }

    /**
     * Get cuotaVariable.
     *
     * @return float|null
     */
    public function getCuotaVariable()
    {
        return $this->cuotaVariable;
    }

    /**
     * Set cuotaTasada.
     *
     * @param float|null $cuotaTasada
     *
     * @return ImportesContrato
     */
    public function setCuotaTasada($cuotaTasada = null)
    {
        $this->cuotaTasada = $cuotaTasada;

        return $this;
    }

    /**
     * Get cuotaTasada.
     *
     * @return float|null
     */
    public function getCuotaTasada()
    {
        return $this->cuotaTasada;
    }

    /**
     * Set tarifaHora.
     *
     * @param float|null $tarifaHora
     *
     * @return ImportesContrato
     */
    public function setTarifaHora($tarifaHora = null)
    {
        $this->tarifaHora = $tarifaHora;

        return $this;
    }

    /**
     * Get tarifaHora.
     *
     * @return float|null
     */
    public function getTarifaHora()
    {
        return $this->tarifaHora;
    }

    /**
     * Set contrato.
     *
     * @param \AppBundle\Entity\Contrato|null $contrato
     *
     * @return ImportesContrato
     */
    public function setContrato(\AppBundle\Entity\Contrato $contrato = null)
    {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato.
     *
     * @return \AppBundle\Entity\Contrato|null
     */
    public function getContrato()
    {
        return $this->contrato;
    }
}
