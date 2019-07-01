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
 * @ORM\Table(name="certificado_servicios")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgrupacionRepository")
 *
 */

class CertificadoServicios
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
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var Contrato\null
	 *
	 * @ORM\ManyToOne(targetEntity="Contrato")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="contrato_id", referencedColumnName="id")
	 * })
	 */
	private $contrato;

	/**
	 * @var EstadoCertificado\null
	 *
	 * @ORM\ManyToOne(targetEntity="EstadoCertificado")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="estado_certificado_id", referencedColumnName="id")
	 * })
	 */
	private $estadoCertificado;

	/**
	 * @var Mes\null
	 *
	 * @ORM\ManyToOne(targetEntity="Mes")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="meses_id", referencedColumnName="id")
	 * })
	 */
	private $mes;


	/**
	 * @var FicheroLog \null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\FicheroLog")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="fichero_log_id", referencedColumnName="id")
	 * })
	 */
	private $ficheroLog;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="total_factura", type="float", nullable=true)
	 */

	private $totalFactura;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="total_penalizaciones", type="float", nullable=true)
	 */

	private $totalPenalizaciones;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="maximo_penalizaciones", type="float", nullable=true)
	 */

	private $maximoPenalizaciones;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="penalizacion_aplicable", type="float", nullable=true)
	 */

	private $penalizacionAplicable;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_iva", type="float", nullable=true)
	 */

	private $cuotaIva;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="total_factura_con_iva", type="float", nullable=true)
	 */

	private $totalFacturaConIva;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="importe_cuota_fija_mensual", type="float", nullable=true)
	 */

	private $importeCuotaFijaMensual;


	/**
	 * @var integer
	 *
	 * @ORM\Column(name="contador_NPL", type="integer", nullable=true)
	 */

	private $contadorNPL;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="contador_ADM", type="integer", nullable=true)
	 */

	private $contadorADM;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="aplica_penalizacion", type="boolean", nullable=true)
	 */

	private $aplicaPenalizacion;


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
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return CertificadoServicios
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
     * Set totalFactura.
     *
     * @param float|null $totalFactura
     *
     * @return CertificadoServicios
     */
    public function setTotalFactura($totalFactura = null)
    {
        $this->totalFactura = $totalFactura;

        return $this;
    }

    /**
     * Get totalFactura.
     *
     * @return float|null
     */
    public function getTotalFactura()
    {
        return $this->totalFactura;
    }

    /**
     * Set totalPenalizaciones.
     *
     * @param float|null $totalPenalizaciones
     *
     * @return CertificadoServicios
     */
    public function setTotalPenalizaciones($totalPenalizaciones = null)
    {
        $this->totalPenalizaciones = $totalPenalizaciones;

        return $this;
    }

    /**
     * Get totalPenalizaciones.
     *
     * @return float|null
     */
    public function getTotalPenalizaciones()
    {
        return $this->totalPenalizaciones;
    }

    /**
     * Set maximoPenalizaciones.
     *
     * @param float|null $maximoPenalizaciones
     *
     * @return CertificadoServicios
     */
    public function setMaximoPenalizaciones($maximoPenalizaciones = null)
    {
        $this->maximoPenalizaciones = $maximoPenalizaciones;

        return $this;
    }

    /**
     * Get maximoPenalizaciones.
     *
     * @return float|null
     */
    public function getMaximoPenalizaciones()
    {
        return $this->maximoPenalizaciones;
    }

    /**
     * Set penalizacionAplicable.
     *
     * @param float|null $penalizacionAplicable
     *
     * @return CertificadoServicios
     */
    public function setPenalizacionAplicable($penalizacionAplicable = null)
    {
        $this->penalizacionAplicable = $penalizacionAplicable;

        return $this;
    }

    /**
     * Get penalizacionAplicable.
     *
     * @return float|null
     */
    public function getPenalizacionAplicable()
    {
        return $this->penalizacionAplicable;
    }

    /**
     * Set cuotaIva.
     *
     * @param float|null $cuotaIva
     *
     * @return CertificadoServicios
     */
    public function setCuotaIva($cuotaIva = null)
    {
        $this->cuotaIva = $cuotaIva;

        return $this;
    }

    /**
     * Get cuotaIva.
     *
     * @return float|null
     */
    public function getCuotaIva()
    {
        return $this->cuotaIva;
    }

    /**
     * Set totalFacturaConIva.
     *
     * @param float|null $totalFacturaConIva
     *
     * @return CertificadoServicios
     */
    public function setTotalFacturaConIva($totalFacturaConIva = null)
    {
        $this->totalFacturaConIva = $totalFacturaConIva;

        return $this;
    }

    /**
     * Get totalFacturaConIva.
     *
     * @return float|null
     */
    public function getTotalFacturaConIva()
    {
        return $this->totalFacturaConIva;
    }

    /**
     * Set contrato.
     *
     * @param \AppBundle\Entity\Contrato|null $contrato
     *
     * @return CertificadoServicios
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

    /**
     * Set estadoCertificado.
     *
     * @param \AppBundle\Entity\EstadoCertificado|null $estadoCertificado
     *
     * @return CertificadoServicios
     */
    public function setEstadoCertificado(\AppBundle\Entity\EstadoCertificado $estadoCertificado = null)
    {
        $this->estadoCertificado = $estadoCertificado;

        return $this;
    }

    /**
     * Get estadoCertificado.
     *
     * @return \AppBundle\Entity\EstadoCertificado|null
     */
    public function getEstadoCertificado()
    {
        return $this->estadoCertificado;
    }

    /**
     * Set mes.
     *
     * @param \AppBundle\Entity\Mes|null $mes
     *
     * @return CertificadoServicios
     */
    public function setMes(\AppBundle\Entity\Mes $mes = null)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes.
     *
     * @return \AppBundle\Entity\Mes|null
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set ficheroLog.
     *
     * @param \AppBundle\Entity\FicheroLog|null $ficheroLog
     *
     * @return CertificadoServicios
     */
    public function setFicheroLog(\AppBundle\Entity\FicheroLog $ficheroLog = null)
    {
        $this->ficheroLog = $ficheroLog;

        return $this;
    }

    /**
     * Get ficheroLog.
     *
     * @return \AppBundle\Entity\FicheroLog|null
     */
    public function getFicheroLog()
    {
        return $this->ficheroLog;
    }

    /**
     * Set contadorNPL.
     *
     * @param int|null $contadorNPL
     *
     * @return CertificadoServicios
     */
    public function setContadorNPL($contadorNPL = null)
    {
        $this->contadorNPL = $contadorNPL;

        return $this;
    }

    /**
     * Get contadorNPL.
     *
     * @return int|null
     */
    public function getContadorNPL()
    {
        return $this->contadorNPL;
    }

    /**
     * Set contadorADM.
     *
     * @param int|null $contadorADM
     *
     * @return CertificadoServicios
     */
    public function setContadorADM($contadorADM = null)
    {
        $this->contadorADM = $contadorADM;

        return $this;
    }

    /**
     * Get contadorADM.
     *
     * @return int|null
     */
    public function getContadorADM()
    {
        return $this->contadorADM;
    }

    /**
     * Set importeCuotaFijaMensual.
     *
     * @param float|null $importeCuotaFijaMensual
     *
     * @return CertificadoServicios
     */
    public function setImporteCuotaFijaMensual($importeCuotaFijaMensual = null)
    {
        $this->importeCuotaFijaMensual = $importeCuotaFijaMensual;

        return $this;
    }

    /**
     * Get importeCuotaFijaMensual.
     *
     * @return float|null
     */
    public function getImporteCuotaFijaMensual()
    {
        return $this->importeCuotaFijaMensual;
    }

    /**
     * Set aplicaPenalizacion.
     *
     * @param bool|null $aplicaPenalizacion
     *
     * @return CertificadoServicios
     */
    public function setAplicaPenalizacion($aplicaPenalizacion = null)
    {
        $this->aplicaPenalizacion = $aplicaPenalizacion;

        return $this;
    }

    /**
     * Get aplicaPenalizacion.
     *
     * @return bool|null
     */
    public function getAplicaPenalizacion()
    {
        return $this->aplicaPenalizacion;
    }
}
