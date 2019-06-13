<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalQualification
 *
 * @ORM\Table(name="medical_qualification")
 * @ORM\Entity
 */
class MedicalQualification
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
     * @ORM\Column(name="abbreviation", type="string", length=250, nullable=true)
     */
    private $abbreviation;

    /**
     * @var string
     *
     * @ORM\Column(name="fullname", type="string", length=250, nullable=true)
     */
    private $fullname;



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
     * Set abbreviation
     *
     * @param string $abbreviation
     * @return MedicalQualification
     */
    public function setAbbreviation($abbreviation)
    {
        $this->abbreviation = $abbreviation;

        return $this;
    }

    /**
     * Get abbreviation
     *
     * @return string 
     */
    public function getAbbreviation()
    {
        return $this->abbreviation;
    }

    /**
     * Set fullname
     *
     * @param string $fullname
     * @return MedicalQualification
     */
    public function setFullname($fullname)
    {
        $this->fullname = $fullname;

        return $this;
    }

    /**
     * Get fullname
     *
     * @return string 
     */
    public function getFullname()
    {
        return $this->fullname;
    }
}
