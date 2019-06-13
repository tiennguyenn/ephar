<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveDispenseDrug
 *
 * @ORM\Table(name="resolve_dispense_drug", indexes={@ORM\Index(name="FK_resolve_dispense_drug", columns={"resolve_redispense_id"})})
 * @ORM\Entity
 */
class ResolveDispenseDrug
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
     * @ORM\Column(name="rx_line_id", type="integer", nullable=false)
     */
    private $rxLineId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \ResolveRedispense
     *
     * @ORM\ManyToOne(targetEntity="ResolveRedispense")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_redispense_id", referencedColumnName="id")
     * })
     */
    private $resolveRedispense;



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
     * Set rxLineId
     *
     * @param integer $rxLineId
     *
     * @return ResolveDispenseDrug
     */
    public function setRxLineId($rxLineId)
    {
        $this->rxLineId = $rxLineId;

        return $this;
    }

    /**
     * Get rxLineId
     *
     * @return integer
     */
    public function getRxLineId()
    {
        return $this->rxLineId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveDispenseDrug
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
     * Set resolveRedispense
     *
     * @param \UtilBundle\Entity\ResolveRedispense $resolveRedispense
     *
     * @return ResolveDispenseDrug
     */
    public function setResolveRedispense(\UtilBundle\Entity\ResolveRedispense $resolveRedispense = null)
    {
        $this->resolveRedispense = $resolveRedispense;

        return $this;
    }

    /**
     * Get resolveRedispense
     *
     * @return \UtilBundle\Entity\ResolveRedispense
     */
    public function getResolveRedispense()
    {
        return $this->resolveRedispense;
    }
}
