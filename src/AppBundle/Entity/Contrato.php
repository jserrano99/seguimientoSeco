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
 * @ORM\Table(name="contrato")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ContratoRepository")
 */

class Contrato
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
	 * @ORM\Column(name="codigo", type="string", length=15, nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fc_inicio", type="datetime", nullable=false)
	 */
	private $fcInicio;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fc_fin", type="datetime", nullable=false)
	 */
	private $fcFin;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="centro_coste_cd", type="integer", nullable=true)
	 */
	private $centroCosteCd;

	/**
	 * @var  string
	 *
	 * @ORM\Column(name="centro_coste_ds", type="string", length=255, nullable=true)
	 */
	private $centroCosteDs;

	/**
	 * @var  string
	 *
	 * @ORM\Column(name="expediente", type="string", length=255,nullable=true)
	 */
	private $expediente;

	/**
	 * @var  string
	 *
	 * @ORM\Column(name="adjudicatario", type="string", length=255,nullable=true)
	 */
	private $adjudicatario;

	/**
	 * @var  float
	 *
	 * @ORM\Column(name="importe_contrato", type="float", nullable=true)
	 */
	private $importeContrato;

	/**
	 * @var  float
	 *
	 * @ORM\Column(name="importe_adjudicacion", type="float", nullable=true)
	 */
	private $importeAdjudicacion;

	/**
	 * @var  float
	 *
	 * @ORM\Column(name="porcentaje_baja", type="float", nullable=true)
	 */
	private $porcentajeBaja;

	/**
	 * @var  integer
	 *
	 * @ORM\Column(name="numero_pedido", type="integer", nullable=true)
	 */
	private $numeroPedido;


	public function setId($id) {
		$this->id = $id;

		return $this;
	}
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
     * @param string $codigo
     *
     * @return Contrato
     */
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;

        return $this;
    }

    /**
     * Get codigo.
     *
     * @return string
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
     * @return Contrato
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
     * Set fcInicio.
     *
     * @param \DateTime $fcInicio
     *
     * @return Contrato
     */
    public function setFcInicio($fcInicio)
    {
        $this->fcInicio = $fcInicio;

        return $this;
    }

    /**
     * Get fcInicio.
     *
     * @return \DateTime
     */
    public function getFcInicio()
    {
        return $this->fcInicio;
    }

    /**
     * Set fcFin.
     *
     * @param \DateTime $fcFin
     *
     * @return Contrato
     */
    public function setFcFin($fcFin)
    {
        $this->fcFin = $fcFin;

        return $this;
    }

    /**
     * Get fcFin.
     *
     * @return \DateTime
     */
    public function getFcFin()
    {
        return $this->fcFin;
    }

    /**
     * Set centroCosteCd.
     *
     * @param int|null $centroCosteCd
     *
     * @return Contrato
     */
    public function setCentroCosteCd($centroCosteCd = null)
    {
        $this->centroCosteCd = $centroCosteCd;

        return $this;
    }

    /**
     * Get centroCosteCd.
     *
     * @return int|null
     */
    public function getCentroCosteCd()
    {
        return $this->centroCosteCd;
    }

    /**
     * Set centroCosteDs.
     *
     * @param string|null $centroCosteDs
     *
     * @return Contrato
     */
    public function setCentroCosteDs($centroCosteDs = null)
    {
        $this->centroCosteDs = $centroCosteDs;

        return $this;
    }

    /**
     * Get centroCosteDs.
     *
     * @return string|null
     */
    public function getCentroCosteDs()
    {
        return $this->centroCosteDs;
    }

    /**
     * Set expediente.
     *
     * @param string|null $expediente
     *
     * @return Contrato
     */
    public function setExpediente($expediente = null)
    {
        $this->expediente = $expediente;

        return $this;
    }

    /**
     * Get expediente.
     *
     * @return string|null
     */
    public function getExpediente()
    {
        return $this->expediente;
    }

    /**
     * Set adjudicatario.
     *
     * @param string|null $adjudicatario
     *
     * @return Contrato
     */
    public function setAdjudicatario($adjudicatario = null)
    {
        $this->adjudicatario = $adjudicatario;

        return $this;
    }

    /**
     * Get adjudicatario.
     *
     * @return string|null
     */
    public function getAdjudicatario()
    {
        return $this->adjudicatario;
    }

    /**
     * Set importeContrato.
     *
     * @param float|null $importeContrato
     *
     * @return Contrato
     */
    public function setImporteContrato($importeContrato = null)
    {
        $this->importeContrato = $importeContrato;

        return $this;
    }

    /**
     * Get importeContrato.
     *
     * @return float|null
     */
    public function getImporteContrato()
    {
        return $this->importeContrato;
    }

    /**
     * Set importeAdjudicacion.
     *
     * @param float|null $importeAdjudicacion
     *
     * @return Contrato
     */
    public function setImporteAdjudicacion($importeAdjudicacion = null)
    {
        $this->importeAdjudicacion = $importeAdjudicacion;

        return $this;
    }

    /**
     * Get importeAdjudicacion.
     *
     * @return float|null
     */
    public function getImporteAdjudicacion()
    {
        return $this->importeAdjudicacion;
    }

    /**
     * Set porcentajeBaja.
     *
     * @param float|null $porcentajeBaja
     *
     * @return Contrato
     */
    public function setPorcentajeBaja($porcentajeBaja = null)
    {
        $this->porcentajeBaja = $porcentajeBaja;

        return $this;
    }

    /**
     * Get porcentajeBaja.
     *
     * @return float|null
     */
    public function getPorcentajeBaja()
    {
        return $this->porcentajeBaja;
    }

    /**
     * Set numeroPedido.
     *
     * @param int|null $numeroPedido
     *
     * @return Contrato
     */
    public function setNumeroPedido($numeroPedido = null)
    {
        $this->numeroPedido = $numeroPedido;

        return $this;
    }

    /**
     * Get numeroPedido.
     *
     * @return int|null
     */
    public function getNumeroPedido()
    {
        return $this->numeroPedido;
    }

    public  function __toString()
	{
		return $this->codigo.'-'.$this->descripcion;
	}
}
