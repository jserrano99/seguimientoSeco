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
 * @ORM\Table(name="importes_contrato")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ImportesContratoRepository")
 *
 */

class ImportesContrato
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
	 * @var float
	 *
	 * @ORM\Column(name="cuota_fija", type="float", nullable=true)
	 */
	private $cuotaFija;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_fija_mensual", type="float", nullable=true)
	 */
	private $cuotaFijaMensual;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_variable", type="float", nullable=true)
	 */
	private $cuotaVariable;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="cuota_tasada", type="float", nullable=true)
	 */
	private $cuotaTasada;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="tarifa_hora", type="float", nullable=true)
	 */
	private $tarifaHora;

	/**
	 * @var float
	 *
	 * @ORM\Column(name="tarifa_hora_cs", type="float", nullable=true)
	 */
	private $tarifaHoraCs;

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
	 * @return float
	 */
	public function getCuotaFija()
	{
		return $this->cuotaFija;
	}

	/**
	 * @param float $cuotaFija
	 */
	public function setCuotaFija($cuotaFija)
	{
		$this->cuotaFija = $cuotaFija;
	}

	/**
	 * @return float
	 */
	public function getCuotaFijaMensual()
	{
		return $this->cuotaFijaMensual;
	}

	/**
	 * @param float $cuotaFijaMensual
	 */
	public function setCuotaFijaMensual($cuotaFijaMensual)
	{
		$this->cuotaFijaMensual = $cuotaFijaMensual;
	}

	/**
	 * @return float
	 */
	public function getCuotaVariable()
	{
		return $this->cuotaVariable;
	}

	/**
	 * @param float $cuotaVariable
	 */
	public function setCuotaVariable($cuotaVariable)
	{
		$this->cuotaVariable = $cuotaVariable;
	}

	/**
	 * @return float
	 */
	public function getCuotaTasada()
	{
		return $this->cuotaTasada;
	}

	/**
	 * @param float $cuotaTasada
	 */
	public function setCuotaTasada($cuotaTasada)
	{
		$this->cuotaTasada = $cuotaTasada;
	}

	/**
	 * @return float
	 */
	public function getTarifaHora()
	{
		return $this->tarifaHora;
	}

	/**
	 * @param float $tarifaHora
	 */
	public function setTarifaHora($tarifaHora)
	{
		$this->tarifaHora = $tarifaHora;
	}

	/**
	 * @return float
	 */
	public function getTarifaHoraCs()
	{
		return $this->tarifaHoraCs;
	}

	/**
	 * @param float $tarifaHoraCs
	 */
	public function setTarifaHoraCs($tarifaHoraCs)
	{
		$this->tarifaHoraCs = $tarifaHoraCs;
	}


}
