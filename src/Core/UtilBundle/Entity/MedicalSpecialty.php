<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalSpecialty
 *
 * @ORM\Table(name="medical_specialty")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\MedicalSpecialtyRepository")
 */
class MedicalSpecialty
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
     * @ORM\Column(name="name", type="string", length=250, nullable=false)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="Doctor", mappedBy="medicalSpecialty")
     */
    private $doctor;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->doctor = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return MedicalSpecialty
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
     * Add doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     * @return MedicalSpecialty
     */
    public function addDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctor[] = $doctor;

        return $this;
    }

    /**
     * Remove doctor
     *
     * @param \UtilBundle\Entity\Doctor $doctor
     */
    public function removeDoctor(\UtilBundle\Entity\Doctor $doctor)
    {
        $this->doctor->removeElement($doctor);
    }

    /**
     * Get doctor
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDoctor()
    {
        return $this->doctor;
    }
}
