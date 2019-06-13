<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * BankSettlementUpload
 *
 * @ORM\Table(name="bank_settlement_upload")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\BankSettlementUploadRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class BankSettlementUpload
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
     * @ORM\Column(name="uploaded_by", type="string", length=250, nullable=true)
     */
    private $uploadedBy;

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
     * @ORM\OneToMany(targetEntity="BankSettlementUploadAccount", mappedBy="bankSettlementUpload", cascade={"persist", "remove" })
     */
    private $accounts;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
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
     * Set uploadedBy
     *
     * @param string $uploadedBy
     *
     * @return BankSettlementUpload
     */
    public function setUploadedBy($uploadedBy)
    {
        $this->uploadedBy = $uploadedBy;

        return $this;
    }

    /**
     * Get uploadedBy
     *
     * @return string
     */
    public function getUploadedBy()
    {
        return $this->uploadedBy;
    }

    /**
     * Set documentUrl
     *
     * @param string $documentUrl
     *
     * @return BankSettlementUpload
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
     *
     * @return BankSettlementUpload
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
     * @ORM\PrePersist
     */
    public function setCreatedOnValue()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Add account
     *
     * @param \UtilBundle\Entity\BankSettlementUploadAccount $account
     *
     * @return BankSettlementUpload
     */
    public function addAccount(\UtilBundle\Entity\BankSettlementUploadAccount $account)
    {
        $account->setBankSettlementUpload($this);
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account
     *
     * @param \UtilBundle\Entity\BankSettlementUploadAccount $account
     */
    public function removeAccount(\UtilBundle\Entity\BankSettlementUploadAccount $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }
}
