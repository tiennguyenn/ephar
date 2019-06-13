<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SequenceNumber
 *
 * @ORM\Table(name="sequence_number")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\SequenceNumberRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SequenceNumber
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
     * @ORM\Column(name="receipt", type="integer", nullable=true)
     */
    private $receipt;

    /**
     * @var integer
     *
     * @ORM\Column(name="tax_invoice", type="integer", nullable=true)
     */
    private $taxInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="gmedes_tax_invoice", type="integer", nullable=true)
     */
    private $gmedesTaxInvoice;

    /**
     * @var integer
     *
     * @ORM\Column(name="serial_number", type="integer", nullable=true)
     */
    private $serialNumber;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set receipt
     *
     * @param integer $receipt
     * @return SequenceNumber
     */
    public function setReceipt($receipt)
    {
        $this->receipt = $receipt;

        return $this;
    }

    /**
     * Get receipt
     *
     * @return integer 
     */
    public function getReceipt()
    {
        return $this->receipt;
    }

    /**
     * Set taxInvoice
     *
     * @param integer $taxInvoice
     * @return SequenceNumber
     */
    public function setGmedesTaxInvoice($gmedesTaxInvoice)
    {
        $this->gmedesTaxInvoice = $gmedesTaxInvoice;

        return $this;
    }

    /**
     * Get taxInvoice
     *
     * @return integer 
     */
    public function getGmedesTaxInvoice()
    {
        return $this->gmedesTaxInvoice;
    }

    /**
     * Set taxInvoice
     *
     * @param integer $taxInvoice
     * @return SequenceNumber
     */
    public function setTaxInvoice($taxInvoice)
    {
        $this->taxInvoice = $taxInvoice;

        return $this;
    }

    /**
     * Get taxInvoice
     *
     * @return integer 
     */
    public function getTaxInvoice()
    {
        return $this->taxInvoice;
    }

    /**
     * Set serialNumber
     *
     * @param integer $serialNumber
     * @return SequenceNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;

        return $this;
    }

    /**
     * Get serialNumber
     *
     * @return integer 
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return SequenceNumber
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
     * @return SequenceNumber
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");   
    }

    /**
     * Gets triggered every time on update

     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime("now");
    }
}
