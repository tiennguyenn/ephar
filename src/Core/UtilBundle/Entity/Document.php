<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Document
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
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=250, nullable=true)
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="ResolveRefund", mappedBy="creditNotes", cascade={"persist", "remove" })
     * @ORM\JoinTable(name="resolve_refund_credit_note")
     *
     */
    private $refunds;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->refunds = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return Document
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set url
     *
     * @param string $url
     * @return Document
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Document
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Document
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    /**
     * Add refund
     *
     * @param \UtilBundle\Entity\ResolveRefund $refund
     *
     * @return Document
     */
    public function addRefund(\UtilBundle\Entity\ResolveRefund $refund)
    {
        $this->refunds[] = $refund;

        return $this;
    }

    /**
     * Remove refund
     *
     * @param \UtilBundle\Entity\ResolveRefund $refund
     */
    public function removeRefund(\UtilBundle\Entity\ResolveRefund $refund)
    {
        $this->refunds->removeElement($refund);
    }

    /**
     * Get refunds
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRefunds()
    {
        return $this->refunds;
    }
}
