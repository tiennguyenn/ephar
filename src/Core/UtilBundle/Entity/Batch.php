<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Batch
 *
 * @ORM\Table(name="batch", indexes={@ORM\Index(name="drug_id", columns={"drug_id"})})
 * @ORM\Entity
 */
class Batch
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
     * @ORM\Column(name="batch_number", type="string", length=100, nullable=false)
     */
    private $batchNumber;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiry_date", type="datetime", nullable=false)
     */
    private $expiryDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

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
     * Set batchNumber
     *
     * @param string $batchNumber
     * @return Batch
     */
    public function setBatchNumber($batchNumber)
    {
        $this->batchNumber = $batchNumber;

        return $this;
    }

    /**
     * Get batchNumber
     *
     * @return string 
     */
    public function getBatchNumber()
    {
        return $this->batchNumber;
    }

    /**
     * Set expiryDate
     *
     * @param \DateTime $expiryDate
     * @return Batch
     */
    public function setExpiryDate($expiryDate)
    {
        $this->expiryDate = $expiryDate;

        return $this;
    }

    /**
     * Get expiryDate
     *
     * @return \DateTime 
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Batch
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
     * @return Batch
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return Batch
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
