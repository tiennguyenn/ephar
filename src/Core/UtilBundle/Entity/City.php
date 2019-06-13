<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * City
 *
 * @ORM\Table(name="city", indexes={@ORM\Index(name="FK_city_country", columns={"country_id"}), @ORM\Index(name="FK_city_state", columns={"state_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CityRepository")
 */
class City
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
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var \State
     *
     * @ORM\ManyToOne(targetEntity="State", inversedBy="cities")
     *   @ORM\JoinColumn(name="state_id", referencedColumnName="id")
     * })
     */
    private $state;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country",inversedBy="cities")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;

      /**
     * @ORM\OneToMany(targetEntity="Area", mappedBy="city", cascade={"persist", "remove" })
     */
    private $areas;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->areas = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return City
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set state
     *
     * @param \UtilBundle\Entity\State $state
     * @return City
     */
    public function setState(\UtilBundle\Entity\State $state = null)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Get state
     *
     * @return \UtilBundle\Entity\State 
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set country
     *
     * @param \UtilBundle\Entity\Country $country
     * @return City
     */
    public function setCountry(\UtilBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Add area
     *
     * @param \UtilBundle\Entity\Area $area
     *
     * @return City
     */
    public function addArea(\UtilBundle\Entity\Area $area)
    {
        $this->areas[] = $area;

        return $this;
    }

    /**
     * Remove area
     *
     * @param \UtilBundle\Entity\Area $area
     */
    public function removeArea(\UtilBundle\Entity\Area $area)
    {
        $this->areas->removeElement($area);
    }

    /**
     * Get areas
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAreas()
    {
        return $this->areas;
    }
}
