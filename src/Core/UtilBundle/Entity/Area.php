<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Area
 *
 * @ORM\Table(name="area", indexes={@ORM\Index(name="FK_area", columns={"city_id"})})
 * @ORM\Entity
 */
class Area
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
     * @ORM\Column(name="area", type="string", length=50, nullable=false)
     */
    private $area;

    /**
     * @var string
     *
     * @ORM\Column(name="postal", type="string", length=10, nullable=false)
     */
    private $postal;

    /**
     * @var \City
     *
     * @ORM\ManyToOne(targetEntity="City")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     * })
     */
    private $city;



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
     * Set area
     *
     * @param string $area
     *
     * @return Area
     */
    public function setArea($area)
    {
        $this->area = $area;

        return $this;
    }

    /**
     * Get area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
    }

    /**
     * Set postal
     *
     * @param string $postal
     *
     * @return Area
     */
    public function setPostal($postal)
    {
        $this->postal = $postal;

        return $this;
    }

    /**
     * Get postal
     *
     * @return string
     */
    public function getPostal()
    {
        return $this->postal;
    }

    /**
     * Set city
     *
     * @param \UtilBundle\Entity\City $city
     *
     * @return Area
     */
    public function setCity(\UtilBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \UtilBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }
}
