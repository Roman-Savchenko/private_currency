<?php

namespace DollarAndEuro;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
/**
 * DollarAndEuro
 *
 * @ORM\Entity(repositoryClass="DollarRepository")
 * @ORM\Table(name="dollar_and_euro")
 */
class DollarAndEuro
{
    /**
     * @var float
     *
     * @ORM\Column(name="silver", type="decimal", precision=12, scale=3, nullable=false)
     */
    protected $course = 0.000;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="added_at", type="datetime", nullable=false)
     */
    protected $date;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set course
     *
     * @param  $course
     *
     * @return DollarAndEuro
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get course
     *
     * @return mixed
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return DollarAndEuro
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

}