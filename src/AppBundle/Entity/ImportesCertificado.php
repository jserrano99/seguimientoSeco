<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 10/04/2019
 * Time: 17:20
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="importes_certificado_servicios")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImportesCertificadoRepository")
 *
 */

class ImportesCertificado
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
	 * @var CertificadoServicios\null
	 *
	 * @ORM\ManyToOne(targetEntity="CertificadoServicios")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="certificado_servicios_id", referencedColumnName="id")
	 * })
	 */
	private $certificadoServicios;

	/**
	 * @var TipoCuota\null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoCuota")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_cuota_id", referencedColumnName="id")
	 * })
	 */
	private $tipoCuota;

	/**
	 * @var float
	 * @ORM\Column(name="horas_certificadas", type="float",nullable=true)
	 *
	 */
	private $horasCertificadas;

	/**
	 * @var float
	 * @ORM\Column(name="tarifa", type="float",nullable=true)
	 */
	private $tarifa;

	/**
	 * @var PosicionEconomica\null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PosicionEconomica")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="posicion_economica_id", referencedColumnName="id")
	 * })
	 */
	private $posicionEconomica;

	/**
	 * @var float
	 * @ORM\Column(name="importe", type="float",nullable=true)
	 */
	private $importe;

	/**
	 * @var float
	 * @ORM\Column(name="penalizacion", type="float",nullable=true)
	 */
	private $penalizacion;

	/**
	 * @var float
	 * @ORM\Column(name="total", type="float",nullable=true)
	 */
	private $total;



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
     * Set codigo.
     *
     * @param int $codigo
     *
     * @return ImportesCertificado
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return int
     */
    public function getCodigo()
    {
        return $this->codigo;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return ImportesCertificado
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set horasCertificadas.
     *
     * @param float|null $horasCertificadas
     *
     * @return ImportesCertificado
     */
    public function setHorasCertificadas($horasCertificadas = null)
    {
        $this->horasCertificadas = $horasCertificadas;

        return $this;
    }

    /**
     * Get horasCertificadas.
     *
     * @return float|null
     */
    public function getHorasCertificadas()
    {
        return $this->horasCertificadas;
    }

    /**
     * Set tarifa.
     *
     * @param float|null $tarifa
     *
     * @return ImportesCertificado
     */
    public function setTarifa($tarifa = null)
    {
        $this->tarifa = $tarifa;

        return $this;
    }

    /**
     * Get tarifa.
     *
     * @return float|null
     */
    public function getTarifa()
    {
        return $this->tarifa;
    }

    /**
     * Set importe.
     *
     * @param float|null $importe
     *
     * @return ImportesCertificado
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
     * Set penalizacion.
     *
     * @param float|null $penalizacion
     *
     * @return ImportesCertificado
     */
    public function setPenalizacion($penalizacion = null)
    {
        $this->penalizacion = $penalizacion;

        return $this;
    }

    /**
     * Get penalizacion.
     *
     * @return float|null
     */
    public function getPenalizacion()
    {
        return $this->penalizacion;
    }

    /**
     * Set total.
     *
     * @param float|null $total
     *
     * @return ImportesCertificado
     */
    public function setTotal($total = null)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return float|null
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set certificadoServicios.
     *
     * @param \AppBundle\Entity\CertificadoServicios|null $certificadoServicios
     *
     * @return ImportesCertificado
     */
    public function setCertificadoServicios(\AppBundle\Entity\CertificadoServicios $certificadoServicios = null)
    {
        $this->certificadoServicios = $certificadoServicios;

        return $this;
    }

    /**
     * Get certificadoServicios.
     *
     * @return \AppBundle\Entity\CertificadoServicios
     */
    public function getCertificadoServicios()
    {
        return $this->certificadoServicios;
    }

    /**
     * Set tipoCuota.
     *
     * @param \AppBundle\Entity\TipoCuota|null $tipoCuota
     *
     * @return ImportesCertificado
     */
    public function setTipoCuota(\AppBundle\Entity\TipoCuota $tipoCuota = null)
    {
        $this->tipoCuota = $tipoCuota;

        return $this;
    }

	/**
	 * @return TipoCuota\null
	 */
    public function getTipoCuota()
    {
        return $this->tipoCuota;
    }

	/**
	 * @return PosicionEconomica\null
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


}
