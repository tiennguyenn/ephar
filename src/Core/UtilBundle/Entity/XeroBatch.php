<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * XeroBatch
 *
 * @ORM\Table(name="xero_batch")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroBatchRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroBatch
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
     * @ORM\Column(name="start_time", type="datetime", nullable=true)
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_time", type="datetime", nullable=true)
     */
    private $endTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="batch_date", type="datetime", nullable=true)
     */
    private $batchDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = '0';

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
     * @ORM\OneToMany(targetEntity="XeroSyncData", mappedBy="xeroBatch", cascade={"persist", "remove" })
     */
    private $syncs;
    
    /**
     * @var \Site
     *
     * @ORM\ManyToOne(targetEntity="Site")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     * })
     */
    private $site;

    /**
     * @ORM\OneToMany(targetEntity="XeroBatchRx", mappedBy="xeroBatch", cascade={"persist", "remove" })
     */
    private $batchRxes;

   



    /**
     * Constructor
     */
    public function __construct()
    {
        $this->syncs = new ArrayCollection();
        $this->batchRxes = new ArrayCollection();
    }

    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->updatedOn = new \DateTime("now");
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
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
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return XeroBatch
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return XeroBatch
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return XeroBatch
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
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
     * @return XeroBatch
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
     * @return XeroBatch
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
     * Add sync
     *
     * @param \UtilBundle\Entity\XeroSyncData $sync
     *
     * @return XeroBatch
     */
    public function addSync(\UtilBundle\Entity\XeroSyncData $sync)
    {
        $sync->setXeroBatch($this);
        $this->syncs[] = $sync;

        return $this;
    }

    /**
     * Remove sync
     *
     * @param \UtilBundle\Entity\XeroSyncData $sync
     */
    public function removeSync(\UtilBundle\Entity\XeroSyncData $sync)
    {
        $this->syncs->removeElement($sync);
    }

    /**
     * Get syncs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSyncs()
    {
        return $this->syncs;
    }

    /**
     * Set batchDate
     *
     * @param \DateTime $batchDate
     *
     * @return XeroBatch
     */
    public function setBatchDate($batchDate)
    {
        $this->batchDate = $batchDate;

        return $this;
    }

    /**
     * Get batchDate
     *
     * @return \DateTime
     */
    public function getBatchDate()
    {
        return $this->batchDate;
    }

    /**
     * Set site
     *
     * @param \UtilBundle\Entity\Site $site
     *
     * @return XeroBatch
     */
    public function setSite(\UtilBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \UtilBundle\Entity\Site
     */
    public function getSite()
    {
        return $this->site;
    }

   

    /**
     * Add batchRx
     *
     * @param \UtilBundle\Entity\XeroBatchRx $batchRx
     *
     * @return XeroBatch
     */
    public function addBatchRx(\UtilBundle\Entity\XeroBatchRx $batchRx)
    {
        $batchRx->setXeroBatch($this);
        $this->batchRxes[] = $batchRx;

        return $this;
    }

    /**
     * Remove batchRx
     *
     * @param \UtilBundle\Entity\XeroBatchRx $batchRx
     */
    public function removeBatchRx(\UtilBundle\Entity\XeroBatchRx $batchRx)
    {
        $this->batchRxes->removeElement($batchRx);
    }

    /**
     * Get batchRxes
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBatchRxes()
    {
        return $this->batchRxes;
    }
}
