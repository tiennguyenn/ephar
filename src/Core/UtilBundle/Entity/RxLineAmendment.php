<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RxLineAmendment
 *
 * @ORM\Table(name="rx_line_amendment", uniqueConstraints={@ORM\UniqueConstraint(name="id", columns={"id"})}, indexes={@ORM\Index(name="FK_rx_line_amendment", columns={"rx_line_id"})})
 * @ORM\Entity
 */
class RxLineAmendment
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
     * @ORM\Column(name="amendment", type="text", length=65535, nullable=true)
     */
    private $amendment;

    /**
     * @var string
     *
     * @ORM\Column(name="created_by", type="string", length=250, nullable=true)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \RxLine
     *
     * @ORM\ManyToOne(targetEntity="RxLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rx_line_id", referencedColumnName="id")
     * })
     */
    private $rxLine;



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
     * Set amendment
     *
     * @param string $amendment
     *
     * @return RxLineAmendment
     */
    public function setAmendment($amendment)
    {
        $this->amendment = $amendment;

        return $this;
    }

    /**
     * Get amendment
     *
     * @return string
     */
    public function getAmendment()
    {
        return $this->amendment;
    }

    /**
     * Set createdBy
     *
     * @param string $createdBy
     *
     * @return RxLineAmendment
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return string
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return RxLineAmendment
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
     * Set rxLine
     *
     * @param \UtilBundle\Entity\RxLine $rxLine
     *
     * @return RxLineAmendment
     */
    public function setRxLine(\UtilBundle\Entity\RxLine $rxLine = null)
    {
        $this->rxLine = $rxLine;

        return $this;
    }

    /**
     * Get rxLine
     *
     * @return \UtilBundle\Entity\RxLine
     */
    public function getRxLine()
    {
        return $this->rxLine;
    }
}
