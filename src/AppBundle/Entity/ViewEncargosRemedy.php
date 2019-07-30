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
 * @ORM\Table(name="view_encargos_remedy")
 * @ORM\Entity()
 *
 */

class ViewEncargosRemedy
{
    /**
     * @var integer
     *
     * @ORM\Column(name="objeto_encargo_id", type="integer")
     */
    private $objetoEncargoid;

    /**
     * @var string
     *
     * @ORM\Column(name="titulo", type="string")
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="numeroEncargo", type="string")
     */
    private $numeroEncargo;

    /**
     * @var integer
     *
     * @ORM\Column(name="encargoId", type="integer")
     */
    private $encargoId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="esRepertura", type="boolean")
     */
    private $esReapertura;

    /**
     * @return int
     */
    public function getObjetoEncargoid()
    {
        return $this->objetoEncargoid;
    }

    /**
     * @return string
     */
    public function getTitulo()
    {
        return $this->titulo;
    }

    /**
     * @return string
     */
    public function getNumeroEncargo()
    {
        return $this->numeroEncargo;
    }

    /**
     * @return int
     */
    public function getEncargoId()
    {
        return $this->encargoId;
    }

    /**
     * @return bool
     */
    public function isEsReapertura()
    {
        return $this->esReapertura;
    }



}
