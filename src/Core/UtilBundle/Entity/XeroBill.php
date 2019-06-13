<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * XeroBill
 *
 * @ORM\Table(name="xero_bill", indexes={@ORM\Index(name="FK_xero_bill", columns={"xero_sync_data_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroBill
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
     * @ORM\Column(name="reference", type="string", length=250, nullable=false)
     */
    private $reference;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="date", nullable=false)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=100, nullable=false)
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
     * @ORM\OneToMany(targetEntity="XeroBillLine", mappedBy="xeroBill", cascade={"persist", "remove" })
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
     * Set xeroGuid
     *
     * @param string $xeroGuid
     *
     * @return XeroBill
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
     * @return XeroBill
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
     * @return XeroBill
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
     * @return XeroBill
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
     * Set xeroSyncData
     *
     * @param \UtilBundle\Entity\XeroSyncData $xeroSyncData
     *
     * @return XeroBill
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
     * Add line
     *
     * @param \UtilBundle\Entity\XeroBillLine $line
     *
     * @return XeroBill
     */
    public function addLine(\UtilBundle\Entity\XeroBillLine $line)
    {
        $line->setXeroBill($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\XeroBillLine $line
     */
    public function removeLine(\UtilBundle\Entity\XeroBillLine $line)
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
