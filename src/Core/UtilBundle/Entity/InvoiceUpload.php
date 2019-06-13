<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InvoiceUpload
 *
 * @ORM\Table(name="invoice_upload")
 * @ORM\Entity
 */
class InvoiceUpload
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
     * @ORM\Column(name="invoice_number", type="string", length=20, nullable=true)
     */
    private $invoiceNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $invoiceAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="gst", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $gst;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_total_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $invoiceTotalAmount;

    /**
     * @var string
     *
     * @ORM\Column(name="document_url", type="string", length=250, nullable=true)
     */
    private $documentUrl;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="updated_by", type="string", length=250, nullable=true)
     */
    private $updatedBy;
    
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="receive_date", type="date", nullable=true)
     */
    private $receiveDate;

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
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     * @return InvoiceUpload
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string 
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * Set invoiceAmount
     *
     * @param string $invoiceAmount
     * @return InvoiceUpload
     */
    public function setInvoiceAmount($invoiceAmount)
    {
        $this->invoiceAmount = $invoiceAmount;

        return $this;
    }

    /**
     * Get invoiceAmount
     *
     * @return string 
     */
    public function getInvoiceAmount()
    {
        return $this->invoiceAmount;
    }

    /**
     * Set gst
     *
     * @param string $gst
     * @return InvoiceUpload
     */
    public function setGst($gst)
    {
        $this->gst = $gst;

        return $this;
    }

    /**
     * Get gst
     *
     * @return string 
     */
    public function getGst()
    {
        return $this->gst;
    }

    /**
     * Set invoiceTotalAmount
     *
     * @param string $invoiceTotalAmount
     * @return InvoiceUpload
     */
    public function setInvoiceTotalAmount($invoiceTotalAmount)
    {
        $this->invoiceTotalAmount = $invoiceTotalAmount;

        return $this;
    }

    /**
     * Get invoiceTotalAmount
     *
     * @return string 
     */
    public function getInvoiceTotalAmount()
    {
        return $this->invoiceTotalAmount;
    }

    /**
     * Set documentUrl
     *
     * @param string $documentUrl
     * @return InvoiceUpload
     */
    public function setDocumentUrl($documentUrl)
    {
        $this->documentUrl = $documentUrl;

        return $this;
    }

    /**
     * Get documentUrl
     *
     * @return string 
     */
    public function getDocumentUrl()
    {
        return $this->documentUrl;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return InvoiceUpload
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
     * Set updatedBy
     *
     * @param string $updatedBy
     * @return InvoiceUpload
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return string 
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }
    
    /**
     * Set receiveDate
     *
     * @param \DateTime $receiveDate
     * @return InvoiceUpload
     */
    public function setReceiveDate($receiveDate)
    {
        $this->receiveDate = $receiveDate;

        return $this;
    }

    /**
     * Get receiveDate
     *
     * @return \DateTime 
     */
    public function getReceiveDate()
    {
        return $this->receiveDate;
    }
}
