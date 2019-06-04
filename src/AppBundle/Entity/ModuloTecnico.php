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
 * @ORM\Table(name="modulo_tecnico")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ModuloTecnicoRepository")
 */

class ModuloTecnico
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
	 * @ORM\Column(name="codigo", type="string",length=15,nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;


	/**
	 * @var Aplicacion\null
	 *
	 * @ORM\ManyToOne(targetEntity="Aplicacion")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="aplicacion_id", referencedColumnName="id")
	 * })
	 */
	private $aplicacion;

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
     * @return ModuloTecnico
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
     * @return ModuloTecnico
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
     * Set aplicacion.
     *
     * @param \AppBundle\Entity\Aplicacion|null $aplicacion
     *
     * @return ModuloTecnico
     */
    public function setAplicacion(\AppBundle\Entity\Aplicacion $aplicacion = null)
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
}
