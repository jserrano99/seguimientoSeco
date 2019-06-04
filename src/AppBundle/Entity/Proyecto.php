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
 * @ORM\Table(name="proyecto")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProyectoRepository")
 *
 */

class Proyecto
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
     * @return Proyecto
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
     * @return Proyecto
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
     * @param \DateTime|null $fcInicio
     *
     * @return Proyecto
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
     * @return Proyecto
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
}
