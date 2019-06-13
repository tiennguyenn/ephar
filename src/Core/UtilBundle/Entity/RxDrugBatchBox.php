<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxDrugBatchBox
 *
 * @ORM\Table(name="rx_drug_batch_box", indexes={@ORM\Index(name="rx_drug_batch_id", columns={"rx_drug_batch_id"}), @ORM\Index(name="box_id", columns={"box_id"})})
 * @ORM\Entity
 */
class RxDrugBatchBox
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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Box
     *
     * @ORM\ManyToOne(targetEntity="Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     * })
     */
    private $box;

    /**
     * @var \RxDrugBatch
     *
     * @ORM\ManyToOne(targetEntity="RxDrugBatch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_drug_batch_id", referencedColumnName="id")
     * })
     */
    private $rxDrugBatch;



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
     * @return RxDrugBatchBox
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return RxDrugBatchBox
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

    /**
     * Set box
     *
     * @param \UtilBundle\Entity\Box $box
     * @return RxDrugBatchBox
     */
    public function setBox(\UtilBundle\Entity\Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \UtilBundle\Entity\Box 
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Set rxDrugBatch
     *
     * @param \UtilBundle\Entity\RxDrugBatch $rxDrugBatch
     * @return RxDrugBatchBox
     */
    public function setRxDrugBatch(\UtilBundle\Entity\RxDrugBatch $rxDrugBatch = null)
    {
        $this->rxDrugBatch = $rxDrugBatch;

        return $this;
    }

    /**
     * Get rxDrugBatch
     *
     * @return \UtilBundle\Entity\RxDrugBatch 
     */
    public function getRxDrugBatch()
    {
        return $this->rxDrugBatch;
    }
}
