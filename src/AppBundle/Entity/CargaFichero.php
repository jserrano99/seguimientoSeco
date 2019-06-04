<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CargaFichero
 * @ORM\Table(name="carga_fichero")
 * @ORM\Entity
 */
class CargaFichero {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \Datetime
     *
     * @ORM\Column(name="fecha_carga", type="datetime", nullable=true)
     */
    private $fechaCarga;

    /**
     * @var string
     *
     * @ORM\Column(name="fichero", type="string", length=255, nullable=false)
     */
    private $fichero;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;


    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

	/**
	 * @var \AppBundle\Entity\CargaFicheroLog
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\CargaFicheroLog", cascade={"persist"})
	 * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="carga_fichero_log_id", referencedColumnName="id")
	 * })
	 */
	private $cargaFicheroLog;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="numero_registros", type="integer",  nullable=true)
	 */
	private $numeroRegistros;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="numero_registros_cargados", type="integer",  nullable=true)
	 */
	private $numeroRegistrosCargados;


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
     * Set fechaCarga.
     *
     * @param \DateTime|null $fechaCarga
     *
     * @return CargaFichero
     */
    public function setFechaCarga($fechaCarga = null)
    {
        $this->fechaCarga = $fechaCarga;

        return $this;
    }

    /**
     * Get fechaCarga.
     *
     * @return \DateTime|null
     */
    public function getFechaCarga()
    {
        return $this->fechaCarga;
    }

    /**
     * Set fichero.
     *
     * @param string $fichero
     *
     * @return CargaFichero
     */
    public function setFichero($fichero)
    {
        $this->fichero = $fichero;

        return $this;
    }

    /**
     * Get fichero.
     *
     * @return string
     */
    public function getFichero()
    {
        return $this->fichero;
    }

    /**
     * Set descripcion.
     *
     * @param string $descripcion
     *
     * @return CargaFichero
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
     * Set usuario.
     *
     * @param \AppBundle\Entity\Usuario|null $usuario
     *
     * @return CargaFichero
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario.
     *
     * @return \AppBundle\Entity\Usuario|null
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set cargaFicheroLog.
     *
     * @param \AppBundle\Entity\CargaFicheroLog|null $cargaFicheroLog
     *
     * @return CargaFichero
     */
    public function setCargaFicheroLog(\AppBundle\Entity\CargaFicheroLog $cargaFicheroLog = null)
    {
        $this->cargaFicheroLog = $cargaFicheroLog;

        return $this;
    }

    /**
     * Get cargaFicheroLog.
     *
     * @return \AppBundle\Entity\CargaFicheroLog|null
     */
    public function getCargaFicheroLog()
    {
        return $this->cargaFicheroLog;
    }

    /**
     * Set numeroRegistros.
     *
     * @param int|null $numeroRegistros
     *
     * @return CargaFichero
     */
    public function setNumeroRegistros($numeroRegistros = null)
    {
        $this->numeroRegistros = $numeroRegistros;

        return $this;
    }

    /**
     * Get numeroRegistros.
     *
     * @return int|null
     */
    public function getNumeroRegistros()
    {
        return $this->numeroRegistros;
    }

    /**
     * Set numeroRegistrosCargados.
     *
     * @param int|null $numeroRegistrosCargados
     *
     * @return CargaFichero
     */
    public function setNumeroRegistrosCargados($numeroRegistrosCargados = null)
    {
        $this->numeroRegistrosCargados = $numeroRegistrosCargados;

        return $this;
    }

    /**
     * Get numeroRegistrosCargados.
     *
     * @return int|null
     */
    public function getNumeroRegistrosCargados()
    {
        return $this->numeroRegistrosCargados;
    }
}
