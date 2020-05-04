<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 10/04/2019
 * Time: 17:20
 */

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="encargo"
 *         ,uniqueConstraints={@ORM\UniqueConstraint(name="uk_numero", columns={"numero"})}
 *           )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EncargoRepository")
 */
class Encargo
{
	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var integer
	 * @ORM\Column(name="numero", type="integer", nullable=false)
	 */
	private $numero;

	/**
	 * @var Contrato|null
	 * @ORM\ManyToOne(targetEntity="Contrato")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="contrato_id", referencedColumnName="id")
	 * })
	 */
	private $contrato;

	/**
	 * @var Remedy|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Remedy")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="remedy_id", referencedColumnName="id")
	 * })
	 */
	private $remedy;

	/**
	 * @var ObjetoEncargo|null
	 * @ORM\ManyToOne(targetEntity="ObjetoEncargo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="objeto_encargo_id", referencedColumnName="id")
	 * })
	 */
	private $objetoEncargo;

	/**
	 * @var Agrupacion|null
	 * @ORM\ManyToOne(targetEntity="Agrupacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="agrupacion_id", referencedColumnName="id")
	 * })
	 */
	private $agrupacion;

	/**
	 * @var EstadoEncargo|null
	 * @ORM\ManyToOne(targetEntity="EstadoEncargo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="estado_actual_id", referencedColumnName="id")
	 * })
	 */
	private $estadoActual;

	/**
	 * @var Aplicacion|null
	 * @ORM\ManyToOne(targetEntity="Aplicacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="aplicacion_id", referencedColumnName="id")
	 * })
	 */
	private $aplicacion;

	/**
	 * @var ModuloFuncional|null
	 * @ORM\ManyToOne(targetEntity="ModuloFuncional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="modulo_funcional_id", referencedColumnName="id")
	 * })
	 */
	private $moduloFuncional;

	/**
	 * @var ModuloTecnico|null
	 * @ORM\ManyToOne(targetEntity="ModuloTecnico")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="modulo_tecnico_id", referencedColumnName="id")
	 * })
	 */
	private $moduloTecnico;

	/**
	 * @var TipoSolucion|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoSolucion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_solucion_id", referencedColumnName="id")
	 * })
	 */
	private $tipoSolucion;

	/**
	 * @var Criticidad|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Criticidad")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="criticidad_id", referencedColumnName="id")
	 * })
	 */
	private $criticidad2;

	/**
	 * @var Operacional|null
	 * @ORM\ManyToOne(targetEntity="Operacional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="operacional1_id", referencedColumnName="id")
	 * })
	 */
	private $operacional1;

	/**
	 * @var Operacional|null
	 * @ORM\ManyToOne(targetEntity="Operacional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="operacional2_id", referencedColumnName="id")
	 * })
	 */
	private $operacional2;
	/**
	 * @var Operacional|null
	 * @ORM\ManyToOne(targetEntity="Operacional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="operacional3_id", referencedColumnName="id")
	 * })
	 */
	private $operacional3;

	/**
	 * @var string
	 * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
	 */
	private $titulo;

	/**
	 * @var text
	 * @ORM\Column(name="descripcion", type="text",nullable=true)
	 */
	private $descripcion;

	/**
	 * @var string
	 * @ORM\Column(name="nm_remedy", type="string", length=15, nullable=true)
	 */
	private $nmRemedy;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_estado_actual", type="datetime", nullable=false)*
	 */
	private $fcEstadoActual;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_exportacion", type="datetime", nullable=true)*
	 */
	private $fcExportacion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_cierre", type="datetime", nullable=true)*
	 */
	private $fcCierre;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_registro", type="datetime", nullable=true)*
	 */
	private $fcRegistro;
	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_asignacion", type="datetime", nullable=true)*
	 */
	private $fcAsignacion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_estimada_solucion", type="datetime", nullable=true)*
	 */
	private $fcEstimadaSolucion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_requerida_solucion", type="datetime", nullable=true)*
	 */
	private $fcRequeridaSolucion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_requerida_valoracion", type="datetime", nullable=true)*
	 */
	private $fcRequeridaValoracion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_entrega_valoracion", type="datetime", nullable=true)*
	 */
	private $fcEntregaValoracion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_requerida_entrega", type="datetime", nullable=true)*
	 */
	private $fcRequeridaEntrega;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_compromiso", type="datetime", nullable=true)*
	 */
	private $fcCompromiso;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_fin_prevista", type="datetime", nullable=true)*
	 */
	private $fcFinPrevista;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_comienzo_ejecucion", type="datetime", nullable=true)*
	 */
	private $fcComienzoEjecucion;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_entrega", type="datetime", nullable=true)*
	 */
	private $fcEntrega;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_resolucion_icm", type="datetime", nullable=true)*
	 */
	private $fcResolucionIcm;

	/**
	 * @var DateTime
	 * @ORM\Column(name="fc_aceptacion", type="datetime", nullable=true)*
	 */
	private $fcAceptacion;

	/**
	 * @var Float
	 * @ORM\Column(name="tiempo_total", type="float", nullable=true)*
	 */
	private $tiempoTotal;

	/**
	 * @var Float
	 * @ORM\Column(name="tiempo_resolucion", type="float", nullable=true)*
	 */
	private $tiempoResolucion;

	/**
	 * @var Float
	 * @ORM\Column(name="horas_valoradas", type="float", nullable=true)*
	 */
	private $horasValoradas;

	/**
	 * @var Float
	 * @ORM\Column(name="horas_comprometidas", type="float", nullable=true)*
	 */
	private $horasComprometidas;

	/**
	 * @var Float
	 * @ORM\Column(name="horas_realizadas", type="float", nullable=true)*
	 */
	private $horasRealizadas;

	/**
	 * @var Float
	 * @ORM\Column(name="coste", type="float", nullable=true)*
	 */
	private $coste;
	/**
	 * @var text
	 * @ORM\Column(name="solucion_usuario", type="text", nullable=true)*
	 */
	private $solucionUsuario;

	/**
	 * @var text
	 * @ORM\Column(name="solucion_tecnica", type="text", nullable=true)*
	 */
	private $solucionTecnica;

	/**
	 * @var string
	 * @ORM\Column(name="motivo_cancelacion", type="string", length=3, nullable=true)*
	 */
	private $motivoCancelacion;

	/**
	 * @var string
	 * @ORM\Column(name="criticidad", type="string", length=1, nullable=true)*
	 */
	private $criticidad;

	/**
	 * @var boolean
	 * @ORM\Column(name="bloqueado", type="boolean",  nullable=true)*
	 */
	private $bloqueado;

	/**
	 * @var boolean
	 * @ORM\Column(name="penaliza", type="boolean",  nullable=true)*
	 */
	private $penaliza;

    /**
     * @var boolean
     * @ORM\Column(name="incluir_en_informe", type="boolean",  nullable=true)*
     */
    private $incluirEnInforme;

    /**
     * @return bool
     */
    public function isIncluirEnInforme()
    {
        return $this->incluirEnInforme;
    }

    /**
     * @param bool $incluirEnInforme
     */
    public function setIncluirEnInforme($incluirEnInforme)
    {
        $this->incluirEnInforme = $incluirEnInforme;
    }


    public function __toString()
	{
		return $this->numero . ' ' . $this->titulo;
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
	public function getNumero()
	{
		return $this->numero;
	}

	/**
	 * @param int $numero
	 */
	public function setNumero($numero)
	{
		$this->numero = $numero;
	}

	/**
	 * @return Contrato|null
	 */
	public function getContrato()
	{
		return $this->contrato;
	}

	/**
	 * @param Contrato|null $contrato
	 */
	public function setContrato($contrato)
	{
		$this->contrato = $contrato;
	}

	/**
	 * @return Remedy|null
	 */
	public function getRemedy()
	{
		return $this->remedy;
	}

	/**
	 * @param Remedy|null $remedy
	 */
	public function setRemedy($remedy)
	{
		$this->remedy = $remedy;
	}

	/**
	 * @return ObjetoEncargo|null
	 */
	public function getObjetoEncargo()
	{
		return $this->objetoEncargo;
	}

	/**
	 * @param ObjetoEncargo|null $objetoEncargo
	 */
	public function setObjetoEncargo($objetoEncargo)
	{
		$this->objetoEncargo = $objetoEncargo;
	}

	/**
	 * @return Agrupacion|null
	 */
	public function getAgrupacion()
	{
		return $this->agrupacion;
	}

	/**
	 * @param Agrupacion|null $agrupacion
	 */
	public function setAgrupacion($agrupacion)
	{
		$this->agrupacion = $agrupacion;
	}

	/**
	 * @return EstadoEncargo|null
	 */
	public function getEstadoActual()
	{
		return $this->estadoActual;
	}

	/**
	 * @param EstadoEncargo|null $estadoActual
	 */
	public function setEstadoActual($estadoActual)
	{
		$this->estadoActual = $estadoActual;
	}

	/**
	 * @return Aplicacion|null
	 */
	public function getAplicacion()
	{
		return $this->aplicacion;
	}

	/**
	 * @param Aplicacion|null $aplicacion
	 */
	public function setAplicacion($aplicacion)
	{
		$this->aplicacion = $aplicacion;
	}

	/**
	 * @return ModuloFuncional|null
	 */
	public function getModuloFuncional()
	{
		return $this->moduloFuncional;
	}

	/**
	 * @param ModuloFuncional|null $moduloFuncional
	 */
	public function setModuloFuncional($moduloFuncional)
	{
		$this->moduloFuncional = $moduloFuncional;
	}

	/**
	 * @return ModuloTecnico|null
	 */
	public function getModuloTecnico()
	{
		return $this->moduloTecnico;
	}

	/**
	 * @param ModuloTecnico|null $moduloTecnico
	 */
	public function setModuloTecnico($moduloTecnico)
	{
		$this->moduloTecnico = $moduloTecnico;
	}

	/**
	 * @return TipoSolucion|null
	 */
	public function getTipoSolucion()
	{
		return $this->tipoSolucion;
	}

	/**
	 * @param TipoSolucion|null $tipoSolucion
	 */
	public function setTipoSolucion($tipoSolucion)
	{
		$this->tipoSolucion = $tipoSolucion;
	}

	/**
	 * @return Operacional|null
	 */
	public function getOperacional1()
	{
		return $this->operacional1;
	}

	/**
	 * @param Operacional|null $operacional1
	 */
	public function setOperacional1($operacional1)
	{
		$this->operacional1 = $operacional1;
	}

	/**
	 * @return Operacional|null
	 */
	public function getOperacional2()
	{
		return $this->operacional2;
	}

	/**
	 * @param Operacional|null $operacional2
	 */
	public function setOperacional2($operacional2)
	{
		$this->operacional2 = $operacional2;
	}

	/**
	 * @return Operacional|null
	 */
	public function getOperacional3()
	{
		return $this->operacional3;
	}

	/**
	 * @param Operacional|null $operacional3
	 */
	public function setOperacional3($operacional3)
	{
		$this->operacional3 = $operacional3;
	}

	/**
	 * @return string
	 */
	public function getTitulo()
	{
		return $this->titulo;
	}

	/**
	 * @param string $titulo
	 */
	public function setTitulo($titulo)
	{
		$this->titulo = $titulo;
	}

	/**
	 * @return text
	 */
	public function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	 * @param text $descripcion
	 */
	public function setDescripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * @return string
	 */
	public function getNmRemedy()
	{
		return $this->nmRemedy;
	}

	/**
	 * @param string $nmRemedy
	 */
	public function setNmRemedy($nmRemedy)
	{
		$this->nmRemedy = $nmRemedy;
	}

	/**
	 * @return DateTime
	 */
	public function getFcEstadoActual()
	{
		return $this->fcEstadoActual;
	}

	/**
	 * @param DateTime $fcEstadoActual
	 */
	public function setFcEstadoActual($fcEstadoActual)
	{
		$this->fcEstadoActual = $fcEstadoActual;
	}

	/**
	 * @return DateTime
	 */
	public function getFcExportacion()
	{
		return $this->fcExportacion;
	}

	/**
	 * @param DateTime $fcExportacion
	 */
	public function setFcExportacion($fcExportacion)
	{
		$this->fcExportacion = $fcExportacion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcCierre()
	{
		return $this->fcCierre;
	}

	/**
	 * @param DateTime $fcCierre
	 */
	public function setFcCierre($fcCierre)
	{
		$this->fcCierre = $fcCierre;
	}

	/**
	 * @return DateTime
	 */
	public function getFcRegistro()
	{
		return $this->fcRegistro;
	}

	/**
	 * @param DateTime $fcRegistro
	 */
	public function setFcRegistro($fcRegistro)
	{
		$this->fcRegistro = $fcRegistro;
	}

	/**
	 * @return DateTime
	 */
	public function getFcAsignacion()
	{
		return $this->fcAsignacion;
	}

	/**
	 * @param DateTime $fcAsignacion
	 */
	public function setFcAsignacion($fcAsignacion)
	{
		$this->fcAsignacion = $fcAsignacion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcEstimadaSolucion()
	{
		return $this->fcEstimadaSolucion;
	}

	/**
	 * @param DateTime $fcEstimadaSolucion
	 */
	public function setFcEstimadaSolucion($fcEstimadaSolucion)
	{
		$this->fcEstimadaSolucion = $fcEstimadaSolucion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcRequeridaSolucion()
	{
		return $this->fcRequeridaSolucion;
	}

	/**
	 * @param DateTime $fcRequeridaSolucion
	 */
	public function setFcRequeridaSolucion($fcRequeridaSolucion)
	{
		$this->fcRequeridaSolucion = $fcRequeridaSolucion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcRequeridaValoracion()
	{
		return $this->fcRequeridaValoracion;
	}

	/**
	 * @param DateTime $fcRequeridaValoracion
	 */
	public function setFcRequeridaValoracion($fcRequeridaValoracion)
	{
		$this->fcRequeridaValoracion = $fcRequeridaValoracion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcEntregaValoracion()
	{
		return $this->fcEntregaValoracion;
	}

	/**
	 * @param DateTime $fcEntregaValoracion
	 */
	public function setFcEntregaValoracion($fcEntregaValoracion)
	{
		$this->fcEntregaValoracion = $fcEntregaValoracion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcRequeridaEntrega()
	{
		return $this->fcRequeridaEntrega;
	}

	/**
	 * @param DateTime $fcRequeridaEntrega
	 */
	public function setFcRequeridaEntrega($fcRequeridaEntrega)
	{
		$this->fcRequeridaEntrega = $fcRequeridaEntrega;
	}

	/**
	 * @return DateTime
	 */
	public function getFcCompromiso()
	{
		return $this->fcCompromiso;
	}

	/**
	 * @param DateTime $fcCompromiso
	 */
	public function setFcCompromiso($fcCompromiso)
	{
		$this->fcCompromiso = $fcCompromiso;
	}

	/**
	 * @return DateTime
	 */
	public function getFcFinPrevista()
	{
		return $this->fcFinPrevista;
	}

	/**
	 * @param DateTime $fcFinPrevista
	 */
	public function setFcFinPrevista($fcFinPrevista)
	{
		$this->fcFinPrevista = $fcFinPrevista;
	}

	/**
	 * @return DateTime
	 */
	public function getFcComienzoEjecucion()
	{
		return $this->fcComienzoEjecucion;
	}

	/**
	 * @param DateTime $fcComienzoEjecucion
	 */
	public function setFcComienzoEjecucion($fcComienzoEjecucion)
	{
		$this->fcComienzoEjecucion = $fcComienzoEjecucion;
	}

	/**
	 * @return DateTime
	 */
	public function getFcEntrega()
	{
		return $this->fcEntrega;
	}

	/**
	 * @param DateTime $fcEntrega
	 */
	public function setFcEntrega($fcEntrega)
	{
		$this->fcEntrega = $fcEntrega;
	}

	/**
	 * @return DateTime
	 */
	public function getFcResolucionIcm()
	{
		return $this->fcResolucionIcm;
	}

	/**
	 * @param DateTime $fcResolucionIcm
	 */
	public function setFcResolucionIcm($fcResolucionIcm)
	{
		$this->fcResolucionIcm = $fcResolucionIcm;
	}

	/**
	 * @return DateTime
	 */
	public function getFcAceptacion()
	{
		return $this->fcAceptacion;
	}

	/**
	 * @param DateTime $fcAceptacion
	 */
	public function setFcAceptacion($fcAceptacion)
	{
		$this->fcAceptacion = $fcAceptacion;
	}

	/**
	 * @return Float
	 */
	public function getTiempoTotal()
	{
		return $this->tiempoTotal;
	}

	/**
	 * @param Float $tiempoTotal
	 */
	public function setTiempoTotal($tiempoTotal)
	{
		$this->tiempoTotal = $tiempoTotal;
	}

	/**
	 * @return Float
	 */
	public function getTiempoResolucion()
	{
		return $this->tiempoResolucion;
	}

	/**
	 * @param Float $tiempoResolucion
	 */
	public function setTiempoResolucion($tiempoResolucion)
	{
		$this->tiempoResolucion = $tiempoResolucion;
	}

	/**
	 * @return Float
	 */
	public function getHorasValoradas()
	{
		return $this->horasValoradas;
	}

	/**
	 * @param Float $horasValoradas
	 */
	public function setHorasValoradas($horasValoradas)
	{
		$this->horasValoradas = $horasValoradas;
	}

	/**
	 * @return Float
	 */
	public function getHorasComprometidas()
	{
		return $this->horasComprometidas;
	}

	/**
	 * @param Float $horasComprometidas
	 */
	public function setHorasComprometidas($horasComprometidas)
	{
		$this->horasComprometidas = $horasComprometidas;
	}

	/**
	 * @return Float
	 */
	public function getHorasRealizadas()
	{
		return $this->horasRealizadas;
	}

	/**
	 * @param Float $horasRealizadas
	 */
	public function setHorasRealizadas($horasRealizadas)
	{
		$this->horasRealizadas = $horasRealizadas;
	}

	/**
	 * @return Float
	 */
	public function getCoste()
	{
		return $this->coste;
	}

	/**
	 * @param Float $coste
	 */
	public function setCoste($coste)
	{
		$this->coste = $coste;
	}

	/**
	 * @return text
	 */
	public function getSolucionUsuario()
	{
		return $this->solucionUsuario;
	}

	/**
	 * @param text $solucionUsuario
	 */
	public function setSolucionUsuario($solucionUsuario)
	{
		$this->solucionUsuario = $solucionUsuario;
	}

	/**
	 * @return text
	 */
	public function getSolucionTecnica()
	{
		return $this->solucionTecnica;
	}

	/**
	 * @param text $solucionTecnica
	 */
	public function setSolucionTecnica($solucionTecnica)
	{
		$this->solucionTecnica = $solucionTecnica;
	}

	/**
	 * @return string
	 */
	public function getMotivoCancelacion()
	{
		return $this->motivoCancelacion;
	}

	/**
	 * @param string $motivoCancelacion
	 */
	public function setMotivoCancelacion($motivoCancelacion)
	{
		$this->motivoCancelacion = $motivoCancelacion;
	}

	/**
	 * @return string
	 */
	public function getCriticidad()
	{
		return $this->criticidad;
	}

	/**
	 * @param string $criticidad
	 */
	public function setCriticidad($criticidad)
	{
		$this->criticidad = $criticidad;
	}

	/**
	 * @return bool
	 */
	public function isBloqueado()
	{
		return $this->bloqueado;
	}

	/**
	 * @param bool $bloqueado
	 */
	public function setBloqueado($bloqueado)
	{
		$this->bloqueado = $bloqueado;
	}

	/**
	 * @return bool
	 */
	public function isPenaliza()
	{
		return $this->penaliza;
	}

	/**
	 * @param bool $penaliza
	 */
	public function setPenaliza($penaliza)
	{
		$this->penaliza = $penaliza;
	}

	/**
	 * @return Criticidad|null
	 */
	public function getCriticidad2()
	{
		return $this->criticidad2;
	}

	/**
	 * @param Criticidad|null $criticidad2
	 */
	public function setCriticidad2($criticidad2)
	{
		$this->criticidad2 = $criticidad2;
	}



}
