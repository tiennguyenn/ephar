<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxService
 *
 * @ORM\Table(name="rx_service", indexes={@ORM\Index(name="rx_id", columns={"rx_id"}), @ORM\Index(name="service_id", columns={"service_id"})})
 * @ORM\Entity
 */
class RxService
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
     * @ORM\Column(name="cost_price", type="decimal", precision=13, scale=4, nullable=false)
     */
    private $costPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price", type="decimal", precision=13, scale=4, nullable=false)
     */
    private $listPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price_gst", type="decimal", precision=13, scale=4, nullable=false)
     */
    private $costPriceGst;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price_gst", type="decimal", precision=13, scale=4, nullable=false)
     */
    private $listPriceGst;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
     */
    private $updatedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \Service
     *
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     * })
     */
    private $service;

    /**
     * @var \Rx
     *
     * @ORM\ManyToOne(targetEntity="Rx")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_id", referencedColumnName="id")
     * })
     */
    private $rx;



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
     * Set costPrice
     *
     * @param string $costPrice
     * @return RxService
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get costPrice
     *
     * @return string 
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * Set listPrice
     *
     * @param string $listPrice
     * @return RxService
     */
    public function setListPrice($listPrice)
    {
        $this->listPrice = $listPrice;

        return $this;
    }

    /**
     * Get listPrice
     *
     * @return string 
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * Set costPriceGst
     *
     * @param string $costPriceGst
     * @return RxService
     */
    public function setCostPriceGst($costPriceGst)
    {
        $this->costPriceGst = $costPriceGst;

        return $this;
    }

    /**
     * Get costPriceGst
     *
     * @return string 
     */
    public function getCostPriceGst()
    {
        return $this->costPriceGst;
    }

    /**
     * Set listPriceGst
     *
     * @param string $listPriceGst
     * @return RxService
     */
    public function setListPriceGst($listPriceGst)
    {
        $this->listPriceGst = $listPriceGst;

        return $this;
    }

    /**
     * Get listPriceGst
     *
     * @return string 
     */
    public function getListPriceGst()
    {
        return $this->listPriceGst;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return RxService
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
     * @return RxService
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
     * @return RxService
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
     * Set service
     *
     * @param \UtilBundle\Entity\Service $service
     * @return RxService
     */
    public function setService(\UtilBundle\Entity\Service $service = null)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return \UtilBundle\Entity\Service 
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     * @return RxService
     */
    public function setRx(\UtilBundle\Entity\Rx $rx = null)
    {
        $this->rx = $rx;

        return $this;
    }

    /**
     * Get rx
     *
     * @return \UtilBundle\Entity\Rx 
     */
    public function getRx()
    {
        return $this->rx;
    }
}
