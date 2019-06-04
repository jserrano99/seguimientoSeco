<?php

namespace AppBundle\Entity;

/**
 * cambioPassword
 */
class cambioPassword
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $old;

    /**
     * @var string
     */
    private $new;

    /**
     * @var string
     */
    private $new2;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set old
     *
     * @param string $old
     *
     * @return cambioPassword
     */
    public function setOld($old)
    {
        $this->old = $old;

        return $this;
    }

    /**
     * Get old
     *
     * @return string
     */
    public function getOld()
    {
        return $this->old;
    }

    /**
     * Set new
     *
     * @param string $new
     *
     * @return cambioPassword
     */
    public function setNew($new)
    {
        $this->new = $new;

        return $this;
    }

    /**
     * Get new
     *
     * @return string
     */
    public function getNew()
    {
        return $this->new;
    }

    /**
     * Set new2
     *
     * @param string $new2
     *
     * @return cambioPassword
     */
    public function setNew2($new2)
    {
        $this->new2 = $new2;

        return $this;
    }

    /**
     * Get new2
     *
     * @return string
     */
    public function getNew2()
    {
        return $this->new2;
    }
}

