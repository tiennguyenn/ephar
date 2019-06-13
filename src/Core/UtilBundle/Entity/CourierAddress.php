<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourierAddress
 *
 * @ORM\Table(name="courier_address", indexes={@ORM\Index(name="FK_courier_address_courier", columns={"courier_id"}), @ORM\Index(name="FK_courier_address_1", columns={"address_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class CourierAddress
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
     * @var \Address
     *
     * @ORM\ManyToOne(targetEntity="Address")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="address_id", referencedColumnName="id")
     * })
     */
    private $address;

    /**
     * @var \Courier
     *
     * @ORM\ManyToOne(targetEntity="Courier", inversedBy="courierAddresses", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="courier_id", referencedColumnName="id")
     * })
     */
    private $courier;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return CourierAddress
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
     * @return CourierAddress
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
     * Set address
     *
     * @param \UtilBundle\Entity\Address $address
     * @return CourierAddress
     */
    public function setAddress(\UtilBundle\Entity\Address $address = null)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return \UtilBundle\Entity\Address 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set courier
     *
     * @param \UtilBundle\Entity\Courier $courier
     * @return CourierAddress
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
