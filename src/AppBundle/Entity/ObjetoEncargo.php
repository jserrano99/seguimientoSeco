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
 * @ORM\Table(name="objetos_encargo")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ObjetoEncargoRepository")
 */

class ObjetoEncargo
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
	 * @ORM\Column(name="codigo", type="string", length=3, nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;

	/**
	 * @var TipoObjeto|null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoObjeto")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_objeto_id", referencedColumnName="id")
	 * })
	 */
	private $tipoObjeto;

	/**
	 * @var TipoCuota|null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoCuota")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_cuota_id", referencedColumnName="id")
	 * })
	 */
	private $tipoCuota;



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
     * @return ObjetoEncargo
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
     * @return ObjetoEncargo
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
     * Set tipoObjeto.
     *
     * @param \AppBundle\Entity\TipoObjeto|null $tipoObjeto
     *
     * @return ObjetoEncargo
     */
    public function setTipoObjeto(\AppBundle\Entity\TipoObjeto $tipoObjeto = null)
    {
        $this->tipoObjeto = $tipoObjeto;

        return $this;
    }

    /**
     * Get tipoObjeto.
     *
     * @return \AppBundle\Entity\TipoObjeto|null
     */
    public function getTipoObjeto()
    {
        return $this->tipoObjeto;
    }

    /**
     * Set tipoCuota.
     *
     * @param \AppBundle\Entity\TipoCuota|null $tipoCuota
     *
     * @return ObjetoEncargo
     */
    public function setTipoCuota(\AppBundle\Entity\TipoCuota $tipoCuota = null)
    {
        $this->tipoCuota = $tipoCuota;

        return $this;
    }

    /**
     * Get tipoCuota.
     *
     * @return \AppBundle\Entity\TipoCuota|null
     */
    public function getTipoCuota()
    {
        return $this->tipoCuota;
    }

	public function __toString()
	{
	return $this->descripcion;
	}
}
