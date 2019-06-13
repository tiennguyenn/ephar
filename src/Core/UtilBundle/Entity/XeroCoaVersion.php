<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * XeroCoaVersion
 *
 * @ORM\Table(name="xero_coa_version")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroCoaVersionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class XeroCoaVersion
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
     * @ORM\Column(name="effective_date", type="date", nullable=false)
     */
    private $effectiveDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sync_on", type="datetime", nullable=true)
     */
    private $syncOn;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="string", length=250, nullable=true)
     */
    private $note;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="string", length=50, nullable=false)
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="XeroAccountCode", mappedBy="xeroCoaVersion", cascade={"persist", "remove" })
     */
    private $accounts;

    /**
     * @var \Document
     *
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * })
     */
    private $document;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->accounts = new ArrayCollection();
    }


    /**
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
        $this->effectiveDate = new \DateTime(date('Y-m-d',strtotime("+1 days")));

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
     * Set effectiveDate
     *
     * @param \DateTime $effectiveDate
     *
     * @return XeroCoaVersion
     */
    public function setEffectiveDate($effectiveDate)
    {
        $this->effectiveDate = $effectiveDate;

        return $this;
    }

    /**
     * Get effectiveDate
     *
     * @return \DateTime
     */
    public function getEffectiveDate()
    {
        return $this->effectiveDate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroCoaVersion
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
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return XeroCoaVersion
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set syncOn
     *
     * @param \DateTime $syncOn
     *
     * @return XeroCoaVersion
     */
    public function setSyncOn($syncOn)
    {
        $this->syncOn = $syncOn;

        return $this;
    }

    /**
     * Get syncOn
     *
     * @return \DateTime
     */
    public function getSyncOn()
    {
        return $this->syncOn;
    }

    /**
     * Add account
     *
     * @param \UtilBundle\Entity\XeroAccountCode $account
     *
     * @return XeroCoaVersion
     */
    public function addAccount(\UtilBundle\Entity\XeroAccountCode $account)
    {
        $account->setXeroCoaVersion($this);
        $this->accounts[] = $account;

        return $this;
    }

    /**
     * Remove account
     *
     * @param \UtilBundle\Entity\XeroAccountCode $account
     */
    public function removeAccount(\UtilBundle\Entity\XeroAccountCode $account)
    {
        $this->accounts->removeElement($account);
    }

    /**
     * Get accounts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAccounts()
    {
        return $this->accounts;
    }

    /**
     * Set document
     *
     * @param \UtilBundle\Entity\Document $document
     *
     * @return XeroCoaVersion
     */
    public function setDocument(\UtilBundle\Entity\Document $document = null)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get document
     *
     * @return \UtilBundle\Entity\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return XeroCoaVersion
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
}
