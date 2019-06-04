<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Mes
 *
 * @ORM\Table(name="meses",
 *           )
 * @ORM\Entity
 */
class Mes {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var \AppBundle\Entity\Anyo
	 * @ORM\ManyToOne(targetEntity="Anyo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="anyo_id", referencedColumnName="id")
	 * })
	 */
	private $anyo;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_inicio", type="datetime", nullable=false)
	 */
	private $fechaInicio;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha_fin", type="datetime", nullable=false)
	 */
	private $fechaFin;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=25, nullable=false)
	 */
	private $descripcion;

	public function __toString()
	{
		return $this->descripcion;
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
     * Set fechaInicio.
     *
     * @param \DateTime $fechaInicio
     *
     * @return Mes
     */
    public function setFechaInicio($fechaInicio)
    {
        $this->fechaInicio = $fechaInicio;

        return $this;
    }

    /**
     * Get fechaInicio.
     *
     * @return \DateTime
     */
    public function getFechaInicio()
    {
        return $this->fechaInicio;
    }

    /**
     * Set fechaFin.
     *
     * @param \DateTime $fechaFin
     *
     * @return Mes
     */
    public function setFechaFin($fechaFin)
    {
        $this->fechaFin = $fechaFin;

        return $this;
    }

    /**
     * Get fechaFin.
     *
     * @return \DateTime
     */
    public function getFechaFin()
    {
        return $this->fechaFin;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return Mes
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
     * Set anyo.
     *
     * @param \AppBundle\Entity\Anyo|null $anyo
     *
     * @return Mes
     */
    public function setAnyo(\AppBundle\Entity\Anyo $anyo = null)
    {
        $this->anyo = $anyo;

        return $this;
    }

    /**
     * Get anyo.
     *
     * @return \AppBundle\Entity\Anyo|null
     */
    public function getAnyo()
    {
        return $this->anyo;
    }
}
