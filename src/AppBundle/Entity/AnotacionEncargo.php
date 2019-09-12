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
 * @ORM\Table(name="anotaciones_encargo")
 * @ORM\Entity
 *
 */
class AnotacionEncargo
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
	 * @var text
	 *
	 * @ORM\Column(name="anotacion", type="text", length=255, nullable=false)
	 */
	private $anotacion;

	/**
	 * @var \Datetime
	 *
	 * @ORM\Column(name="fecha", type="datetime", nullable=true)
	 */
	private $fecha;

	/**
	 * @var Encargo
	 *
	 * @ORM\ManyToOne(targetEntity="Encargo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="encargo_id", referencedColumnName="id")
	 * })
	 */
	private $encargo;

	/**
	 * @var Usuario
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Usuario")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
	 * })
	 */
	private $usuario;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return text
	 */
	public function getAnotacion()
	{
		return $this->anotacion;
	}

	/**
	 * @param text $anotacion
	 */
	public function setAnotacion($anotacion)
	{
		$this->anotacion = $anotacion;
	}

	/**
	 * @return Datetime
	 */
	public function getFecha()
	{
		return $this->fecha;
	}

	/**
	 * @param Datetime $fecha
	 */
	public function setFecha($fecha)
	{
		$this->fecha = $fecha;
	}

	/**
	 * @return Encargo
	 */
	public function getEncargo()
	{
		return $this->encargo;
	}

	/**
	 * @param Encargo $encargo
	 */
	public function setEncargo($encargo)
	{
		$this->encargo = $encargo;
	}

	/**
	 * @return Usuario
	 */
	public function getUsuario()
	{
		return $this->usuario;
	}

	/**
	 * @param Usuario $usuario
	 */
	public function setUsuario($usuario)
	{
		$this->usuario = $usuario;
	}



}
