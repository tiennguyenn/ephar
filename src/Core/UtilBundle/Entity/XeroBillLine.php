<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * XeroBillLine
 *
 * @ORM\Table(name="xero_bill_line", indexes={@ORM\Index(name="xero_sale_id", columns={"xero_bill_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class XeroBillLine
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
     * @ORM\Column(name="gmeds_code", type="string", length=50, nullable=false)
     */
    private $gmedsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=240, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="amount", type="decimal", precision=13, scale=2, nullable=false)
     */
    private $amount;

    /**
     * @var \XeroBill
     *
     * @ORM\ManyToOne(targetEntity="XeroBill", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_bill_id", referencedColumnName="id")
     * })
     */
    private $xeroBill;

    /**
     * @var \XeroMapping
     *
     * @ORM\ManyToOne(targetEntity="XeroMapping")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_mapping_id", referencedColumnName="id")
     * })
     */
    private $xeroMapping;

    /**
     * @ORM\OneToMany(targetEntity="XeroBillLineSource", mappedBy="xeroBillLine", cascade={"persist", "remove" })
     */
    private $sources;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sources = new ArrayCollection();
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set gmedsCode
     *
     * @param string $gmedsCode
     *
     * @return XeroBillLine
     */
    public function setGmedsCode($gmedsCode)
    {
        $this->gmedsCode = $gmedsCode;

        return $this;
    }

    /**
     * Get gmedsCode
     *
     * @return string
     */
    public function getGmedsCode()
    {
        return $this->gmedsCode;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return XeroBillLine
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set amount
     *
     * @param string $amount
     *
     * @return XeroBillLine
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set xeroBill
     *
     * @param \UtilBundle\Entity\XeroBill $xeroBill
     *
     * @return XeroBillLine
     */
    public function setXeroBill(\UtilBundle\Entity\XeroBill $xeroBill = null)
    {
        $this->xeroBill = $xeroBill;

        return $this;
    }

    /**
     * Get xeroBill
     *
     * @return \UtilBundle\Entity\XeroBill
     */
    public function getXeroBill()
    {
        return $this->xeroBill;
    }

    /**
     * Add source
     *
     * @param \UtilBundle\Entity\XeroBillLineSource $source
     *
     * @return XeroBillLine
     */
    public function addSource(\UtilBundle\Entity\XeroBillLineSource $source)
    {
        $source->setXeroBillLine($this);
        $this->sources[] = $source;

        return $this;
    }

    /**
     * Remove source
     *
     * @param \UtilBundle\Entity\XeroBillLineSource $source
     */
    public function removeSource(\UtilBundle\Entity\XeroBillLineSource $source)
    {
        $this->sources->removeElement($source);
    }

    /**
     * Get sources
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * Set xeroMapping
     *
     * @param \UtilBundle\Entity\XeroMapping $xeroMapping
     *
     * @return XeroBillLine
     */
    public function setXeroMapping(\UtilBundle\Entity\XeroMapping $xeroMapping = null)
    {
        $this->xeroMapping = $xeroMapping;

        return $this;
    }

    /**
     * Get xeroMapping
     *
     * @return \UtilBundle\Entity\XeroMapping
     */
    public function getXeroMapping()
    {
        return $this->xeroMapping;
    }
}
