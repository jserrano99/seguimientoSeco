<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Fichero
 *
 * @ORM\Table(name="fichero",
 *           )
 * @ORM\Entity
 */
class Fichero {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

	/**
	 * @var \AppBundle\Entity\CargaFichero
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CargaFichero", cascade={"persist"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="carga_fichero_id", referencedColumnName="id")
	 * })
	 */
	private $cargaFichero;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="numero_encargo", type="integer",nullable=false)
	 */
	private $numeroEncargo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_remedy", type="string",length=30,nullable=false)
	 */
	private $numeroRemedy;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="numero_agrupacion", type="string", length=20,nullable=true)
	 */
	private $numeroAgrupacion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="titulo", type="string", length=255,nullable=true)
	 */
	private $titulo;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255,nullable=true)
	 */
	private $descripcion;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="objeto_encargo", type="string", length=3,nullable=false)
	 */
	private $objetoEncargo;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="estado_actual", type="string", length=3,nullable=false)
	 */
	private $estadoActual;


	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_estado_actual", type="datetime", nullable=true)
	 */
	private $fechaEstadoActual;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_registro", type="datetime", nullable=true)
	 */
	private $fechaRegistro;


	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_asignacion", type="datetime", nullable=true)
	 */
	private $fechaAsignacion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_estimada_solucion", type="datetime", nullable=true)
	 */
	private $fechaEstimadaSolucion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_requerida_valoracion", type="datetime", nullable=true)
	 */
	private $fechaRequeridaValoracion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_requierida_entrega", type="datetime", nullable=true)
	 */
	private $fechaRequeridaEntrega;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_entrega_valoracion", type="datetime", nullable=true)
	 */
	private $fechaEntregaValoracion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_compromiso", type="datetime", nullable=true)
	 */
	private $fechaCompromiso;
	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_fin_prevista", type="datetime", nullable=true)
	 */
	private $fechaFinPrevista;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_comienzo_ejecucion", type="datetime", nullable=true)
	 */
	private $fechaComienzoEjecucion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_entrega", type="datetime", nullable=true)
	 */
	private $fechaEntrega;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_resolucion_icm", type="datetime", nullable=true)
	 */
	private $fechaResolucionIcm;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_aceptacion", type="datetime", nullable=true)
	 */
	private $fechaAceptacion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_cierre", type="datetime", nullable=true)
	 */
	private $fechaCierre;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tiempo_total", type="string", length=40,nullable=true)
	 */
	private $tiempoTotal;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="tiempo_resolucion", type="string", length=40,nullable=true)
	 */
	private $tiempoResolucion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="aplicacion", type="string", length=40,nullable=true)
	 */
	private $aplicacion;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="modulo_funcional", type="string", length=40,nullable=true)
	 */
	private $moduloFuncional;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="modulo_tecnico", type="string", length=40,nullable=true)
	 */
	private $moduloTecnico;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="contrato", type="string", length=40,nullable=true)
	 */
	private $contrato;



	/**
	 * @var float
	 *
	 * @ORM\Column(name="horas_valoradas", type="float", nullable=true)
	 */
	private $horasValoradas;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="horas_comprometidas", type="float", nullable=true)
	 */
	private $horasComprometidas;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="horas_realizadas", type="float", nullable=true)
	 */
	private $horasRealizadas;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="coste", type="float", nullable=true)
	 */
	private $coste;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="tipo_solucion", type="string",length=255, nullable=true)
	 */
	private $tipoSolucion;

	/**
	 * @var text
	 *
	 * @ORM\Column(name="solucion_usuario", type="text", nullable=true)
	 */
	private $solucionUsuario;

	/**
	 * @var text
	 *
	 * @ORM\Column(name="solucion_tecnica", type="text", nullable=true)
	 */
	private $solucionTecnica;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="operacional1", type="string",length=255, nullable=true)
	 */
	private $operacional1;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="operacional2", type="string",length=255, nullable=true)
	 */
	private $operacional2;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="operacional3", type="string",length=255, nullable=true)
	 */
	private $operacional3;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="motivo_cancelacion", type="string",length=255, nullable=true)
	 */
	private $motivoCancelacion;


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
     * Set numeroEncargo.
     *
     * @param int $numeroEncargo
     *
     * @return Fichero
     */
    public function setNumeroEncargo($numeroEncargo)
    {
        $this->numeroEncargo = $numeroEncargo;

        return $this;
    }

    /**
     * Get numeroEncargo.
     *
     * @return int
     */
    public function getNumeroEncargo()
    {
        return $this->numeroEncargo;
    }

    /**
     * Set numeroAgrupacion.
     *
     * @param string|null $numeroAgrupacion
     *
     * @return Fichero
     */
    public function setNumeroAgrupacion($numeroAgrupacion = null)
    {
        $this->numeroAgrupacion = $numeroAgrupacion;

        return $this;
    }

    /**
     * Get numeroAgrupacion.
     *
     * @return string|null
     */
    public function getNumeroAgrupacion()
    {
        return $this->numeroAgrupacion;
    }

    /**
     * Set titulo.
     *
     * @param string|null $titulo
     *
     * @return Fichero
     */
    public function setTitulo($titulo = null)
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * Get titulo.
     *
     * @return string|null
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * Set descripcion.
     *
     * @param string|null $descripcion
     *
     * @return Fichero
     */
    public function setDescripcion($descripcion = null)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * Get descripcion.
     *
     * @return string|null
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }

    /**
     * Set objetoEncargo.
     *
     * @param string $objetoEncargo
     *
     * @return Fichero
     */
    public function setObjetoEncargo($objetoEncargo)
    {
        $this->objetoEncargo = $objetoEncargo;

        return $this;
    }

    /**
     * Get objetoEncargo.
     *
     * @return string
     */
    public function getObjetoEncargo()
    {
        return $this->objetoEncargo;
    }

    /**
     * Set estadoActual.
     *
     * @param string $estadoActual
     *
     * @return Fichero
     */
    public function setEstadoActual($estadoActual)
    {
        $this->estadoActual = $estadoActual;

        return $this;
    }

    /**
     * Get estadoActual.
     *
     * @return string
     */
    public function getEstadoActual()
    {
        return $this->estadoActual;
    }

    /**
     * Set fechaEstadoActual.
     *
     * @param \DateTime|null $fechaEstadoActual
     *
     * @return Fichero
     */
    public function setFechaEstadoActual($fechaEstadoActual = null)
    {
        $this->fechaEstadoActual = $fechaEstadoActual;

        return $this;
    }

    /**
     * Get fechaEstadoActual.
     *
     * @return \DateTime|null
     */
    public function getFechaEstadoActual()
    {
        return $this->fechaEstadoActual;
    }

    /**
     * Set fechaRegistro.
     *
     * @param \DateTime|null $fechaRegistro
     *
     * @return Fichero
     */
    public function setFechaRegistro($fechaRegistro = null)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro.
     *
     * @return \DateTime|null
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set fechaAsignacion.
     *
     * @param \DateTime|null $fechaAsignacion
     *
     * @return Fichero
     */
    public function setFechaAsignacion($fechaAsignacion = null)
    {
        $this->fechaAsignacion = $fechaAsignacion;

        return $this;
    }

    /**
     * Get fechaAsignacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAsignacion()
    {
        return $this->fechaAsignacion;
    }

    /**
     * Set fechaEstimadaSolucion.
     *
     * @param \DateTime|null $fechaEstimadaSolucion
     *
     * @return Fichero
     */
    public function setFechaEstimadaSolucion($fechaEstimadaSolucion = null)
    {
        $this->fechaEstimadaSolucion = $fechaEstimadaSolucion;

        return $this;
    }

    /**
     * Get fechaEstimadaSolucion.
     *
     * @return \DateTime|null
     */
    public function getFechaEstimadaSolucion()
    {
        return $this->fechaEstimadaSolucion;
    }

    /**
     * Set fechaRequeridaValoracion.
     *
     * @param \DateTime|null $fechaRequeridaValoracion
     *
     * @return Fichero
     */
    public function setFechaRequeridaValoracion($fechaRequeridaValoracion = null)
    {
        $this->fechaRequeridaValoracion = $fechaRequeridaValoracion;

        return $this;
    }

    /**
     * Get fechaRequeridaValoracion.
     *
     * @return \DateTime|null
     */
    public function getFechaRequeridaValoracion()
    {
        return $this->fechaRequeridaValoracion;
    }

    /**
     * Set fechaRequeridaEntrega.
     *
     * @param \DateTime|null $fechaRequeridaEntrega
     *
     * @return Fichero
     */
    public function setFechaRequeridaEntrega($fechaRequeridaEntrega = null)
    {
        $this->fechaRequeridaEntrega = $fechaRequeridaEntrega;

        return $this;
    }

    /**
     * Get fechaRequeridaEntrega.
     *
     * @return \DateTime|null
     */
    public function getFechaRequeridaEntrega()
    {
        return $this->fechaRequeridaEntrega;
    }

    /**
     * Set fechaEntregaValoracion.
     *
     * @param \DateTime|null $fechaEntregaValoracion
     *
     * @return Fichero
     */
    public function setFechaEntregaValoracion($fechaEntregaValoracion = null)
    {
        $this->fechaEntregaValoracion = $fechaEntregaValoracion;

        return $this;
    }

    /**
     * Get fechaEntregaValoracion.
     *
     * @return \DateTime|null
     */
    public function getFechaEntregaValoracion()
    {
        return $this->fechaEntregaValoracion;
    }

    /**
     * Set fechaCompromiso.
     *
     * @param \DateTime|null $fechaCompromiso
     *
     * @return Fichero
     */
    public function setFechaCompromiso($fechaCompromiso = null)
    {
        $this->fechaCompromiso = $fechaCompromiso;

        return $this;
    }

    /**
     * Get fechaCompromiso.
     *
     * @return \DateTime|null
     */
    public function getFechaCompromiso()
    {
        return $this->fechaCompromiso;
    }

    /**
     * Set fechaFinPrevista.
     *
     * @param \DateTime|null $fechaFinPrevista
     *
     * @return Fichero
     */
    public function setFechaFinPrevista($fechaFinPrevista = null)
    {
        $this->fechaFinPrevista = $fechaFinPrevista;

        return $this;
    }

    /**
     * Get fechaFinPrevista.
     *
     * @return \DateTime|null
     */
    public function getFechaFinPrevista()
    {
        return $this->fechaFinPrevista;
    }

    /**
     * Set fechaComienzoEjecucion.
     *
     * @param \DateTime|null $fechaComienzoEjecucion
     *
     * @return Fichero
     */
    public function setFechaComienzoEjecucion($fechaComienzoEjecucion = null)
    {
        $this->fechaComienzoEjecucion = $fechaComienzoEjecucion;

        return $this;
    }

    /**
     * Get fechaComienzoEjecucion.
     *
     * @return \DateTime|null
     */
    public function getFechaComienzoEjecucion()
    {
        return $this->fechaComienzoEjecucion;
    }

    /**
     * Set fechaEntrega.
     *
     * @param \DateTime|null $fechaEntrega
     *
     * @return Fichero
     */
    public function setFechaEntrega($fechaEntrega = null)
    {
        $this->fechaEntrega = $fechaEntrega;

        return $this;
    }

    /**
     * Get fechaEntrega.
     *
     * @return \DateTime|null
     */
    public function getFechaEntrega()
    {
        return $this->fechaEntrega;
    }

    /**
     * Set fechaResolucionIcm.
     *
     * @param \DateTime|null $fechaResolucionIcm
     *
     * @return Fichero
     */
    public function setFechaResolucionIcm($fechaResolucionIcm = null)
    {
        $this->fechaResolucionIcm = $fechaResolucionIcm;

        return $this;
    }

    /**
     * Get fechaResolucionIcm.
     *
     * @return \DateTime|null
     */
    public function getFechaResolucionIcm()
    {
        return $this->fechaResolucionIcm;
    }

    /**
     * Set fechaAceptacion.
     *
     * @param \DateTime|null $fechaAceptacion
     *
     * @return Fichero
     */
    public function setFechaAceptacion($fechaAceptacion = null)
    {
        $this->fechaAceptacion = $fechaAceptacion;

        return $this;
    }

    /**
     * Get fechaAceptacion.
     *
     * @return \DateTime|null
     */
    public function getFechaAceptacion()
    {
        return $this->fechaAceptacion;
    }

    /**
     * Set fechaCierre.
     *
     * @param \DateTime|null $fechaCierre
     *
     * @return Fichero
     */
    public function setFechaCierre($fechaCierre = null)
    {
        $this->fechaCierre = $fechaCierre;

        return $this;
    }

    /**
     * Get fechaCierre.
     *
     * @return \DateTime|null
     */
    public function getFechaCierre()
    {
        return $this->fechaCierre;
    }

    /**
     * Set tiempoTotal.
     *
     * @param string|null $tiempoTotal
     *
     * @return Fichero
     */
    public function setTiempoTotal($tiempoTotal = null)
    {
        $this->tiempoTotal = $tiempoTotal;

        return $this;
    }

    /**
     * Get tiempoTotal.
     *
     * @return string|null
     */
    public function getTiempoTotal()
    {
        return $this->tiempoTotal;
    }

    /**
     * Set tiempoResolucion.
     *
     * @param string|null $tiempoResolucion
     *
     * @return Fichero
     */
    public function setTiempoResolucion($tiempoResolucion = null)
    {
        $this->tiempoResolucion = $tiempoResolucion;

        return $this;
    }

    /**
     * Get tiempoResolucion.
     *
     * @return string|null
     */
    public function getTiempoResolucion()
    {
        return $this->tiempoResolucion;
    }

    /**
     * Set aplicacion.
     *
     * @param string|null $aplicacion
     *
     * @return Fichero
     */
    public function setAplicacion($aplicacion = null)
    {
        $this->aplicacion = $aplicacion;

        return $this;
    }

    /**
     * Get aplicacion.
     *
     * @return string|null
     */
    public function getAplicacion()
    {
        return $this->aplicacion;
    }

    /**
     * Set moduloFuncional.
     *
     * @param string|null $moduloFuncional
     *
     * @return Fichero
     */
    public function setModuloFuncional($moduloFuncional = null)
    {
        $this->moduloFuncional = $moduloFuncional;

        return $this;
    }

    /**
     * Get moduloFuncional.
     *
     * @return string|null
     */
    public function getModuloFuncional()
    {
        return $this->moduloFuncional;
    }

    /**
     * Set moduloTecnico.
     *
     * @param string|null $moduloTecnico
     *
     * @return Fichero
     */
    public function setModuloTecnico($moduloTecnico = null)
    {
        $this->moduloTecnico = $moduloTecnico;

        return $this;
    }

    /**
     * Get moduloTecnico.
     *
     * @return string|null
     */
    public function getModuloTecnico()
    {
        return $this->moduloTecnico;
    }

    /**
     * Set horasValoradas.
     *
     * @param float|null $horasValoradas
     *
     * @return Fichero
     */
    public function setHorasValoradas($horasValoradas = null)
    {
        $this->horasValoradas = $horasValoradas;

        return $this;
    }

    /**
     * Get horasValoradas.
     *
     * @return float|null
     */
    public function getHorasValoradas()
    {
        return $this->horasValoradas;
    }

    /**
     * Set horasComprometidas.
     *
     * @param float|null $horasComprometidas
     *
     * @return Fichero
     */
    public function setHorasComprometidas($horasComprometidas = null)
    {
        $this->horasComprometidas = $horasComprometidas;

        return $this;
    }

    /**
     * Get horasComprometidas.
     *
     * @return float|null
     */
    public function getHorasComprometidas()
    {
        return $this->horasComprometidas;
    }

    /**
     * Set horasRealizadas.
     *
     * @param float|null $horasRealizadas
     *
     * @return Fichero
     */
    public function setHorasRealizadas($horasRealizadas = null)
    {
        $this->horasRealizadas = $horasRealizadas;

        return $this;
    }

    /**
     * Get horasRealizadas.
     *
     * @return float|null
     */
    public function getHorasRealizadas()
    {
        return $this->horasRealizadas;
    }

    /**
     * Set coste.
     *
     * @param float|null $coste
     *
     * @return Fichero
     */
    public function setCoste($coste = null)
    {
        $this->coste = $coste;

        return $this;
    }

    /**
     * Get coste.
     *
     * @return float|null
     */
    public function getCoste()
    {
        return $this->coste;
    }

    /**
     * Set tipoSolucion.
     *
     * @param string|null $tipoSolucion
     *
     * @return Fichero
     */
    public function setTipoSolucion($tipoSolucion = null)
    {
        $this->tipoSolucion = $tipoSolucion;

        return $this;
    }

    /**
     * Get tipoSolucion.
     *
     * @return string|null
     */
    public function getTipoSolucion()
    {
        return $this->tipoSolucion;
    }

    /**
     * Set solucionUsuario.
     *
     * @param string|null $solucionUsuario
     *
     * @return Fichero
     */
    public function setSolucionUsuario($solucionUsuario = null)
    {
        $this->solucionUsuario = $solucionUsuario;

        return $this;
    }

    /**
     * Get solucionUsuario.
     *
     * @return string|null
     */
    public function getSolucionUsuario()
    {
        return $this->solucionUsuario;
    }

    /**
     * Set solucionTecnica.
     *
     * @param string|null $solucionTecnica
     *
     * @return Fichero
     */
    public function setSolucionTecnica($solucionTecnica = null)
    {
        $this->solucionTecnica = $solucionTecnica;

        return $this;
    }

    /**
     * Get solucionTecnica.
     *
     * @return string|null
     */
    public function getSolucionTecnica()
    {
        return $this->solucionTecnica;
    }

    /**
     * Set operacional1.
     *
     * @param string|null $operacional1
     *
     * @return Fichero
     */
    public function setOperacional1($operacional1 = null)
    {
        $this->operacional1 = $operacional1;

        return $this;
    }

    /**
     * Get operacional1.
     *
     * @return string|null
     */
    public function getOperacional1()
    {
        return $this->operacional1;
    }

    /**
     * Set operacional2.
     *
     * @param string|null $operacional2
     *
     * @return Fichero
     */
    public function setOperacional2($operacional2 = null)
    {
        $this->operacional2 = $operacional2;

        return $this;
    }

    /**
     * Get operacional2.
     *
     * @return string|null
     */
    public function getOperacional2()
    {
        return $this->operacional2;
    }

    /**
     * Set operacional3.
     *
     * @param string|null $operacional3
     *
     * @return Fichero
     */
    public function setOperacional3($operacional3 = null)
    {
        $this->operacional3 = $operacional3;

        return $this;
    }

    /**
     * Get operacional3.
     *
     * @return string|null
     */
    public function getOperacional3()
    {
        return $this->operacional3;
    }

    /**
     * Set motivoCancelacion.
     *
     * @param string|null $motivoCancelacion
     *
     * @return Fichero
     */
    public function setMotivoCancelacion($motivoCancelacion = null)
    {
        $this->motivoCancelacion = $motivoCancelacion;

        return $this;
    }

    /**
     * Get motivoCancelacion.
     *
     * @return string|null
     */
    public function getMotivoCancelacion()
    {
        return $this->motivoCancelacion;
    }

    /**
     * Set cargaFichero.
     *
     * @param \AppBundle\Entity\CargaFichero|null $cargaFichero
     *
     * @return Fichero
     */
    public function setCargaFichero(\AppBundle\Entity\CargaFichero $cargaFichero = null)
    {
        $this->cargaFichero = $cargaFichero;

        return $this;
    }

    /**
     * Get cargaFichero.
     *
     * @return \AppBundle\Entity\CargaFichero|null
     */
    public function getCargaFichero()
    {
        return $this->cargaFichero;
    }

    /**
     * Set numeroRemedy.
     *
     * @param int $numeroRemedy
     *
     * @return Fichero
     */
    public function setNumeroRemedy($numeroRemedy)
    {
        $this->numeroRemedy = $numeroRemedy;

        return $this;
    }

    /**
     * Get numeroRemedy.
     *
     * @return int
     */
    public function getNumeroRemedy()
    {
        return $this->numeroRemedy;
    }

    /**
     * Set contrato.
     *
     * @param string|null $contrato
     *
     * @return Fichero
     */
    public function setContrato($contrato = null)
    {
        $this->contrato = $contrato;

        return $this;
    }

    /**
     * Get contrato.
     *
     * @return string|null
     */
    public function getContrato()
    {
        return $this->contrato;
    }
}
