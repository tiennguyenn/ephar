<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveTrackingCompleted
 *
 * @ORM\Table(name="resolve_tracking_completed", indexes={@ORM\Index(name="FK_resolve_tracking_complete", columns={"resolve_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\ResolveTrackingCompletedRepository")
 * @ORM\HasLifecycleCallbacks
 */
class ResolveTrackingCompleted
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
     *
     * @ORM\Column(name="upload_incident", type="integer", nullable=true)
     */
    private $uploadIncident = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="refund", type="integer", nullable=true)
     */
    private $refund = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="redispense", type="integer", nullable=true)
     */
    private $redispense = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="replacement_order", type="integer", nullable=true)
     */
    private $replacementOrder = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="change_delivery_address", type="integer", nullable=true)
     */
    private $changeDeliveryAddress = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="collect_destroy_parcel", type="integer", nullable=true)
     */
    private $collectDestroyParcel = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="invoice_party", type="integer", nullable=true)
     */
    private $invoiceParty = '1';

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
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;

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
     * Set uploadIncident
     *
     * @param boolean $uploadIncident
     *
     * @return ResolveTrackingCompleted
     */
    public function setUploadIncident($uploadIncident)
    {
        $this->uploadIncident = $uploadIncident;

        return $this;
    }

    /**
     * Get uploadIncident
     *
     * @return boolean
     */
    public function getUploadIncident()
    {
        return $this->uploadIncident;
    }

    /**
     * Set refund
     *
     * @param boolean $refund
     *
     * @return ResolveTrackingCompleted
     */
    public function setRefund($refund)
    {
        $this->refund = $refund;

        return $this;
    }

    /**
     * Get refund
     *
     * @return boolean
     */
    public function getRefund()
    {
        return $this->refund;
    }

    /**
     * Set redispense
     *
     * @param boolean $redispense
     *
     * @return ResolveTrackingCompleted
     */
    public function setRedispense($redispense)
    {
        $this->redispense = $redispense;

        return $this;
    }

    /**
     * Get redispense
     *
     * @return boolean
     */
    public function getRedispense()
    {
        return $this->redispense;
    }

    /**
     * Set replacementOrder
     *
     * @param boolean $replacementOrder
     *
     * @return ResolveTrackingCompleted
     */
    public function setReplacementOrder($replacementOrder)
    {
        $this->replacementOrder = $replacementOrder;

        return $this;
    }

    /**
     * Get replacementOrder
     *
     * @return boolean
     */
    public function getReplacementOrder()
    {
        return $this->replacementOrder;
    }

    /**
     * Set changeDeliveryAddress
     *
     * @param boolean $changeDeliveryAddress
     *
     * @return ResolveTrackingCompleted
     */
    public function setChangeDeliveryAddress($changeDeliveryAddress)
    {
        $this->changeDeliveryAddress = $changeDeliveryAddress;

        return $this;
    }

    /**
     * Get changeDeliveryAddress
     *
     * @return boolean
     */
    public function getChangeDeliveryAddress()
    {
        return $this->changeDeliveryAddress;
    }

    /**
     * Set collectDestroyParcel
     *
     * @param boolean $collectDestroyParcel
     *
     * @return ResolveTrackingCompleted
     */
    public function setCollectDestroyParcel($collectDestroyParcel)
    {
        $this->collectDestroyParcel = $collectDestroyParcel;

        return $this;
    }

    /**
     * Get collectDestroyParcel
     *
     * @return boolean
     */
    public function getCollectDestroyParcel()
    {
        return $this->collectDestroyParcel;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveTrackingCompleted
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
     *
     * @return ResolveTrackingCompleted
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
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveTrackingCompleted
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }

    /**
     * Set invoiceParty
     *
     * @param integer $invoiceParty
     *
     * @return ResolveTrackingCompleted
     */
    public function setInvoiceParty($invoiceParty)
    {
        $this->invoiceParty = $invoiceParty;

        return $this;
    }

    /**
     * Get invoiceParty
     *
     * @return integer
     */
    public function getInvoiceParty()
    {
        return $this->invoiceParty;
    }
}
