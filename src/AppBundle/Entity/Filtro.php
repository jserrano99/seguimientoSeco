<?php
/**
 * Created by PhpStorm.
 * User: jluis_local
 * Date: 27/05/2019
 * Time: 9:51
 */

namespace AppBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/** Mes
 *
 * @ORM\Table(name="filtro",
 *           )
 * @ORM\Entity
*/

class Filtro
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
	 * @var \AppBundle\Entity\Anyo
	 * @ORM\ManyToOne(targetEntity="Anyo")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="anyo_id", referencedColumnName="id")
	 * })
	 */
	private $anyo;

	/**
	 * @var \AppBundle\Entity\Mes
	 * @ORM\ManyToOne(targetEntity="Mes")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="mes_id", referencedColumnName="id")
	 * })
	 */
	private $mes;

	/**
	 * @var boolean
	 * @ORM\Column(name="aplicar_penalizaciones", type="boolean", nullable=true)*
	 */
	private $aplicarPenalizaciones;

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
     * Set anyo.
     *
     * @param \AppBundle\Entity\Anyo|null $anyo
     *
     * @return Filtro
     */
    public function setAnyo(\AppBundle\Entity\Anyo $anyo = null)
    {
        $this->anyo = $anyo;

        return $this;
    }

    /**
     * Get anyo.
     *
     * @return \AppBundle\Entity\Anyo|null
     */
    public function getAnyo()
    {
        return $this->anyo;
    }

    /**
     * Set mes.
     *
     * @param \AppBundle\Entity\Mes|null $mes
     *
     * @return Filtro
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

    /**
     * Set aplicarPenalizaciones.
     *
     * @param bool|null $aplicarPenalizaciones
     *
     * @return Filtro
     */
    public function setAplicarPenalizaciones($aplicarPenalizaciones = null)
    {
        $this->aplicarPenalizaciones = $aplicarPenalizaciones;

        return $this;
    }

    /**
     * Get aplicarPenalizaciones.
     *
     * @return bool|null
     */
    public function getAplicarPenalizaciones()
    {
        return $this->aplicarPenalizaciones;
    }
}
