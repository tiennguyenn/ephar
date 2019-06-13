<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PharmacyPhone
 *
 * @ORM\Table(name="pharmacy_phone", indexes={@ORM\Index(name="FK_pharmacy_phone", columns={"phone_id"}), @ORM\Index(name="FK_pharmacy_phone_1", columns={"pharmacy_id"})})
 * @ORM\Entity
 */
class PharmacyPhone
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     * })
     */
    private $phone;

    /**
     * @var \Pharmacy
     *
     * @ORM\ManyToOne(targetEntity="Pharmacy")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pharmacy_id", referencedColumnName="id")
     * })
     */
    private $pharmacy;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PharmacyPhone
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
     * Set phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     * @return PharmacyPhone
     */
    public function setPhone(\UtilBundle\Entity\Phone $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \UtilBundle\Entity\Phone 
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set pharmacy
     *
     * @param \UtilBundle\Entity\Pharmacy $pharmacy
     * @return PharmacyPhone
     */
    public function setPharmacy(\UtilBundle\Entity\Pharmacy $pharmacy = null)
    {
        $this->pharmacy = $pharmacy;

        return $this;
    }

    /**
     * Get pharmacy
     *
     * @return \UtilBundle\Entity\Pharmacy 
     */
    public function getPharmacy()
    {
        return $this->pharmacy;
    }
}
