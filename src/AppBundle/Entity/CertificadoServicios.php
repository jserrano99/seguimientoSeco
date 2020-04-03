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
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_certificado", type="datetime", nullable=false)
	 */
	private $fechaCertificado;

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
	 * @ORM\Column(name="total_cuota", type="float", nullable=true)
	 */

	private $totalCuota;
	/**
	 * @var float
	 *
	 * @ORM\Column(name="base_imponible", type="float", nullable=true)
	 */

	private $baseImponible;



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
     * @ORM\Column(name="contador_NPL_CRI", type="integer", nullable=true)
     */

    private $contadorNPLCRI;


    /**
     * @var integer
     *
     * @ORM\Column(name="contador_NPL_NOR", type="integer", nullable=true)
     */

    private $contadorNPLNOR;

    /**
	 * @var integer
	 *
	 * @ORM\Column(name="contador_ADM", type="integer", nullable=true)
	 */

	private $contadorADM;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador_CAN", type="integer", nullable=true)
     */

    private $contadorCAN;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_CRI", type="integer", nullable=true)
     */

    private $penalizadosCRI;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_NOR", type="integer", nullable=true)
     */

    private $penalizadosNOR;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_reaperturas", type="integer", nullable=true)
     */

    private $penalizadosReaperturas;


    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_ADM", type="integer", nullable=true)
     */

    private $penalizadosADM;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_enc01", type="integer", nullable=true)
     */

    private $penalizadosENC01;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_enc02", type="integer", nullable=true)
     */

    private $penalizadosENC02;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_ent01", type="integer", nullable=true)
     */

    private $penalizadosENT01;

    /**
     * @var integer
     *
     * @ORM\Column(name="penalizados_ent02", type="integer", nullable=true)
     */

    private $penalizadosENT02;

    /**
     * @var integer
     *
     * @ORM\Column(name="contador_ADM_sin_can", type="integer", nullable=true)
     */

    private $contadorADMSinCan;

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
     * Set contadorNPLCRI.
     *
     * @param int|null $contadorNPLCRI
     *
     * @return CertificadoServicios
     */
    public function setContadorNPLCRI($contadorNPLCRI = null)
    {
        $this->contadorNPLCRI = $contadorNPLCRI;

        return $this;
    }

    /**
     * Get contadorNPLCRI.
     *
     * @return int|null
     */
    public function getContadorNPLCRI()
    {
        return $this->contadorNPLCRI;
    }

    /**
     * Set contadorNPLNOR.
     *
     * @param int|null $contadorNPLNOR
     *
     * @return CertificadoServicios
     */
    public function setContadorNPLNOR($contadorNPLNOR = null)
    {
        $this->contadorNPLNOR = $contadorNPLNOR;

        return $this;
    }

    /**
     * Get contadorNPLNOR.
     *
     * @return int|null
     */
    public function getContadorNPLNOR()
    {
        return $this->contadorNPLNOR;
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
     * Set contadorCAN.
     *
     * @param int|null $contadorCAN
     *
     * @return CertificadoServicios
     */
    public function setContadorCAN($contadorCAN = null)
    {
        $this->contadorCAN = $contadorCAN;

        return $this;
    }

    /**
     * Get contadorCAN.
     *
     * @return int|null
     */
    public function getContadorCAN()
    {
        return $this->contadorCAN;
    }

    /**
     * Set penalizadosCRI.
     *
     * @param int|null $penalizadosCRI
     *
     * @return CertificadoServicios
     */
    public function setPenalizadosCRI($penalizadosCRI = null)
    {
        $this->penalizadosCRI = $penalizadosCRI;

        return $this;
    }

    /**
     * Get penalizadosCRI.
     *
     * @return int|null
     */
    public function getPenalizadosCRI()
    {
        return $this->penalizadosCRI;
    }

    /**
     * Set penalizadosNOR.
     *
     * @param int|null $penalizadosNOR
     *
     * @return CertificadoServicios
     */
    public function setPenalizadosNOR($penalizadosNOR = null)
    {
        $this->penalizadosNOR = $penalizadosNOR;

        return $this;
    }

    /**
     * Get penalizadosNOR.
     *
     * @return int|null
     */
    public function getPenalizadosNOR()
    {
        return $this->penalizadosNOR;
    }

    /**
     * Set penalizadosADM.
     *
     * @param int|null $penalizadosADM
     *
     * @return CertificadoServicios
     */
    public function setPenalizadosADM($penalizadosADM = null)
    {
        $this->penalizadosADM = $penalizadosADM;

        return $this;
    }

    /**
     * Get penalizadosADM.
     *
     * @return int|null
     */
    public function getPenalizadosADM()
    {
        return $this->penalizadosADM;
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

    /**
     * Set contrato.
     *
     * @param Contrato|null $contrato
     *
     * @return CertificadoServicios
     */
    public function setContrato(Contrato $contrato = null)
    {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato.
     *
     * @return Contrato|null
     */
    public function getContrato()
    {
        return $this->contrato;
    }

    /**
     * Set estadoCertificado.
     *
     * @param EstadoCertificado|null $estadoCertificado
     *
     * @return CertificadoServicios
     */
    public function setEstadoCertificado(EstadoCertificado $estadoCertificado = null)
    {
        $this->estadoCertificado = $estadoCertificado;

        return $this;
    }

    /**
     * Get estadoCertificado.
     *
     * @return EstadoCertificado|null
     */
    public function getEstadoCertificado()
    {
        return $this->estadoCertificado;
    }

    /**
     * Set mes.
     *
     * @param Mes|null $mes
     *
     * @return CertificadoServicios
     */
    public function setMes(Mes $mes = null)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes.
     *
     * @return Mes|null
     */
    public function getMes()
    {
        return $this->mes;
    }

    /**
     * Set ficheroLog.
     *
     * @param FicheroLog|null $ficheroLog
     *
     * @return CertificadoServicios
     */
    public function setFicheroLog(FicheroLog $ficheroLog = null)
    {
        $this->ficheroLog = $ficheroLog;

        return $this;
    }

    /**
     * Get ficheroLog.
     *
     * @return FicheroLog|null
     */
    public function getFicheroLog()
    {
        return $this->ficheroLog;
    }


    public function  __toString()
    {
     return $this->descripcion;
    }

	/**
	 * @return float
	 */
	public function getTotalCuota()
	{
		return $this->totalCuota;
	}

	/**
	 * @param float $totalCuota
	 */
	public function setTotalCuota($totalCuota)
	{
		$this->totalCuota = $totalCuota;
	}

	/**
	 * @return float
	 */
	public function getBaseImponible()
	{
		return $this->baseImponible;
	}

	/**
	 * @param float $baseImponible
	 */
	public function setBaseImponible($baseImponible)
	{
		$this->baseImponible = $baseImponible;
	}

	/**
	 * @return Datetime
	 */
	public function getFechaCertificado()
	{
		return $this->fechaCertificado;
	}

	/**
	 * @param Datetime $fechaCertificado
	 */
	public function setFechaCertificado($fechaCertificado)
	{
		$this->fechaCertificado = $fechaCertificado;
	}

    /**
     * @return int
     */
    public function getPenalizadosReaperturas()
    {
        return $this->penalizadosReaperturas;
    }

    /**
     * @param int $penalizadosReaperturas
     */
    public function setPenalizadosReaperturas($penalizadosReaperturas)
    {
        $this->penalizadosReaperturas = $penalizadosReaperturas;
    }

    /**
     * @return int
     */
    public function getPenalizadosENC01()
    {
        return $this->penalizadosENC01;
    }

    /**
     * @param int $penalizadosENC01
     */
    public function setPenalizadosENC01($penalizadosENC01)
    {
        $this->penalizadosENC01 = $penalizadosENC01;
    }

    /**
     * @return int
     */
    public function getPenalizadosENC02()
    {
        return $this->penalizadosENC02;
    }

    /**
     * @param int $penalizadosENC02
     */
    public function setPenalizadosENC02($penalizadosENC02)
    {
        $this->penalizadosENC02 = $penalizadosENC02;
    }

    /**
     * @return int
     */
    public function getPenalizadosENT01()
    {
        return $this->penalizadosENT01;
    }

    /**
     * @param int $penalizadosENT01
     */
    public function setPenalizadosENT01($penalizadosENT01)
    {
        $this->penalizadosENT01 = $penalizadosENT01;
    }

    /**
     * @return int
     */
    public function getPenalizadosENT02()
    {
        return $this->penalizadosENT02;
    }

    /**
     * @param int $penalizadosENT02
     */
    public function setPenalizadosENT02($penalizadosENT02)
    {
        $this->penalizadosENT02 = $penalizadosENT02;
    }

    /**
     * @return int
     */
    public function getContadorADMSinCan()
    {
        return $this->contadorADMSinCan;
    }

    /**
     * @param int $contadorADMSinCan
     */
    public function setContadorADMSinCan($contadorADMSinCan)
    {
        $this->contadorADMSinCan = $contadorADMSinCan;
    }



}
