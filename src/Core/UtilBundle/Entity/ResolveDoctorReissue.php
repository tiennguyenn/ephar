<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveDoctorReissue
 *
 * @ORM\Table(name="resolve_doctor_reissue", indexes={@ORM\Index(name="FK_resolve_doctor_reissue", columns={"resolve_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class ResolveDoctorReissue
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
     * @ORM\Column(name="status", type="integer", nullable=true)
     */
    private $status;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    private $isLocked;

    /**
     * @var string
     *
     * @ORM\Column(name="order_number_link", type="string", length=20, nullable=true)
     */
    private $orderNumberLink;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="text", length=65535, nullable=true)
     */
    private $reason;

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
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;


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
     * Set status
     *
     * @param boolean $status
     *
     * @return ResolveDoctorReissue
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
     * Set orderNumberLink
     *
     * @param string $orderNumberLink
     *
     * @return ResolveDoctorReissue
     */
    public function setOrderNumberLink($orderNumberLink)
    {
        $this->orderNumberLink = $orderNumberLink;

        return $this;
    }

    /**
     * Get orderNumberLink
     *
     * @return string
     */
    public function getOrderNumberLink()
    {
        return $this->orderNumberLink;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return ResolveDoctorReissue
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveDoctorReissue
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
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveDoctorReissue
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }

    /**
     * Set isLocked
     *
     * @param boolean $isLocked
     *
     * @return ResolveDoctorReissue
     */
    public function setIsLocked($isLocked)
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * Get isLocked
     *
     * @return boolean
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return ResolveDoctorReissue
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
}
