<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DrugPriceLog
 *
 * @ORM\Table(name="drug_price_log")
 * @ORM\Entity
 */
class DrugPriceLog
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
     * @ORM\Column(name="drug_id", type="integer", nullable=false)
     */
    private $drugId;

    /**
     * @var string
     *
     * @ORM\Column(name="old_cost_price", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $oldCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="old_list_price_domestic", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $oldListPriceDomestic;

    /**
     * @var string
     *
     * @ORM\Column(name="old_list_price_international", type="decimal", precision=13, scale=4, nullable=true)
     */
    private $oldListPriceInternational;

    /**
     * @var integer
     *
     * @ORM\Column(name="old_stock_status", type="integer", nullable=false)
     */
    private $oldStockStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="new_cost_price", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $newCostPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="new_list_price_domestic", type="decimal", precision=18, scale=2, nullable=false)
     */
    private $newListPriceDomestic;

    /**
     * @var string
     *
     * @ORM\Column(name="new_list_price_international", type="decimal", precision=13, scale=4, nullable=true)
     */
    private $newListPriceInternational;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set drugId
     *
     * @param integer $drugId
     * @return DrugPriceLog
     */
    public function setDrugId($drugId)
    {
        $this->drugId = $drugId;

        return $this;
    }

    /**
     * Get drugId
     *
     * @return integer 
     */
    public function getDrugId()
    {
        return $this->drugId;
    }

    /**
     * Set oldCostPrice
     *
     * @param string $oldCostPrice
     * @return DrugPriceLog
     */
    public function setOldCostPrice($oldCostPrice)
    {
        $this->oldCostPrice = $oldCostPrice;

        return $this;
    }

    /**
     * Get oldCostPrice
     *
     * @return string 
     */
    public function getOldCostPrice()
    {
        return $this->oldCostPrice;
    }

    /**
     * Set oldListPriceDomestic
     *
     * @param string $oldListPriceDomestic
     * @return DrugPriceLog
     */
    public function setOldListPriceDomestic($oldListPriceDomestic)
    {
        $this->oldListPriceDomestic = $oldListPriceDomestic;

        return $this;
    }

    /**
     * Get oldListPriceDomestic
     *
     * @return string 
     */
    public function getOldListPriceDomestic()
    {
        return $this->oldListPriceDomestic;
    }

    /**
     * Set oldListPriceInternational
     *
     * @param string $oldListPriceInternational
     * @return DrugPriceLog
     */
    public function setOldListPriceInternational($oldListPriceInternational)
    {
        $this->oldListPriceInternational = $oldListPriceInternational;

        return $this;
    }

    /**
     * Get oldListPriceInternational
     *
     * @return string 
     */
    public function getOldListPriceInternational()
    {
        return $this->oldListPriceInternational;
    }

    /**
     * Set oldStockStatus
     *
     * @param integer $oldStockStatus
     * @return DrugPriceLog
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
     * Set newCostPrice
     *
     * @param string $newCostPrice
     * @return DrugPriceLog
     */
    public function setNewCostPrice($newCostPrice)
    {
        $this->newCostPrice = $newCostPrice;

        return $this;
    }

    /**
     * Get newCostPrice
     *
     * @return string 
     */
    public function getNewCostPrice()
    {
        return $this->newCostPrice;
    }

    /**
     * Set newListPriceDomestic
     *
     * @param string $newListPriceDomestic
     * @return DrugPriceLog
     */
    public function setNewListPriceDomestic($newListPriceDomestic)
    {
        $this->newListPriceDomestic = $newListPriceDomestic;

        return $this;
    }

    /**
     * Get newListPriceDomestic
     *
     * @return string 
     */
    public function getNewListPriceDomestic()
    {
        return $this->newListPriceDomestic;
    }

    /**
     * Set newListPriceInternational
     *
     * @param string $newListPriceInternational
     * @return DrugPriceLog
     */
    public function setNewListPriceInternational($newListPriceInternational)
    {
        $this->newListPriceInternational = $newListPriceInternational;

        return $this;
    }

    /**
     * Get newListPriceInternational
     *
     * @return string 
     */
    public function getNewListPriceInternational()
    {
        return $this->newListPriceInternational;
    }

    /**
     * Set newStockStatus
     *
     * @param integer $newStockStatus
     * @return DrugPriceLog
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
     * @return DrugPriceLog
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
}
