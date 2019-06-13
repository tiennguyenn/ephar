<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BankSettlementUploadAccount
 *
 * @ORM\Table(name="bank_settlement_upload_account", indexes={@ORM\Index(name="FK_bank_settlement_upload_account", columns={"bank_settlement_upload_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class BankSettlementUploadAccount
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
     * @ORM\Column(name="currency", type="string", length=5, nullable=true)
     */
    private $currency;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="from_date", type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="to_date", type="date", nullable=true)
     */
    private $toDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \BankSettlementUpload
     *
     * @ORM\ManyToOne(targetEntity="BankSettlementUpload")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_settlement_upload_id", referencedColumnName="id")
     * })
     */
    private $bankSettlementUpload;

    /**
     * @ORM\OneToMany(targetEntity="BankSettlementUploadLine", mappedBy="bankSettlementUploadAccount", cascade={"persist", "remove" })
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return BankSettlementUploadAccount
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return BankSettlementUploadAccount
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     *
     * @return BankSettlementUploadAccount
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     *
     * @return BankSettlementUploadAccount
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return BankSettlementUploadAccount
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
     *
     * @return BankSettlementUploadAccount
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
     * Set bankSettlementUpload
     *
     * @param \UtilBundle\Entity\BankSettlementUpload $bankSettlementUpload
     *
     * @return BankSettlementUploadAccount
     */
    public function setBankSettlementUpload(\UtilBundle\Entity\BankSettlementUpload $bankSettlementUpload = null)
    {
        $this->bankSettlementUpload = $bankSettlementUpload;

        return $this;
    }

    /**
     * Get bankSettlementUpload
     *
     * @return \UtilBundle\Entity\BankSettlementUpload
     */
    public function getBankSettlementUpload()
    {
        return $this->bankSettlementUpload;
    }

    /**
     * Add line
     *
     * @param \UtilBundle\Entity\BankSettlementUploadLine $line
     *
     * @return BankSettlementUploadAccount
     */
    public function addLine(\UtilBundle\Entity\BankSettlementUploadLine $line)
    {
        $line->setBankSettlementUploadAccount($this);
        $this->lines[] = $line;

        return $this;
    }

    /**
     * Remove line
     *
     * @param \UtilBundle\Entity\BankSettlementUploadLine $line
     */
    public function removeLine(\UtilBundle\Entity\BankSettlementUploadLine $line)
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

    /**
     * @ORM\PrePersist
     */
    public function setCreatedOnValue()
    {
        $this->createdOn = new \DateTime();
    }
}
