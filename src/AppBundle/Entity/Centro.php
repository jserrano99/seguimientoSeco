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
 * @ORM\Table(name="centro")
 * @ORM\Entity
 */

class Centro
{
	/**
	 * @var integer
	 * @ORM\Column(name="id", type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="IDENTITY")
	 */
	private $id;

	/**
	 * @var string
	 * @ORM\Column(name="codigo", type="string",length=15,nullable=false)
	 */
	private $codigo;

	/**
	 * @var string
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var boolean
	 * @ORM\Column(name="valido", type="boolean",nullable=false)
	 */
	private $valido;


	/**
	 * @var Centro|null
	 * @ORM\ManyToOne(targetEntity="Centro")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="centro_unif_id", referencedColumnName="id")
	 * })
	 */
	private $centroUnificado;


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
     * @return Centro
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
     * @return Centro
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

    public  function  __toString()
    {
	    return $this->descripcion;
    }

    /**
     * Set valido.
     *
     * @param bool $valido
     *
     * @return Centro
     */
    public function setValido($valido)
    {
        $this->valido = $valido;

        return $this;
    }

    /**
     * Get valido.
     *
     * @return bool
     */
    public function getValido()
    {
        return $this->valido;
    }

    /**
     * Set centroUnificado.
     *
     * @param Centro|null $centroUnificado
     *
     * @return Centro
     */
    public function setCentroUnificado(Centro $centroUnificado = null)
    {
        $this->centroUnificado = $centroUnificado;

        return $this;
    }

    /**
     * Get centroUnificado.
     *
     * @return Centro|null
     */
    public function getCentroUnificado()
    {
        return $this->centroUnificado;
    }
}
