<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserActors
 *
 * @ORM\Table(name="user_actors", indexes={@ORM\Index(name="user_id", columns={"user_id"}), @ORM\Index(name="entity_id", columns={"entity_id"}), @ORM\Index(name="contact_id", columns={"contact_id"}), @ORM\Index(name="role_id", columns={"role_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\UserActorsRepository")
 * @ORM\HasLifecycleCallbacks
 */
class UserActors
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
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(name="privilege", type="string", length=500, nullable=true)
     */
    private $privilege;

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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    /**
     * @var \Role
     *
     * @ORM\ManyToOne(targetEntity="Role")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

    /**
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone",  cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", nullable=true, referencedColumnName="id")
     * })
     */
    private $contact;

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
     * Set entityId
     *
     * @param integer $entityId
     *
     * @return UserActors
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set privilege
     *
     * @param string $privilege
     *
     * @return UserActors
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = is_array($privilege) ? json_encode($privilege) : null;

        return $this;
    }

    /**
     * Get privilege
     *
     * @return string
     */
    public function getPrivilege()
    {
        return $this->privilege && !empty($this->privilege) ? json_decode($this->privilege, true) : array();
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Agent
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
     * @return Agent
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
     * @return Agent
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
     * Set user
     *
     * @param \UtilBundle\Entity\User $user
     *
     * @return UserActors
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
     * Set role
     *
     * @param \UtilBundle\Entity\Role $role
     *
     * @return UserActors
     */
    public function setRole(\UtilBundle\Entity\Role $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \UtilBundle\Entity\Role
     */
    public function getRole()
    {
        return $this->role;
    }
	
    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime();
		$this->updatedOn = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updatedOn = new \DateTime();
    }

    /**
     * Get contact
     *
     * @return \UtilBundle\Entity\Phone
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set contact
     *
     * @param \UtilBundle\Entity\Phone $contact
     * @return DoctorPhone
     */
    public function setContact(\UtilBundle\Entity\Phone $contact = null)
    {
        $this->contact = $contact;

        return $this;
    }
}
