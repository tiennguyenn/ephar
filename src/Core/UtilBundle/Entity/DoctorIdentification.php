<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DoctorIdentification
 *
 * @ORM\Table(name="doctor_identification")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DoctorIdentificationRepository")
 */
class DoctorIdentification
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
     * @ORM\Column(name="doctor_id", type="integer", nullable=true)
     */
    private $doctorId;

    /**
     * @var integer
     *
     * @ORM\Column(name="identification_id", type="integer", nullable=true)
     */
    private $identificationId;

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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set doctorId
     *
     * @param integer $doctorId
     * @return DoctorIdentification
     */
    public function setDoctorId($doctorId)
    {
        $this->doctorId = $doctorId;

        return $this;
    }

    /**
     * Get doctorId
     *
     * @return integer 
     */
    public function getDoctorId()
    {
        return $this->doctorId;
    }

    /**
     * Set identificationId
     *
     * @param integer $identificationId
     * @return DoctorIdentification
     */
    public function setIdentificationId($identificationId)
    {
        $this->identificationId = $identificationId;

        return $this;
    }

    /**
     * Get identificationId
     *
     * @return integer 
     */
    public function getIdentificationId()
    {
        return $this->identificationId;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DoctorIdentification
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
     * @return DoctorIdentification
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
     * @return DoctorIdentification
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
