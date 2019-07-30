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
 * @ORM\Table(name="encargo_remedy")
 * @ORM\Entity()
 *
 */

class EncargoRemedy
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
     * @var Encargo\null
     *
     * @ORM\ManyToOne(targetEntity="Encargo")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="encargo_id", referencedColumnName="id")
     * })
     */
    private $encargo;

    /**
     * @var Remedy\null
     *
     * @ORM\ManyToOne(targetEntity="Remedy")
     * * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="remedy_id", referencedColumnName="id")
     * })
     */
    private $remedy;

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
     * Set encargo.
     *
     * @param \AppBundle\Entity\Encargo|null $encargo
     *
     * @return EncargoRemedy
     */
    public function setEncargo(\AppBundle\Entity\Encargo $encargo = null)
    {
        $this->encargo = $encargo;

        return $this;
    }

    /**
     * Get encargo.
     *
     * @return \AppBundle\Entity\Encargo|null
     */
    public function getEncargo()
    {
        return $this->encargo;
    }

    /**
     * Set remedy.
     *
     * @param \AppBundle\Entity\Remedy|null $remedy
     *
     * @return EncargoRemedy
     */
    public function setRemedy(\AppBundle\Entity\Remedy $remedy = null)
    {
        $this->remedy = $remedy;

        return $this;
    }

    /**
     * Get remedy.
     *
     * @return \AppBundle\Entity\Remedy|null
     */
    public function getRemedy()
    {
        return $this->remedy;
    }
}
