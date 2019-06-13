<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorPhone
 *
 * @ORM\Table(name="doctor_phone", indexes={@ORM\Index(name="doctor_id", columns={"doctor_id"}), @ORM\Index(name="contact_id", columns={"contact_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DoctorPhoneRepository")
 * @ORM\HasLifecycleCallbacks
 */
class DoctorPhone
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
     * @ORM\Column(name="nick_name", type="string", length=50, nullable=true)
     */
    private $nickName;

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
     * @var \Phone
     *
     * @ORM\ManyToOne(targetEntity="Phone",  cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="contact_id", referencedColumnName="id")
     * })
     */
    private $contact;

    /**
     * @var \Doctor
     *
     * @ORM\ManyToOne(targetEntity="Doctor",inversedBy="doctorPhones")
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
     * Set nickName
     *
     * @param string $nickName
     * @return DoctorPhone
     */
    public function setNickName($nickName)
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * Get nickName
     *
     * @return string 
     */
    public function getNickName()
    {
        return $this->nickName;
    }   

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorPhone
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return DoctorPhone
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
     * Set doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return DoctorPhone
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
     * Gets triggered only on insert

     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->createdOn = new \DateTime("now");
    }

    
}
