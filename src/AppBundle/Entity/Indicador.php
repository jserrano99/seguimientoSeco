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
 * @ORM\Table(name="indicadores")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\IndicadorRepository")
 *
 */
class Indicador
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
	 * @ORM\Column(name="codigo", type="string",length=20,nullable=false)
	 */
	private $codigo;
	/**
	 * @var string
	 *
	 * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
	 */
	private $descripcion;


	/**
	 * @var TipoIndicador\null
	 *
	 * @ORM\ManyToOne(targetEntity="TipoIndicador")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="tipo_indicador_id", referencedColumnName="id")
	 * })
	 */
	private $tipoIndicador;


	/**
	 * @var float
	 * @ORM\Column(name="peso", type="float", nullable=false)
	 *
	 */

	private $peso;

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
	 * @return Indicador
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
	 * @return Indicador
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
	 * Set tipoIndicador.
	 *
	 * @param \AppBundle\Entity\TipoIndicador|null $tipoIndicador
	 *
	 * @return Indicador
	 */
	public function setTipoIndicador(\AppBundle\Entity\TipoIndicador $tipoIndicador = null)
	{
		$this->tipoIndicador = $tipoIndicador;

		return $this;
	}

	/**
	 * Get tipoIndicador.
	 *
	 * @return \AppBundle\Entity\TipoIndicador|null
	 */
	public function getTipoIndicador()
	{
		return $this->tipoIndicador;
	}

	/**
	 * Set peso.
	 *
	 * @param float $peso
	 *
	 * @return Indicador
	 */
	public function setPeso($peso)
	{
		$this->peso = $peso;

		return $this;
	}

	/**
	 * Get peso.
	 *
	 * @return float
	 */
	public function getPeso()
	{
		return $this->peso;
	}

	public function __toString()
	{
		return $this->codigo.' : '.$this->descripcion;
	}
}
