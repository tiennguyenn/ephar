<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DrugStockStatusLog
 *
 * @ORM\Table(name="drug_stock_status_log", indexes={@ORM\Index(name="FK_drug_stock_status_log", columns={"drug_id"})})
 * @ORM\Entity
 */
class DrugStockStatusLog
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
     * @ORM\Column(name="old_stock_status", type="integer", nullable=false)
     */
    private $oldStockStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="new_stock_status", type="integer", nullable=false)
     */
    private $newStockStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;



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
     * Set oldStockStatus
     *
     * @param integer $oldStockStatus
     * @return DrugStockStatusLog
     */
    public function setOldStockStatus($oldStockStatus)
    {
        $this->oldStockStatus = $oldStockStatus;

        return $this;
    }

    /**
     * Get oldStockStatus
     *
     * @return integer 
     */
    public function getOldStockStatus()
    {
        return $this->oldStockStatus;
    }

    /**
     * Set newStockStatus
     *
     * @param integer $newStockStatus
     * @return DrugStockStatusLog
     */
    public function setNewStockStatus($newStockStatus)
    {
        $this->newStockStatus = $newStockStatus;

        return $this;
    }

    /**
     * Get newStockStatus
     *
     * @return integer 
     */
    public function getNewStockStatus()
    {
        return $this->newStockStatus;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DrugStockStatusLog
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return DrugStockStatusLog
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug 
     */
    public function getDrug()
    {
        return $this->drug;
    }
}
