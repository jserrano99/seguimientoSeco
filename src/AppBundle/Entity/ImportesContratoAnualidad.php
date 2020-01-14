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
 * @ORM\Table(name="importes_contrato_anualidad")
 * @ORM\Entity
 *
 */

class ImportesContratoAnualidad
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
	 * @var Contrato
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Contrato")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="contrato_id", referencedColumnName="id")
	 * })
	 */
	private $contrato;

	/**
	 * @var Anyo
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Anyo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="anyo_id", referencedColumnName="id")
	 * })
	 */
	private $anyo;

	/**
	 * @var PosicionEconomica
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\PosicionEconomica")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="posicion_economica_id", referencedColumnName="id")
	 * })
	 */
	private $posicionEconomica;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="importe", type="float", nullable=true)
	 */
	private $importe;

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
	 * @return Contrato
	 */
	public function getContrato()
	{
		return $this->contrato;
	}

	/**
	 * @param Contrato $contrato
	 */
	public function setContrato($contrato)
	{
		$this->contrato = $contrato;
	}

	/**
	 * @return Anyo
	 */
	public function getAnyo()
	{
		return $this->anyo;
	}

	/**
	 * @param Anyo $anyo
	 */
	public function setAnyo($anyo)
	{
		$this->anyo = $anyo;
	}

	/**
	 * @return PosicionEconomica
	 */
	public function getPosicionEconomica()
	{
		return $this->posicionEconomica;
	}

	/**
	 * @param PosicionEconomica $posicionEconomica
	 */
	public function setPosicionEconomica($posicionEconomica)
	{
		$this->posicionEconomica = $posicionEconomica;
	}

	/**
	 * @return float
	 */
	public function getImporte()
	{
		return $this->importe;
	}

	/**
	 * @param float $importe
	 */
	public function setImporte($importe)
	{
		$this->importe = $importe;
	}


}
