<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourierRate
 *
 * @ORM\Table(name="courier_rate", indexes={@ORM\Index(name="FK_courier_rate", columns={"courier_id"}), @ORM\Index(name="FK_courier_rate_courier_rate", columns={"destination_country_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CourierRateRepository")
 * @ORM\HasLifecycleCallbacks
 */
class CourierRate
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
     * @ORM\Column(name="friendly_name", type="string", length=250, nullable=true)
     */
    private $friendlyName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=250, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="courier_rate_code", type="string", length=3, nullable=true)
     */
    private $courierRateCode;

    /**
     * @var string
     *
     * @ORM\Column(name="from_postcode", type="string", length=10, nullable=true)
     */
    private $fromPostcode;

    /**
     * @var string
     *
     * @ORM\Column(name="to_postcode", type="string", length=10, nullable=true)
     */
    private $toPostcode;

    /**
     * @var string
     *
     * @ORM\Column(name="cost", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $cost;

    /**
     * @var string
     *
     * @ORM\Column(name="new_cost", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $newCost;

    /**
     * @var string
     *
     * @ORM\Column(name="new_list", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $newList;

    /**
     * @var string
     *
     * @ORM\Column(name="list", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $list;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="rate_effect_date", type="datetime", nullable=true)
     */
    private $rateEffectDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cost_effect_date", type="datetime", nullable=true)
     */
    private $costEffectDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="list_effect_date", type="datetime", nullable=true)
     */
    private $listEffectDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ig_permit_list_effect_date", type="datetime", nullable=true)
     */
    private $igPermitListEffectDate;

    /**
     * @var string
     *
     * @ORM\Column(name="ig_permit_fee", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $igPermitFee;

    /**
     * @var string
     *
     * @ORM\Column(name="new_ig_permit_fee", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $newIgPermitFee;

    /**
     * @var string
     *
     * @ORM\Column(name="estimated_delivery_timeline", type="string", length=100, nullable=true)
     */
    private $estimatedDeliveryTimeline;

    /**
     * @var string
     *
     * @ORM\Column(name="new_collection_rate", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $newCollectionRate;

    /**
     * @var string
     *
     * @ORM\Column(name="collection_rate", type="decimal", precision=13, scale=2, nullable=true)
     */
    private $collectionRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="collection_rate_effect_date", type="datetime", nullable=true)
     */
    private $collectionRateEffectDate;

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
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="type", type="boolean", nullable=true)
     */
    private $type;

    /**
     * @var \Courier
     *
     * @ORM\ManyToOne(targetEntity="Courier")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="courier_id", referencedColumnName="id")
     * })
     */
    private $courier;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="destination_country_id", referencedColumnName="id")
     * })
     */
    private $destinationCountry;

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
     * Set friendlyName
     *
     * @param string $friendlyName
     *
     * @return CourierRate
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }

    /**
     * Get friendlyName
     *
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return CourierRate
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set courierRateCode
     *
     * @param string $courierRateCode
     *
     * @return CourierRate
     */
    public function setCourierRateCode($courierRateCode)
    {
        $this->courierRateCode = $courierRateCode;

        return $this;
    }

    /**
     * Get courierRateCode
     *
     * @return string
     */
    public function getCourierRateCode()
    {
        return $this->courierRateCode;
    }

    /**
     * Set fromPostcode
     *
     * @param string $fromPostcode
     *
     * @return CourierRate
     */
    public function setFromPostcode($fromPostcode)
    {
        $this->fromPostcode = $fromPostcode;

        return $this;
    }

    /**
     * Get fromPostcode
     *
     * @return string
     */
    public function getFromPostcode()
    {
        return $this->fromPostcode;
    }

    /**
     * Set toPostcode
     *
     * @param string $toPostcode
     *
     * @return CourierRate
     */
    public function setToPostcode($toPostcode)
    {
        $this->toPostcode = $toPostcode;

        return $this;
    }

    /**
     * Get toPostcode
     *
     * @return string
     */
    public function getToPostcode()
    {
        return $this->toPostcode;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return CourierRate
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set newCost
     *
     * @param string $newCost
     *
     * @return CourierRate
     */
    public function setNewCost($newCost)
    {
        $this->newCost = $newCost;

        return $this;
    }

    /**
     * Get newCost
     *
     * @return string
     */
    public function getNewCost()
    {
        return $this->newCost;
    }

    /**
     * Set newList
     *
     * @param string $newList
     *
     * @return CourierRate
     */
    public function setNewList($newList)
    {
        $this->newList = $newList;

        return $this;
    }

    /**
     * Get newList
     *
     * @return string
     */
    public function getNewList()
    {
        return $this->newList;
    }

    /**
     * Set list
     *
     * @param string $list
     *
     * @return CourierRate
     */
    public function setList($list)
    {
        $this->list = $list;

        return $this;
    }

    /**
     * Get list
     *
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set rateEffectDate
     *
     * @param \DateTime $rateEffectDate
     *
     * @return CourierRate
     */
    public function setRateEffectDate($rateEffectDate)
    {
        $this->rateEffectDate = $rateEffectDate;

        return $this;
    }

    /**
     * Get rateEffectDate
     *
     * @return \DateTime
     */
    public function getRateEffectDate()
    {
        return $this->rateEffectDate;
    }

    /**
     * Set costEffectDate
     *
     * @param \DateTime $costEffectDate
     *
     * @return CourierRate
     */
    public function setCostEffectDate($costEffectDate)
    {
        $this->costEffectDate = $costEffectDate;

        return $this;
    }

    /**
     * Get costEffectDate
     *
     * @return \DateTime
     */
    public function getCostEffectDate()
    {
        return $this->costEffectDate;
    }

    /**
     * Set listEffectDate
     *
     * @param \DateTime $listEffectDate
     *
     * @return CourierRate
     */
    public function setListEffectDate($listEffectDate)
    {
        $this->listEffectDate = $listEffectDate;

        return $this;
    }

    /**
     * Get listEffectDate
     *
     * @return \DateTime
     */
    public function getListEffectDate()
    {
        return $this->listEffectDate;
    }

    /**
     * Set igPermitListEffectDate
     *
     * @param \DateTime $igPermitListEffectDate
     *
     * @return CourierRate
     */
    public function setIgPermitListEffectDate($igPermitListEffectDate)
    {
        $this->igPermitListEffectDate = $igPermitListEffectDate;

        return $this;
    }

    /**
     * Get igPermitListEffectDate
     *
     * @return \DateTime
     */
    public function getIgPermitListEffectDate()
    {
        return $this->igPermitListEffectDate;
    }

    /**
     * Set igPermitFee
     *
     * @param string $igPermitFee
     *
     * @return CourierRate
     */
    public function setIgPermitFee($igPermitFee)
    {
        $this->igPermitFee = $igPermitFee;

        return $this;
    }

    /**
     * Get igPermitFee
     *
     * @return string
     */
    public function getIgPermitFee()
    {
        return $this->igPermitFee;
    }

    /**
     * Set newIgPermitFee
     *
     * @param string $newIgPermitFee
     *
     * @return CourierRate
     */
    public function setNewIgPermitFee($newIgPermitFee)
    {
        $this->newIgPermitFee = $newIgPermitFee;

        return $this;
    }

    /**
     * Get newIgPermitFee
     *
     * @return string
     */
    public function getNewIgPermitFee()
    {
        return $this->newIgPermitFee;
    }

    /**
     * Set estimatedDeliveryTimeline
     *
     * @param string $estimatedDeliveryTimeline
     *
     * @return CourierRate
     */
    public function setEstimatedDeliveryTimeline($estimatedDeliveryTimeline)
    {
        $this->estimatedDeliveryTimeline = $estimatedDeliveryTimeline;

        return $this;
    }

    /**
     * Get estimatedDeliveryTimeline
     *
     * @return string
     */
    public function getEstimatedDeliveryTimeline()
    {
        return $this->estimatedDeliveryTimeline;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return CourierRate
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
     * @return CourierRate
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return CourierRate
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
     * Set courier
     *
     * @param \UtilBundle\Entity\Courier $courier
     *
     * @return CourierRate
     */
    public function setCourier(\UtilBundle\Entity\Courier $courier = null)
    {
        $this->courier = $courier;

        return $this;
    }

    /**
     * Get courier
     *
     * @return \UtilBundle\Entity\Courier
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * Set destinationCountry
     *
     * @param \UtilBundle\Entity\Country $destinationCountry
     *
     * @return CourierRate
     */
    public function setDestinationCountry(\UtilBundle\Entity\Country $destinationCountry = null)
    {
        $this->destinationCountry = $destinationCountry;

        return $this;
    }

    /**
     * Get destinationCountry
     *
     * @return \UtilBundle\Entity\Country
     */
    public function getDestinationCountry()
    {
        return $this->destinationCountry;
    }

    /**
     * Set newCollectionRate
     *
     * @param string $newCollectionRate
     *
     * @return CourierRate
     */
    public function setNewCollectionRate($newCollectionRate)
    {
        $this->newCollectionRate = $newCollectionRate;

        return $this;
    }

    /**
     * Get newCollectionRate
     *
     * @return string
     */
    public function getNewCollectionRate()
    {
        return $this->newCollectionRate;
    }

    /**
     * Set collectionRate
     *
     * @param string $collectionRate
     *
     * @return CourierRate
     */
    public function setCollectionRate($collectionRate)
    {
        $this->collectionRate = $collectionRate;

        return $this;
    }

    /**
     * Get collectionRate
     *
     * @return string
     */
    public function getCollectionRate()
    {
        return $this->collectionRate;
    }

    /**
     * Set collectionRateEffectDate
     *
     * @param \DateTime $collectionRateEffectDate
     *
     * @return CourierRate
     */
    public function setCollectionRateEffectDate($collectionRateEffectDate)
    {
        $this->collectionRateEffectDate = $collectionRateEffectDate;

        return $this;
    }

    /**
     * Get collectionRateEffectDate
     *
     * @return \DateTime
     */
    public function getCollectionRateEffectDate()
    {
        return $this->collectionRateEffectDate;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }
}
