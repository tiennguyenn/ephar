<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxIssueCounter
 *
 * @ORM\Table(name="rx_issue_counter", uniqueConstraints={@ORM\UniqueConstraint(name="NewIndex1", columns={"rx_id"})})
 * @ORM\Entity
 */
class RxIssueCounter
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_doctor_read", type="boolean", nullable=true)
     */
    private $isDoctorRead = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_customer_care_read", type="boolean", nullable=true)
     */
    private $isCustomerCareRead = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pharmacy_read", type="boolean", nullable=true)
     */
    private $isPharmacyRead = '0';

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
     * @var \UtilBundle\Entity\Rx
     *
     * @ORM\ManyToOne(targetEntity="UtilBundle\Entity\Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;



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
     * Set isDoctorRead
     *
     * @param boolean $isDoctorRead
     *
     * @return RxIssueCounter
     */
    public function setIsDoctorRead($isDoctorRead)
    {
        $this->isDoctorRead = $isDoctorRead;

        return $this;
    }

    /**
     * Get isDoctorRead
     *
     * @return boolean
     */
    public function getIsDoctorRead()
    {
        return $this->isDoctorRead;
    }

    /**
     * Set isCustomerCareRead
     *
     * @param boolean $isCustomerCareRead
     *
     * @return RxIssueCounter
     */
    public function setIsCustomerCareRead($isCustomerCareRead)
    {
        $this->isCustomerCareRead = $isCustomerCareRead;

        return $this;
    }

    /**
     * Get isCustomerCareRead
     *
     * @return boolean
     */
    public function getIsCustomerCareRead()
    {
        return $this->isCustomerCareRead;
    }

    /**
     * Set isPharmacyRead
     *
     * @param boolean $isPharmacyRead
     *
     * @return RxIssueCounter
     */
    public function setIsPharmacyRead($isPharmacyRead)
    {
        $this->isPharmacyRead = $isPharmacyRead;

        return $this;
    }

    /**
     * Get isPharmacyRead
     *
     * @return boolean
     */
    public function getIsPharmacyRead()
    {
        return $this->isPharmacyRead;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return RxIssueCounter
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
     *
     * @return RxIssueCounter
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
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return RxIssueCounter
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx
     */
    public function getRx()
    {
        return $this->rx;
    }
}
