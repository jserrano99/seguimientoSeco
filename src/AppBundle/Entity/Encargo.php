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
	 * @var \AppBundle\Entity\Contrato|null
	 * @ORM\ManyToOne(targetEntity="Contrato")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="contrato_id", referencedColumnName="id")
	 * })
	 */
	private $contrato;

	/**
	 * @var \AppBundle\Entity\Remedy|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Remedy")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="remedy_id", referencedColumnName="id")
	 * })
	 */
	private $remedy;

	/**
	 * @var \AppBundle\Entity\ObjetoEncargo|null
	 * @ORM\ManyToOne(targetEntity="ObjetoEncargo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="objeto_encargo_id", referencedColumnName="id")
	 * })
	 */
	private $objetoEncargo;

	/**
	 * @var \AppBundle\Entity\Agrupacion|null
	 * @ORM\ManyToOne(targetEntity="Agrupacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="agrupacion_id", referencedColumnName="id")
	 * })
	 */
	private $agrupacion;

	/**
	 * @var \AppBundle\Entity\EstadoEncargo|null
	 * @ORM\ManyToOne(targetEntity="EstadoEncargo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="estado_actual_id", referencedColumnName="id")
	 * })
	 */
	private $estadoActual;

	/**
	 * @var \AppBundle\Entity\Aplicacion|null
	 * @ORM\ManyToOne(targetEntity="Aplicacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="aplicacion_id", referencedColumnName="id")
	 * })
	 */
	private $aplicacion;

	/**
	 * @var \AppBundle\Entity\ModuloFuncional|null
	 * @ORM\ManyToOne(targetEntity="ModuloFuncional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="modulo_funcional_id", referencedColumnName="id")
	 * })
	 */
	private $moduloFuncional;

	/**
	 * @var \AppBundle\Entity\ModuloTecnico|null
	 * @ORM\ManyToOne(targetEntity="ModuloTecnico")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="modulo_tecnico_id", referencedColumnName="id")
	 * })
	 */
	private $moduloTecnico;

	/**
	 * @var \AppBundle\Entity\TipoSolucion|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoSolucion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_solucion_id", referencedColumnName="id")
	 * })
	 */
	private $tipoSolucion;

	/**
	 * @var \AppBundle\Entity\Operacional|null
	 * @ORM\ManyToOne(targetEntity="Operacional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="operacional1_id", referencedColumnName="id")
	 * })
	 */
	private $operacional1;

	/**
	 * @var \AppBundle\Entity\Operacional|null
	 * @ORM\ManyToOne(targetEntity="Operacional")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="operacional2_id", referencedColumnName="id")
	 * })
	 */
	private $operacional2;
	/**
	 * @var \AppBundle\Entity\Operacional|null
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
	 * Get id.
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set numero.
	 *
	 * @param int $numero
	 * @return Encargo
	 */
	public function setNumero($numero)
	{
		$this->numero = $numero;

		return $this;
	}

	/**
	 * Get numero.
	 *
	 * @return int
	 */
	public function getNumero()
	{
		return $this->numero;
	}

	/**
	 * Set titulo.
	 *
	 * @param string $titulo
	 * @return Encargo
	 */
	public function setTitulo($titulo)
	{
		$this->titulo = $titulo;

		return $this;
	}

	/**
	 * Get titulo.
	 *
	 * @return string
	 */
	public function getTitulo()
	{
		return $this->titulo;
	}

	/**
	 * Set nmRemedy.
	 *
	 * @param string $nmRemedy
	 * @return Encargo
	 */
	public function setNmRemedy($nmRemedy)
	{
		$this->nmRemedy = $nmRemedy;

		return $this;
	}

	/**
	 * Get nmRemedy.
	 *
	 * @return string
	 */
	public function getNmRemedy()
	{
		return $this->nmRemedy;
	}

	/**
	 * Set fcEstadoActual.
	 *
	 * @param \DateTime $fcEstadoActual
	 * @return Encargo
	 */
	public function setFcEstadoActual($fcEstadoActual)
	{
		$this->fcEstadoActual = $fcEstadoActual;

		return $this;
	}

	/**
	 * Get fcEstadoActual.
	 *
	 * @return \DateTime
	 */
	public function getFcEstadoActual()
	{
		return $this->fcEstadoActual;
	}

	/**
	 * Set fcRegistro.
	 *
	 * @param \DateTime $fcRegistro
	 * @return Encargo
	 */
	public function setFcRegistro($fcRegistro)
	{
		$this->fcRegistro = $fcRegistro;

		return $this;
	}

	/**
	 * Get fcRegistro.
	 *
	 * @return \DateTime
	 */
	public function getFcRegistro()
	{
		return $this->fcRegistro;
	}

	/**
	 * Set fcAsignacion.
	 *
	 * @param \DateTime|null $fcAsignacion
	 * @return Encargo
	 */
	public function setFcAsignacion($fcAsignacion = null)
	{
		$this->fcAsignacion = $fcAsignacion;

		return $this;
	}

	/**
	 * Get fcAsignacion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcAsignacion()
	{
		return $this->fcAsignacion;
	}

	/**
	 * Set fcEstimadaSolucion.
	 *
	 * @param \DateTime|null $fcEstimadaSolucion
	 * @return Encargo
	 */
	public function setFcEstimadaSolucion($fcEstimadaSolucion = null)
	{
		$this->fcEstimadaSolucion = $fcEstimadaSolucion;

		return $this;
	}

	/**
	 * Get fcEstimadaSolucion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcEstimadaSolucion()
	{
		return $this->fcEstimadaSolucion;
	}

	/**
	 * Set fcRequeridaSolucion.
	 *
	 * @param \DateTime|null $fcRequeridaSolucion
	 * @return Encargo
	 */
	public function setFcRequeridaSolucion($fcRequeridaSolucion = null)
	{
		$this->fcRequeridaSolucion = $fcRequeridaSolucion;

		return $this;
	}

	/**
	 * Get fcRequeridaSolucion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcRequeridaSolucion()
	{
		return $this->fcRequeridaSolucion;
	}

	/**
	 * Set fcRequeridaValoracion.
	 *
	 * @param \DateTime|null $fcRequeridaValoracion
	 * @return Encargo
	 */
	public function setFcRequeridaValoracion($fcRequeridaValoracion = null)
	{
		$this->fcRequeridaValoracion = $fcRequeridaValoracion;

		return $this;
	}

	/**
	 * Get fcRequeridaValoracion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcRequeridaValoracion()
	{
		return $this->fcRequeridaValoracion;
	}

	/**
	 * Set fcEntregaValoracion.
	 *
	 * @param \DateTime|null $fcEntregaValoracion
	 * @return Encargo
	 */
	public function setFcEntregaValoracion($fcEntregaValoracion = null)
	{
		$this->fcEntregaValoracion = $fcEntregaValoracion;

		return $this;
	}

	/**
	 * Get fcEntregaValoracion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcEntregaValoracion()
	{
		return $this->fcEntregaValoracion;
	}

	/**
	 * Set fcRequeridaEntrega.
	 *
	 * @param \DateTime|null $fcRequeridaEntrega
	 * @return Encargo
	 */
	public function setFcRequeridaEntrega($fcRequeridaEntrega = null)
	{
		$this->fcRequeridaEntrega = $fcRequeridaEntrega;

		return $this;
	}

	/**
	 * Get fcRequeridaEntrega.
	 *
	 * @return \DateTime|null
	 */
	public function getFcRequeridaEntrega()
	{
		return $this->fcRequeridaEntrega;
	}

	/**
	 * Set fcCompromiso.
	 *
	 * @param \DateTime|null $fcCompromiso
	 * @return Encargo
	 */
	public function setFcCompromiso($fcCompromiso = null)
	{
		$this->fcCompromiso = $fcCompromiso;

		return $this;
	}

	/**
	 * Get fcCompromiso.
	 *
	 * @return \DateTime|null
	 */
	public function getFcCompromiso()
	{
		return $this->fcCompromiso;
	}

	/**
	 * Set fcFinPrevista.
	 *
	 * @param \DateTime|null $fcFinPrevista
	 * @return Encargo
	 */
	public function setFcFinPrevista($fcFinPrevista = null)
	{
		$this->fcFinPrevista = $fcFinPrevista;

		return $this;
	}

	/**
	 * Get fcFinPrevista.
	 *
	 * @return \DateTime|null
	 */
	public function getFcFinPrevista()
	{
		return $this->fcFinPrevista;
	}

	/**
	 * Set fcComienzoEjecucion.
	 *
	 * @param \DateTime|null $fcComienzoEjecucion
	 * @return Encargo
	 */
	public function setFcComienzoEjecucion($fcComienzoEjecucion = null)
	{
		$this->fcComienzoEjecucion = $fcComienzoEjecucion;

		return $this;
	}

	/**
	 * Get fcComienzoEjecucion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcComienzoEjecucion()
	{
		return $this->fcComienzoEjecucion;
	}

	/**
	 * Set fcEntrega.
	 *
	 * @param \DateTime|null $fcEntrega
	 * @return Encargo
	 */
	public function setFcEntrega($fcEntrega = null)
	{
		$this->fcEntrega = $fcEntrega;

		return $this;
	}

	/**
	 * Get fcEntrega.
	 *
	 * @return \DateTime|null
	 */
	public function getFcEntrega()
	{
		return $this->fcEntrega;
	}

	/**
	 * Set fcResolucionIcm.
	 *
	 * @param \DateTime|null $fcResolucionIcm
	 * @return Encargo
	 */
	public function setFcResolucionIcm($fcResolucionIcm = null)
	{
		$this->fcResolucionIcm = $fcResolucionIcm;

		return $this;
	}

	/**
	 * Get fcResolucionIcm.
	 *
	 * @return \DateTime|null
	 */
	public function getFcResolucionIcm()
	{
		return $this->fcResolucionIcm;
	}

	/**
	 * Set fcAceptacion.
	 *
	 * @param \DateTime|null $fcAceptacion
	 * @return Encargo
	 */
	public function setFcAceptacion($fcAceptacion = null)
	{
		$this->fcAceptacion = $fcAceptacion;

		return $this;
	}

	/**
	 * Get fcAceptacion.
	 *
	 * @return \DateTime|null
	 */
	public function getFcAceptacion()
	{
		return $this->fcAceptacion;
	}

	/**
	 * Set fcCierre.
	 *
	 * @param \DateTime|null $fcCierre
	 * @return Encargo
	 */
	public function setFcCierre($fcCierre = null)
	{
		$this->fcCierre = $fcCierre;

		return $this;
	}

	/**
	 * Get fcCierre.
	 *
	 * @return \DateTime|null
	 */
	public function getFcCierre()
	{
		return $this->fcCierre;
	}

	/**
	 * Set tiempoTotal.
	 *
	 * @param string|null $tiempoTotal
	 * @return Encargo
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
	 * @return Encargo
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
	 * Set contrato.
	 *
	 * @param \AppBundle\Entity\Contrato|null $contrato
	 * @return Encargo
	 */
	public function setContrato(Contrato $contrato = null)
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
	 * Set objetoEncargo.
	 *
	 * @param \AppBundle\Entity\ObjetoEncargo|null $objetoEncargo
	 * @return Encargo
	 */
	public function setObjetoEncargo(ObjetoEncargo $objetoEncargo = null)
	{
		$this->objetoEncargo = $objetoEncargo;

		return $this;
	}

	/**
	 * Get objetoEncargo.
	 *
	 * @return \AppBundle\Entity\ObjetoEncargo|null
	 */
	public function getObjetoEncargo()
	{
		return $this->objetoEncargo;
	}

	/**
	 * Set agrupacion.
	 *
	 * @param \AppBundle\Entity\Agrupacion|null $agrupacion
	 * @return Encargo
	 */
	public function setAgrupacion(\AppBundle\Entity\Agrupacion $agrupacion = null)
	{
		$this->agrupacion = $agrupacion;

		return $this;
	}

	/**
	 * Get agrupacion.
	 *
	 * @return \AppBundle\Entity\Agrupacion|null
	 */
	public function getAgrupacion()
	{
		return $this->agrupacion;
	}

	/**
	 * Set estadoActual.
	 *
	 * @param \AppBundle\Entity\EstadoEncargo|null $estadoActual
	 * @return Encargo
	 */
	public function setEstadoActual(\AppBundle\Entity\EstadoEncargo $estadoActual = null)
	{
		$this->estadoActual = $estadoActual;

		return $this;
	}

	/**
	 * Get estadoActual.
	 *
	 * @return \AppBundle\Entity\EstadoEncargo|null
	 */
	public function getEstadoActual()
	{
		return $this->estadoActual;
	}

	/**
	 * Set moduloFuncional.
	 *
	 * @param \AppBundle\Entity\ModuloFuncional|null $moduloFuncional
	 * @return Encargo
	 */
	public function setModuloFuncional(\AppBundle\Entity\ModuloFuncional $moduloFuncional = null)
	{
		$this->moduloFuncional = $moduloFuncional;

		return $this;
	}

	/**
	 * Get moduloFuncional.
	 *
	 * @return \AppBundle\Entity\ModuloFuncional|null
	 */
	public function getModuloFuncional()
	{
		return $this->moduloFuncional;
	}

	/**
	 * Set moduloTecnico.
	 *
	 * @param \AppBundle\Entity\ModuloTecnico|null $moduloTecnico
	 * @return Encargo
	 */
	public function setModuloTecnico(\AppBundle\Entity\ModuloTecnico $moduloTecnico = null)
	{
		$this->moduloTecnico = $moduloTecnico;

		return $this;
	}

	/**
	 * Get moduloTecnico.
	 *
	 * @return \AppBundle\Entity\ModuloTecnico|null
	 */
	public function getModuloTecnico()
	{
		return $this->moduloTecnico;
	}

	/**
	 * Set descripcion.
	 *
	 * @param string $descripcion
	 * @return Encargo
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
	 * Set horasValoradas.
	 *
	 * @param string|null $horasValoradas
	 * @return Encargo
	 */
	public function setHorasValoradas($horasValoradas = null)
	{
		$this->horasValoradas = $horasValoradas;

		return $this;
	}

	/**
	 * Get horasValoradas.
	 *
	 * @return string|null
	 */
	public function getHorasValoradas()
	{
		return $this->horasValoradas;
	}

	/**
	 * Set horasComprometidas.
	 *
	 * @param string|null $horasComprometidas
	 * @return Encargo
	 */
	public function setHorasComprometidas($horasComprometidas = null)
	{
		$this->horasComprometidas = $horasComprometidas;

		return $this;
	}

	/**
	 * Get horasComprometidas.
	 *
	 * @return string|null
	 */
	public function getHorasComprometidas()
	{
		return $this->horasComprometidas;
	}

	/**
	 * Set horasRealizadas.
	 *
	 * @param string|null $horasRealizadas
	 * @return Encargo
	 */
	public function setHorasRealizadas($horasRealizadas = null)
	{
		$this->horasRealizadas = $horasRealizadas;

		return $this;
	}

	/**
	 * Get horasRealizadas.
	 *
	 * @return string|null
	 */
	public function getHorasRealizadas()
	{
		return $this->horasRealizadas;
	}

	/**
	 * Set coste.
	 *
	 * @param string|null $coste
	 * @return Encargo
	 */
	public function setCoste($coste = null)
	{
		$this->coste = $coste;

		return $this;
	}

	/**
	 * Get coste.
	 *
	 * @return string|null
	 */
	public function getCoste()
	{
		return $this->coste;
	}

	/**
	 * Set fcExportacion.
	 *
	 * @param \DateTime $fcExportacion
	 * @return Encargo
	 */
	public function setFcExportacion($fcExportacion)
	{
		$this->fcExportacion = $fcExportacion;

		return $this;
	}

	/**
	 * Get fcExportacion.
	 *
	 * @return \DateTime
	 */
	public function getFcExportacion()
	{
		return $this->fcExportacion;
	}

	/**
	 * Set operacional1.
	 *
	 * @param \AppBundle\Entity\Operacional|null $operacional1
	 * @return Encargo
	 */
	public function setOperacional1(\AppBundle\Entity\Operacional $operacional1 = null)
	{
		$this->operacional1 = $operacional1;

		return $this;
	}

	/**
	 * Get operacional1.
	 *
	 * @return \AppBundle\Entity\Operacional|null
	 */
	public function getOperacional1()
	{
		return $this->operacional1;
	}

	/**
	 * Set operacional2.
	 *
	 * @param \AppBundle\Entity\Operacional|null $operacional2
	 * @return Encargo
	 */
	public function setOperacional2(\AppBundle\Entity\Operacional $operacional2 = null)
	{
		$this->operacional2 = $operacional2;

		return $this;
	}

	/**
	 * Get operacional2.
	 *
	 * @return \AppBundle\Entity\Operacional|null
	 */
	public function getOperacional2()
	{
		return $this->operacional2;
	}

	/**
	 * Set operacional3.
	 *
	 * @param \AppBundle\Entity\Operacional|null $operacional3
	 * @return Encargo
	 */
	public function setOperacional3(\AppBundle\Entity\Operacional $operacional3 = null)
	{
		$this->operacional3 = $operacional3;

		return $this;
	}

	/**
	 * Get operacional3.
	 *
	 * @return \AppBundle\Entity\Operacional|null
	 */
	public function getOperacional3()
	{
		return $this->operacional3;
	}

	/**
	 * Set aplicacion.
	 *
	 * @param \AppBundle\Entity\Aplicacion|null $aplicacion
	 * @return Encargo
	 */
	public function setAplicacion(Aplicacion $aplicacion = null)
	{
		$this->aplicacion = $aplicacion;

		return $this;
	}

	/**
	 * Get aplicacion.
	 *
	 * @return \AppBundle\Entity\Aplicacion|null
	 */
	public function getAplicacion()
	{
		return $this->aplicacion;
	}

	/**
	 * Set tipoSolucion.
	 *
	 * @param \AppBundle\Entity\TipoSolucion|null $tipoSolucion
	 * @return Encargo
	 */
	public function setTipoSolucion(TipoSolucion $tipoSolucion = null)
	{
		$this->tipoSolucion = $tipoSolucion;

		return $this;
	}

	/**
	 * Get tipoSolucion.
	 *
	 * @return \AppBundle\Entity\TipoSolucion|null
	 */
	public function getTipoSolucion()
	{
		return $this->tipoSolucion;
	}

	/**
	 * Set solucionUsuario.
	 *
	 * @param string|null $solucionUsuario
	 * @return Encargo
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
	 * @return Encargo
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
     * Set motivoCancelacion.
     *
     * @param string|null $motivoCancelacion
     *
     * @return Encargo
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
     * Set criticidad.
     *
     * @param string|null $criticidad
     *
     * @return Encargo
     */
    public function setCriticidad($criticidad = null)
    {
        $this->criticidad = $criticidad;

        return $this;
    }

    /**
     * Get criticidad.
     *
     * @return string|null
     */
    public function getCriticidad()
    {
        return $this->criticidad;
    }

    /**
     * Set bloqueado.
     *
     * @param bool|null $bloqueado
     *
     * @return Encargo
     */
    public function setBloqueado($bloqueado = null)
    {
        $this->bloqueado = $bloqueado;

        return $this;
    }

    /**
     * Get bloqueado.
     *
     * @return bool|null
     */
    public function getBloqueado()
    {
        return $this->bloqueado;
    }

    /**
     * Set penaliza.
     *
     * @param bool|null $penaliza
     *
     * @return Encargo
     */
    public function setPenaliza($penaliza = null)
    {
        $this->penaliza = $penaliza;

        return $this;
    }

    /**
     * Get penaliza.
     *
     * @return bool|null
     */
    public function getPenaliza()
    {
        return $this->penaliza;
    }

    /**
     * Set remedy.
     *
     * @param \AppBundle\Entity\Remedy|null $remedy
     *
     * @return Encargo
     */
    public function setRemedy(\AppBundle\Entity\Remedy $remedy = null)
    {
        $this->remedy = $remedy;

        return $this;
    }

    /**
     * Get remedy.
     *
     * @return \AppBundle\Entity\Remedy|null
     */
    public function getRemedy()
    {
        return $this->remedy;
    }
}
