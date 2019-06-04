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
 * @ORM\Table(name="agrupacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AgrupacionRepository")
 *
 */

class Agrupacion
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
	 * @var TipoAgrupacion\null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoAgrupacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_agrupacion_id", referencedColumnName="id")
	 * })
	 */
	private $tipoAgrupacion;


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
     * @return Agrupacion
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
     * @return Agrupacion
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
     * @return Agrupacion
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
     * @return Agrupacion
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
     * Set tipoAgrupacion.
     *
     * @param \AppBundle\Entity\TipoAgrupacion|null $tipoAgrupacion
     *
     * @return Agrupacion
     */
    public function setTipoAgrupacion(\AppBundle\Entity\TipoAgrupacion $tipoAgrupacion = null)
    {
        $this->tipoAgrupacion = $tipoAgrupacion;

        return $this;
    }

    /**
     * Get tipoAgrupacion.
     *
     * @return \AppBundle\Entity\TipoAgrupacion|null
     */
    public function getTipoAgrupacion()
    {
        return $this->tipoAgrupacion;
    }
}
