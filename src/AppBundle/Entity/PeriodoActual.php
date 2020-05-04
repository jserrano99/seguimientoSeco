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
 * @ORM\Table(name="periodo_actual")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PeriodoActualRepository")
 *
 */
class PeriodoActual
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
	 * @var Mes\null
	 *
	 * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Mes")
	 * * @ORM\JoinColumns({
	 *   @ORM\JoinColumn(name="periodo_actual_id", referencedColumnName="id")
	 * })
	 */
	private $periodo;

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
     * @return Mes\null
     */
    public function getPeriodo()
    {
        return $this->periodo;
    }

    /**
     * @param Mes\null $periodo
     */
    public function setPeriodo($periodo)
    {
        $this->periodo = $periodo;
    }



}
