<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroBatchRx
 *
 * @ORM\Table(name="xero_batch_rx", indexes={@ORM\Index(name="FK_batch_transaction_listing_1", columns={"xero_batch_id"}), @ORM\Index(name="FK_xero_batch_transaction_listing", columns={"rx_id"})})
 * @ORM\Entity
 */
class XeroBatchRx
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
     * @var \XeroBatch
     *
     * @ORM\ManyToOne(targetEntity="XeroBatch")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_batch_id", referencedColumnName="id")
     * })
     */
    private $xeroBatch;

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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroBatchRx
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
     * Set xeroBatch
     *
     * @param \UtilBundle\Entity\XeroBatch $xeroBatch
     *
     * @return XeroBatchRx
     */
    public function setXeroBatch(\UtilBundle\Entity\XeroBatch $xeroBatch = null)
    {
        $this->xeroBatch = $xeroBatch;

        return $this;
    }

    /**
     * Get xeroBatch
     *
     * @return \UtilBundle\Entity\XeroBatch
     */
    public function getXeroBatch()
    {
        return $this->xeroBatch;
    }

    /**
     * Set rx
     *
     * @param \UtilBundle\Entity\Rx $rx
     *
     * @return XeroBatchRx
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
