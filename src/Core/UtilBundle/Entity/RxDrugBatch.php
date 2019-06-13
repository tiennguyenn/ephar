<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxDrugBatch
 *
 * @ORM\Table(name="rx_drug_batch", indexes={@ORM\Index(name="batch_id", columns={"batch_id"}), @ORM\Index(name="FK_rx_drug_batch_rx_line", columns={"rx_line_id"})})
 * @ORM\Entity
 */
class RxDrugBatch
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
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     */
    private $quantity;

    /**
     * @var \RxLine
     *
     * @ORM\ManyToOne(targetEntity="RxLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_line_id", referencedColumnName="id")
     * })
     */
    private $rxLine;

    /**
     * @var \Batch
     *
     * @ORM\ManyToOne(targetEntity="Batch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="batch_id", referencedColumnName="id")
     * })
     */
    private $batch;



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
     * Set quantity
     *
     * @param integer $quantity
     * @return RxDrugBatch
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set rxLine
     *
     * @param \UtilBundle\Entity\RxLine $rxLine
     * @return RxDrugBatch
     */
    public function setRxLine(\UtilBundle\Entity\RxLine $rxLine = null)
    {
        $this->rxLine = $rxLine;

        return $this;
    }

    /**
     * Get rxLine
     *
     * @return \UtilBundle\Entity\RxLine 
     */
    public function getRxLine()
    {
        return $this->rxLine;
    }

    /**
     * Set batch
     *
     * @param \UtilBundle\Entity\Batch $batch
     * @return RxDrugBatch
     */
    public function setBatch(\UtilBundle\Entity\Batch $batch = null)
    {
        $this->batch = $batch;

        return $this;
    }

    /**
     * Get batch
     *
     * @return \UtilBundle\Entity\Batch 
     */
    public function getBatch()
    {
        return $this->batch;
    }
}
