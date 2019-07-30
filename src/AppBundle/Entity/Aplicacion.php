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
 * @ORM\Table(name="aplicacion")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AplicacionRepository")
 */

class Aplicacion
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
	 * @ORM\Column(name="descripcion", type="string", nullable=true)
	 */
	private $descripcion;

    /**
     * @var boolean
     * @ORM\Column(name="valido", type="boolean",nullable=true)
     */
    private $valido;


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
     * @return Aplicacion
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
     * @return Aplicacion
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
     * Set valido.
     *
     * @param bool|null $valido
     *
     * @return Aplicacion
     */
    public function setValido($valido = null)
    {
        $this->valido = $valido;

        return $this;
    }

    /**
     * Get valido.
     *
     * @return bool|null
     */
    public function getValido()
    {
        return $this->valido;
    }
}
