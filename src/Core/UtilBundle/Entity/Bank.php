<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Bank
 *
 * @ORM\Table(name="bank", indexes={@ORM\Index(name="FK_bank_country", columns={"country_id"}), @ORM\Index(name="FK_bank_city", columns={"city_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\BankRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Bank
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="branch_name", type="string", length=255, nullable=true)
     */
    private $branchName;

    /**
     * @var string
     *
     * @ORM\Column(name="swift_code", type="string", length=16, nullable=false)
     */
    private $swiftCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;



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
     * @return Bank
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
     * Set branchName
     *
     * @param string $branchName
     * @return Bank
     */
    public function setBranchName($branchName)
    {
        $this->branchName = $branchName;

        return $this;
    }

    /**
     * Get branchName
     *
     * @return string 
     */
    public function getBranchName()
    {
        return $this->branchName;
    }

    /**
     * Set swiftCode
     *
     * @param string $swiftCode
     * @return Bank
     */
    public function setSwiftCode($swiftCode)
    {
        $this->swiftCode = $swiftCode;

        return $this;
    }

    /**
     * Get swiftCode
     *
     * @return string 
     */
    public function getSwiftCode()
    {
        return $this->swiftCode;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Bank
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime 
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return Bank
     */
    public function setUpdatedOn($updatedOn)
    {
        $this->updatedOn = $updatedOn;

        return $this;
    }

    /**
     * Get updatedOn
     *
     * @return \DateTime 
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn;
    }

    /**
     * Set city
     *
     * @param \UtilBundle\Entity\City $city
     * @return Bank
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

    /**
     * Set country
     *
     * @param \UtilBundle\Entity\Country $country
     * @return Bank
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
    }

}
