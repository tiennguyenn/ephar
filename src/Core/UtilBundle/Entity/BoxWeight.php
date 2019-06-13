<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoxWeight
 *
 * @ORM\Table(name="box_weight", indexes={@ORM\Index(name="FK_box_weight", columns={"box_id"})})
 * @ORM\Entity
 */
class BoxWeight
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
     * @ORM\Column(name="weight", type="decimal", precision=13, scale=3, nullable=false)
     */
    private $weight;

    /**
     * @var string
     *
     * @ORM\Column(name="weighing_area", type="string", length=50, nullable=false)
     */
    private $weighingArea;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_weighed", type="datetime", nullable=false)
     */
    private $dateWeighed;

    /**
     * @var \Box
     *
     * @ORM\ManyToOne(targetEntity="Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     * })
     */
    private $box;



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
     * Set weight
     *
     * @param string $weight
     * @return BoxWeight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * Get weight
     *
     * @return string 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set weighingArea
     *
     * @param string $weighingArea
     * @return BoxWeight
     */
    public function setWeighingArea($weighingArea)
    {
        $this->weighingArea = $weighingArea;

        return $this;
    }

    /**
     * Get weighingArea
     *
     * @return string 
     */
    public function getWeighingArea()
    {
        return $this->weighingArea;
    }

    /**
     * Set dateWeighed
     *
     * @param \DateTime $dateWeighed
     * @return BoxWeight
     */
    public function setDateWeighed($dateWeighed)
    {
        $this->dateWeighed = $dateWeighed;

        return $this;
    }

    /**
     * Get dateWeighed
     *
     * @return \DateTime 
     */
    public function getDateWeighed()
    {
        return $this->dateWeighed;
    }

    /**
     * Set box
     *
     * @param \UtilBundle\Entity\Box $box
     * @return BoxWeight
     */
    public function setBox(\UtilBundle\Entity\Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \UtilBundle\Entity\Box 
     */
    public function getBox()
    {
        return $this->box;
    }
}
