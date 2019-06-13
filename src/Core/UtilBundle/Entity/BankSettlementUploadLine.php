<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BankSettlementUploadLine
 *
 * @ORM\Table(name="bank_settlement_upload_line", indexes={@ORM\Index(name="FK_bank_settlement_upload_line", columns={"bank_settlement_upload_account_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\BankSettlementUploadLineRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BankSettlementUploadLine
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
     * @ORM\Column(name="post_date", type="datetime", nullable=true)
     */
    private $postDate;

    /**
     * @var string
     *
     * @ORM\Column(name="customer_reference", type="string", length=250, nullable=true)
     */
    private $customerReference;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_amount", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $transactionAmount;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_sync_summary", type="integer", nullable=true)
     */
    private $isSyncSummary = '0';

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
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status = '0';

    /**
     * @var \BankSettlementUploadAccount
     *
     * @ORM\ManyToOne(targetEntity="BankSettlementUploadAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="bank_settlement_upload_account_id", referencedColumnName="id")
     * })
     */
    private $bankSettlementUploadAccount;



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
     * Set postDate
     *
     * @param \DateTime $postDate
     *
     * @return BankSettlementUploadLine
     */
    public function setPostDate($postDate)
    {
        $this->postDate = $postDate;

        return $this;
    }

    /**
     * Get postDate
     *
     * @return \DateTime
     */
    public function getPostDate()
    {
        return $this->postDate;
    }

    /**
     * Set customerReference
     *
     * @param string $customerReference
     *
     * @return BankSettlementUploadLine
     */
    public function setCustomerReference($customerReference)
    {
        $this->customerReference = $customerReference;

        return $this;
    }

    /**
     * Get customerReference
     *
     * @return string
     */
    public function getCustomerReference()
    {
        return $this->customerReference;
    }

    /**
     * Set transactionAmount
     *
     * @param string $transactionAmount
     *
     * @return BankSettlementUploadLine
     */
    public function setTransactionAmount($transactionAmount)
    {
        $this->transactionAmount = $transactionAmount;

        return $this;
    }

    /**
     * Get transactionAmount
     *
     * @return string
     */
    public function getTransactionAmount()
    {
        return $this->transactionAmount;
    }

    /**
     * Set isSyncSummary
     *
     * @param integer $isSyncSummary
     *
     * @return BankSettlementUploadLine
     */
    public function setIsSyncSummary($isSyncSummary)
    {
        $this->isSyncSummary = $isSyncSummary;

        return $this;
    }

    /**
     * Get isSyncSummary
     *
     * @return integer
     */
    public function getIsSyncSummary()
    {
        return $this->isSyncSummary;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return BankSettlementUploadLine
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
     * @return BankSettlementUploadLine
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
     * Set status
     *
     * @param boolean $status
     *
     * @return BankSettlementUploadLine
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
     * Set bankSettlementUploadAccount
     *
     * @param \UtilBundle\Entity\BankSettlementUploadAccount $bankSettlementUploadAccount
     *
     * @return BankSettlementUploadLine
     */
    public function setBankSettlementUploadAccount(\UtilBundle\Entity\BankSettlementUploadAccount $bankSettlementUploadAccount = null)
    {
        $this->bankSettlementUploadAccount = $bankSettlementUploadAccount;

        return $this;
    }

    /**
     * Get bankSettlementUploadAccount
     *
     * @return \UtilBundle\Entity\BankSettlementUploadAccount
     */
    public function getBankSettlementUploadAccount()
    {
        return $this->bankSettlementUploadAccount;
    }
    /**
     * @ORM\PrePersist
     */
    public function setCreatedOnValue()
    {
        $this->createdOn = new \DateTime();
    }
}
