<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DosageFormRoute
 *
 * @ORM\Table(name="dosage_form_route", indexes={@ORM\Index(name="dosage_route_id", columns={"dosage_route_id"}), @ORM\Index(name="dosage_form_id", columns={"dosage_form_id"})})
 * @ORM\Entity
 */
class DosageFormRoute
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
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DosageForm
     *
     * @ORM\ManyToOne(targetEntity="DosageForm")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dosage_form_id", referencedColumnName="id")
     * })
     */
    private $dosageForm;

    /**
     * @var \DosageRoute
     *
     * @ORM\ManyToOne(targetEntity="DosageRoute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dosage_route_id", referencedColumnName="id")
     * })
     */
    private $dosageRoute;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DosageFormRoute
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
     * @return DosageFormRoute
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
     * Set dosageForm
     *
     * @param \UtilBundle\Entity\DosageForm $dosageForm
     * @return DosageFormRoute
     */
    public function setDosageForm(\UtilBundle\Entity\DosageForm $dosageForm = null)
    {
        $this->dosageForm = $dosageForm;

        return $this;
    }

    /**
     * Get dosageForm
     *
     * @return \UtilBundle\Entity\DosageForm 
     */
    public function getDosageForm()
    {
        return $this->dosageForm;
    }

    /**
     * Set dosageRoute
     *
     * @param \UtilBundle\Entity\DosageRoute $dosageRoute
     * @return DosageFormRoute
     */
    public function setDosageRoute(\UtilBundle\Entity\DosageRoute $dosageRoute = null)
    {
        $this->dosageRoute = $dosageRoute;

        return $this;
    }

    /**
     * Get dosageRoute
     *
     * @return \UtilBundle\Entity\DosageRoute 
     */
    public function getDosageRoute()
    {
        return $this->dosageRoute;
    }
}
