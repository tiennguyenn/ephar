<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * XeroPayment
 *
 * @ORM\Table(name="xero_payment")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroPayment
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
     * @ORM\Column(name="xero_guid", type="string", length=38, nullable=true)
     */
    private $xeroGuid;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255, nullable=false)
     */
    private $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=50, nullable=false)
     */
    private $createdBy;

    /**
     * @var \XeroSyncData
     *
     * @ORM\ManyToOne(targetEntity="XeroSyncData", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_sync_data_id", referencedColumnName="id")
     * })
     */
    private $xeroSyncData;

    /**
     * @ORM\OneToMany(targetEntity="XeroPaymentLine", mappedBy="xeroPayment", cascade={"persist", "remove" })
     */
    private $lines;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lines = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set xeroSyncData
     *
     * @param \UtilBundle\Entity\XeroSyncData $xeroSyncData
     *
     * @return XeroPayment
     */
    public function setXeroSyncData(\UtilBundle\Entity\XeroSyncData $xeroSyncData = null)
    {
        $this->xeroSyncData = $xeroSyncData;

        return $this;
    }

    /**
     * Get xeroSyncData
     *
     * @return \UtilBundle\Entity\XeroSyncData
     */
    public function getXeroSyncData()
    {
        return $this->xeroSyncData;
    }

    /**
     * Set xeroGuid
     *
     * @param string $xeroGuid
     *
     * @return XeroPayment
     */
    public function setXeroGuid($xeroGuid)
    {
        $this->xeroGuid = $xeroGuid;

        return $this;
    }

    /**
     * Get xeroGuid
     *
     * @return string
     */
    public function getXeroGuid()
    {
        return $this->xeroGuid;
    }

    /**
     * Set reference
     *
     * @param string $reference
     *
     * @return XeroPayment
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * Get reference
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroPayment
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
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return XeroPayment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Add line
     *
     * @param \UtilBundle\Entity\XeroPaymentLine $line
     *
     * @return XeroPayment
     */
    public function addLine(\UtilBundle\Entity\XeroPaymentLine $line)
    {
        $line->setXeroPayment($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\XeroPaymentLine $line
     */
    public function removeLine(\UtilBundle\Entity\XeroPaymentLine $line)
    {
        $this->lines->removeElement($line);
    }

    /**
     * Get lines
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLines()
    {
        return $this->lines;
    }
}
