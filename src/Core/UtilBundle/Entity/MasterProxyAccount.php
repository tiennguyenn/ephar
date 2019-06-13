<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
/**
 * MasterProxyAccount
 *
 * @ORM\Table(name="master_proxy_account", uniqueConstraints={@ORM\UniqueConstraint(name="FK_master_proxy_account", columns={"document_id"})}, indexes={@ORM\Index(name="FK_master_proxy_account_user", columns={"user_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MasterProxyAccountRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MasterProxyAccount
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
     * @ORM\Column(name="mpa_code", type="string", length=20, nullable=true)
     */
    private $mpaCode;

    /**
     * @var string
     *
     * @ORM\Column(name="clinic_name", type="string", length=250, nullable=true)
     */
    private $clinicName;

    /**
     * @var string
     *
     * @ORM\Column(name="email_address", type="string", length=250, nullable=true)
     */
    private $emailAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="family_name", type="string", length=250, nullable=true)
     */
    private $familyName;

    /**
     * @var string
     *
     * @ORM\Column(name="given_name", type="string", length=250, nullable=true)
     */
    private $givenName;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="is_confirmed", type="integer", nullable=true)
     */
    private $isConfirmed;

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
     * @var \Document
     *
     * @ORM\ManyToOne(targetEntity="Document", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="document_id", referencedColumnName="id")
     * })
     */
    private $document;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

     /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="phone_id", referencedColumnName="id")
     * })
     */
    private $phone;

    /**
     * @ORM\OneToMany(targetEntity="MasterProxyAccountDoctor", mappedBy="masterProxyAccount", cascade={"persist", "remove" })
     */
    private $mpaDoctors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->mpaDoctors = new ArrayCollection();
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
     * Set mpaCode
     *
     * @param string $mpaCode
     *
     * @return MasterProxyAccount
     */
    public function setMpaCode($mpaCode)
    {
        $this->mpaCode = $mpaCode;

        return $this;
    }

    /**
     * Get mpaCode
     *
     * @return string
     */
    public function getMpaCode()
    {
        return $this->mpaCode;
    }

    /**
     * Set clinicName
     *
     * @param string $clinicName
     *
     * @return MasterProxyAccount
     */
    public function setClinicName($clinicName)
    {
        $this->clinicName = $clinicName;

        return $this;
    }

    /**
     * Get clinicName
     *
     * @return string
     */
    public function getClinicName()
    {
        return $this->clinicName;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     *
     * @return MasterProxyAccount
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * Set familyName
     *
     * @param string $familyName
     *
     * @return MasterProxyAccount
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get familyName
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set givenName
     *
     * @param string $givenName
     *
     * @return MasterProxyAccount
     */
    public function setGivenName($givenName)
    {
        $this->givenName = $givenName;

        return $this;
    }

    /**
     * Get givenName
     *
     * @return string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    /**
     * Set isConfirmed
     *
     * @param integer $isConfirmed
     *
     * @return MasterProxyAccount
     */
    public function setIsConfirmed($isConfirmed)
    {
        $this->isConfirmed = $isConfirmed;

        return $this;
    }

    /**
     * Get isConfirmed
     *
     * @return integer
     */
    public function getIsConfirmed()
    {
        return $this->isConfirmed;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return MasterProxyAccount
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
     * @return MasterProxyAccount
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
     * Set document
     *
     * @param \UtilBundle\Entity\Document $document
     *
     * @return MasterProxyAccount
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
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     *
     * @return MasterProxyAccount
     */
    public function setUser(\UtilBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \UtilBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add mpaDoctor
     *
     * @param \UtilBundle\Entity\MasterProxyAccountDoctor $mpaDoctor
     *
     * @return MasterProxyAccount
     */
    public function addMpaDoctor(\UtilBundle\Entity\MasterProxyAccountDoctor $mpaDoctor)
    {
        $mpaDoctor->setMasterProxyAccount($this);
        $this->mpaDoctors[] = $mpaDoctor;

        return $this;
    }

    /**
     * Remove mpaDoctor
     *
     * @param \UtilBundle\Entity\MasterProxyAccountDoctor $mpaDoctor
     */
    public function removeMpaDoctor(\UtilBundle\Entity\MasterProxyAccountDoctor $mpaDoctor)
    {
        $this->mpaDoctors->removeElement($mpaDoctor);
    }

    /**
     * Get mpaDoctors
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMpaDoctors()
    {
        $list = $this->mpaDoctors;
        $result = [];
        foreach ($list as $item) {
            if(empty($item->getDeletedOn())){
                $result[] =  $item;
            }
        }
        return $result;
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

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return MasterProxyAccount
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
     * Set status
     *
     * @param boolean $status
     *
     * @return MasterProxyAccount
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set phone
     *
     * @param \UtilBundle\Entity\Phone $phone
     *
     * @return MasterProxyAccount
     */
    public function setPhone(\UtilBundle\Entity\Phone $phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return \UtilBundle\Entity\Phone
     */
    public function getPhone()
    {
        return $this->phone;
    }
}
