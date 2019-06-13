<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveChangeAddress
 *
 * @ORM\Table(name="resolve_change_address", indexes={@ORM\Index(name="FK_resolve_change_address", columns={"resolve_id"}), @ORM\Index(name="FK_resolve_change_address_1", columns={"address_id"}), @ORM\Index(name="FK_resolve_change_address_patient", columns={"patient_phone_id"}), @ORM\Index(name="FK_resolve_change_address_care_giver", columns={"care_giver_phone_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ResolveChangeAddress
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
     * @var integer
     *
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false, options={"default": "CURRENT_TIMESTAMP"})
     */
    private $createdOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="care_giver_phone_id", referencedColumnName="id")
     * })
     */
    private $careGiverPhone;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;

    /**
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone",cascade={"persist"})
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_phone_id", referencedColumnName="id")
     * })
     */
    private $patientPhone;


    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
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
     * Set status
     *
     * @param integer $status
     *
     * @return ResolveChangeAddress
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveChangeAddress
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
     * Set careGiverPhone
     *
     * @param \UtilBundle\Entity\Phone $careGiverPhone
     *
     * @return ResolveChangeAddress
     */
    public function setCareGiverPhone(\UtilBundle\Entity\Phone $careGiverPhone = null)
    {
        $this->careGiverPhone = $careGiverPhone;

        return $this;
    }

    /**
     * Get careGiverPhone
     *
     * @return \UtilBundle\Entity\Phone
     */
    public function getCareGiverPhone()
    {
        return $this->careGiverPhone;
    }

    /**
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveChangeAddress
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }

    /**
     * Set address
     *
     * @param \UtilBundle\Entity\Address $address
     *
     * @return ResolveChangeAddress
     */
    public function setAddress(\UtilBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \UtilBundle\Entity\Address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set patientPhone
     *
     * @param \UtilBundle\Entity\Phone $patientPhone
     *
     * @return ResolveChangeAddress
     */
    public function setPatientPhone(\UtilBundle\Entity\Phone $patientPhone = null)
    {
        $this->patientPhone = $patientPhone;

        return $this;
    }

    /**
     * Get patientPhone
     *
     * @return \UtilBundle\Entity\Phone
     */
    public function getPatientPhone()
    {
        return $this->patientPhone;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return ResolveChangeAddress
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveChangeAddress
     */
    public function setDeletedOn($deletedOn)
    {
        $this->deletedOn = $deletedOn;

        return $this;
    }

    /**
     * Get deletedOn
     *
     * @return \DateTime
     */
    public function getDeletedOn()
    {
        return $this->deletedOn;
    }
}
