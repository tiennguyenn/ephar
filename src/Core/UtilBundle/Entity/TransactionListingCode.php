<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TransactionListingCode
 *
 * @ORM\Table(name="transaction_listing_code", indexes={@ORM\Index(name="FK_transaction_listing_code", columns={"transaction_listing_id"})})
 * @ORM\Entity
 */
class TransactionListingCode
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
     * @ORM\Column(name="gmedes_code", type="string", length=20, nullable=true)
     */
    private $gmedesCode;

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
     * Set gmedesCode
     *
     * @param string $gmedesCode
     *
     * @return TransactionListingCode
     */
    public function setGmedesCode($gmedesCode)
    {
        $this->gmedesCode = $gmedesCode;

        return $this;
    }

    /**
     * Get gmedesCode
     *
     * @return string
     */
    public function getGmedesCode()
    {
        return $this->gmedesCode;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return TransactionListingCode
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
     * @return TransactionListingCode
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
