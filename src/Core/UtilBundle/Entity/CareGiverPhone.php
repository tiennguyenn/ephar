<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CareGiverPhone
 *
 * @ORM\Table(name="care_giver_phone", indexes={@ORM\Index(name="FK_care_giver_phone", columns={"care_giver_id"}), @ORM\Index(name="FK_care_giver_phone_1", columns={"phone_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class CareGiverPhone
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
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
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
     * @var \CareGiver
     *
     * @ORM\ManyToOne(targetEntity="CareGiver")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="care_giver_id", referencedColumnName="id")
     * })
     */
    private $careGiver;



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
     * @return CareGiverPhone
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
     * @return CareGiverPhone
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
     * Set careGiver
     *
     * @param \UtilBundle\Entity\CareGiver $careGiver
     * @return CareGiverPhone
     */
    public function setCareGiver(\UtilBundle\Entity\CareGiver $careGiver = null)
    {
        $this->careGiver = $careGiver;

        return $this;
    }

    /**
     * Get careGiver
     *
     * @return \UtilBundle\Entity\CareGiver 
     */
    public function getCareGiver()
    {
        return $this->careGiver;
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime();
    }
}
