<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 10/04/2019
 * Time: 17:20
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="posicion_economica")
 * @ORM\Entity()
 *
 */
class PosicionEconomica
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
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;


	/**
	 * @var TipoCuota|null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoCuota")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_cuota_id", referencedColumnName="id")
	 * })
	 */
	private $tipoCuota;


	public function __toString()
	{
		// TODO: Implement __toString() method.
		return  $this->descripcion;
	}

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
	 * @return string
	 */
	public function getDescripcion()
	{
		return $this->descripcion;
	}

	/**
	 * @param string $descripcion
	 */
	public function setDescripcion($descripcion)
	{
		$this->descripcion = $descripcion;
	}

	/**
	 * @return TipoCuota
	 */
	public function getTipoCuota()
	{
		return $this->tipoCuota;
	}

	/**
	 * @param TipoCuota $tipoCuota
	 */
	public function setTipoCuota($tipoCuota)
	{
		$this->tipoCuota = $tipoCuota;
	}


}
