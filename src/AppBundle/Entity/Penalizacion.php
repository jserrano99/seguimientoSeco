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
 * @ORM\Table(name="penalizaciones")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PenalizacionRepository")
 *
 */

class Penalizacion
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
	 * @var Indicador\null
	 *
	 * @ORM\ManyToOne(targetEntity="Indicador")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="indicador_id", referencedColumnName="id")
	 * })
	 */
	private $indicador;

	/**
	 * @var Mes\null
	 *
	 * @ORM\ManyToOne(targetEntity="Mes")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="meses_id", referencedColumnName="id")
	 * })
	 */
	private $mes;


	/**
	 * @var float
	 *
	 * @ORM\Column(name="importe", type="float", nullable=true)*
	 */

	private $importe;

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
     * Set importe.
     *
     * @param string|null $importe
     *
     * @return Penalizacion
     */
    public function setImporte($importe = null)
    {
        $this->importe = $importe;

        return $this;
    }

    /**
     * Get importe.
     *
     * @return string|null
     */
    public function getImporte()
    {
        return $this->importe;
    }

    /**
     * Set indicador.
     *
     * @param \AppBundle\Entity\Indicador|null $indicador
     *
     * @return Penalizacion
     */
    public function setIndicador(\AppBundle\Entity\Indicador $indicador = null)
    {
        $this->indicador = $indicador;

        return $this;
    }

    /**
     * Get indicador.
     *
     * @return \AppBundle\Entity\Indicador|null
     */
    public function getIndicador()
    {
        return $this->indicador;
    }

    /**
     * Set mes.
     *
     * @param \AppBundle\Entity\Mes|null $mes
     *
     * @return Penalizacion
     */
    public function setMes(\AppBundle\Entity\Mes $mes = null)
    {
        $this->mes = $mes;

        return $this;
    }

    /**
     * Get mes.
     *
     * @return \AppBundle\Entity\Mes|null
     */
    public function getMes()
    {
        return $this->mes;
    }
}
