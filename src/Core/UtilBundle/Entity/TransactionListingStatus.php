<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionListingStatus
 *
 * @ORM\Table(name="transaction_listing_status", indexes={@ORM\Index(name="FK_transaction_listing_status", columns={"transaction_listing_id"})})
 * @ORM\Entity
 */
class TransactionListingStatus
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
     * @var boolean
     *
     * @ORM\Column(name="batch_status", type="boolean", nullable=true)
     */
    private $batchStatus;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sync_status", type="boolean", nullable=true)
     */
    private $syncStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \TransactionListing
     *
     * @ORM\ManyToOne(targetEntity="TransactionListing")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transaction_listing_id", referencedColumnName="id")
     * })
     */
    private $transactionListing;



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
     * Set batchStatus
     *
     * @param boolean $batchStatus
     *
     * @return TransactionListingStatus
     */
    public function setBatchStatus($batchStatus)
    {
        $this->batchStatus = $batchStatus;

        return $this;
    }

    /**
     * Get batchStatus
     *
     * @return boolean
     */
    public function getBatchStatus()
    {
        return $this->batchStatus;
    }

    /**
     * Set syncStatus
     *
     * @param boolean $syncStatus
     *
     * @return TransactionListingStatus
     */
    public function setSyncStatus($syncStatus)
    {
        $this->syncStatus = $syncStatus;

        return $this;
    }

    /**
     * Get syncStatus
     *
     * @return boolean
     */
    public function getSyncStatus()
    {
        return $this->syncStatus;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return TransactionListingStatus
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
     * Set transactionListing
     *
     * @param \UtilBundle\Entity\TransactionListing $transactionListing
     *
     * @return TransactionListingStatus
     */
    public function setTransactionListing(\UtilBundle\Entity\TransactionListing $transactionListing = null)
    {
        $this->transactionListing = $transactionListing;

        return $this;
    }

    /**
     * Get transactionListing
     *
     * @return \UtilBundle\Entity\TransactionListing
     */
    public function getTransactionListing()
    {
        return $this->transactionListing;
    }
}
