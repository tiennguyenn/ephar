<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CourierPoDailyLine
 *
 * @ORM\Table(name="courier_po_daily_line", indexes={@ORM\Index(name="FK_courier_po_daily_line_box", columns={"box_id"}), @ORM\Index(name="FK_courier_po_daily_line_daily", columns={"po_daily_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CourierPoDailyLineRepository")
 */
class CourierPoDailyLine
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
     * @ORM\Column(name="post_code_shipping_address", type="string", length=10, nullable=true)
     */
    private $postCodeShippingAddress;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Box
     *
     * @ORM\ManyToOne(targetEntity="Box")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="box_id", referencedColumnName="id")
     * })
     */
    private $box;

    /**
     * @var \CourierPoDaily
     *
     * @ORM\ManyToOne(targetEntity="CourierPoDaily")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="po_daily_id", referencedColumnName="id")
     * })
     */
    private $poDaily;



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
     * Set postCodeShippingAddress
     *
     * @param string $postCodeShippingAddress
     * @return CourierPoDailyLine
     */
    public function setPostCodeShippingAddress($postCodeShippingAddress)
    {
        $this->postCodeShippingAddress = $postCodeShippingAddress;

        return $this;
    }

    /**
     * Get postCodeShippingAddress
     *
     * @return string 
     */
    public function getPostCodeShippingAddress()
    {
        return $this->postCodeShippingAddress;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return CourierPoDailyLine
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
     * Set box
     *
     * @param \UtilBundle\Entity\Box $box
     * @return CourierPoDailyLine
     */
    public function setBox(\UtilBundle\Entity\Box $box = null)
    {
        $this->box = $box;

        return $this;
    }

    /**
     * Get box
     *
     * @return \UtilBundle\Entity\Box 
     */
    public function getBox()
    {
        return $this->box;
    }

    /**
     * Set poDaily
     *
     * @param \UtilBundle\Entity\CourierPoDaily $poDaily
     * @return CourierPoDailyLine
     */
    public function setPoDaily(\UtilBundle\Entity\CourierPoDaily $poDaily = null)
    {
        $this->poDaily = $poDaily;

        return $this;
    }

    /**
     * Get poDaily
     *
     * @return \UtilBundle\Entity\CourierPoDaily 
     */
    public function getPoDaily()
    {
        return $this->poDaily;
    }
}
