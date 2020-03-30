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
 * @ORM\Table(name="estado_encargo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EstadoEncargoRepository")
 */

class EstadoEncargo
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
	 * @ORM\Column(name="codigo", type="string", length=3,nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var TipoEstado|null
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\TipoEstado")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_estado_id", referencedColumnName="id")
	 * })
	 */
	private $tipoEstado;

	/**
	 * @var boolean
	 * @ORM\Column(name="it_estado_dg", type="boolean", nullable=true)
	 */

	private $itEstadoDg;

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
     * @return EstadoEncargo
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
     * @return EstadoEncargo
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

	public function __toString()
	{
		return $this->descripcion;// TODO: Implement __toString() method.
	}

	/**
	 * @return TipoEstado|null
	 */
	public function getTipoEstado()
	{
		return $this->tippoEstado;
	}

	/**
	 * @param TipoEstado|null $tippoEstado
	 */
	public function setTipoEstado($tippoEstado)
	{
		$this->tippoEstado = $tippoEstado;
	}

	/**
	 * @return bool
	 */
	public function isItEstadoDg()
	{
		return $this->itEstadoDg;
	}

	/**
	 * @param bool $itEstadoDg
	 */
	public function setItEstadoDg($itEstadoDg)
	{
		$this->itEstadoDg = $itEstadoDg;
	}


}
