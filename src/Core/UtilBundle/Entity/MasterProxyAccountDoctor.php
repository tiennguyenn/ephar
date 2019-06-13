<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MasterProxyAccountDoctor
 *
 * @ORM\Table(name="master_proxy_account_doctor", indexes={@ORM\Index(name="FK_proxy_master_account_doctor", columns={"doctor_id"}), @ORM\Index(name="FK_proxy_master_account_doctor_mpa", columns={"master_proxy_account_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MasterProxyAccountDoctorRepository")
 * @ORM\HasLifecycleCallbacks
 */
class MasterProxyAccountDoctor
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
     * @ORM\Column(name="privilege", type="text", length=65535, nullable=true)
     */
    private $privilege;

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
     * @var \MasterProxyAccount
     *
     * @ORM\ManyToOne(targetEntity="MasterProxyAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="master_proxy_account_id", referencedColumnName="id")
     * })
     */
    private $masterProxyAccount;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="doctor_id", referencedColumnName="id")
     * })
     */
    private $doctor;



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
     * Set privilege
     *
     * @param string $privilege
     *
     * @return MasterProxyAccountDoctor
     */
    public function setPrivilege($privilege)
    {
        $this->privilege = $privilege;

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
     *
     * @return MasterProxyAccountDoctor
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
     * Set masterProxyAccount
     *
     * @param \UtilBundle\Entity\MasterProxyAccount $masterProxyAccount
     *
     * @return MasterProxyAccountDoctor
     */
    public function setMasterProxyAccount(\UtilBundle\Entity\MasterProxyAccount $masterProxyAccount = null)
    {
        $this->masterProxyAccount = $masterProxyAccount;

        return $this;
    }

    /**
     * Get masterProxyAccount
     *
     * @return \UtilBundle\Entity\MasterProxyAccount
     */
    public function getMasterProxyAccount()
    {
        return $this->masterProxyAccount;
    }

    /**
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     *
     * @return MasterProxyAccountDoctor
     */
    public function setDoctor(\UtilBundle\Entity\Doctor $doctor = null)
    {
        $this->doctor = $doctor;

        return $this;
    }

    /**
     * Get doctor
     *
     * @return \UtilBundle\Entity\Doctor
     */
    public function getDoctor()
    {
        return $this->doctor;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     *
     * @return MasterProxyAccountDoctor
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }
}
