<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DosageFormAction
 *
 * @ORM\Table(name="dosage_form_action", indexes={@ORM\Index(name="dosage_form_id", columns={"dosage_form_id"}), @ORM\Index(name="dosage_action_id", columns={"dosage_action_id"})})
 * @ORM\Entity
 */
class DosageFormAction
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
     * @var \DosageAction
     *
     * @ORM\ManyToOne(targetEntity="DosageAction")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="dosage_action_id", referencedColumnName="id")
     * })
     */
    private $dosageAction;

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
     * @return DosageFormAction
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
     * @return DosageFormAction
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
     * Set dosageAction
     *
     * @param \UtilBundle\Entity\DosageAction $dosageAction
     * @return DosageFormAction
     */
    public function setDosageAction(\UtilBundle\Entity\DosageAction $dosageAction = null)
    {
        $this->dosageAction = $dosageAction;

        return $this;
    }

    /**
     * Get dosageAction
     *
     * @return \UtilBundle\Entity\DosageAction 
     */
    public function getDosageAction()
    {
        return $this->dosageAction;
    }

    /**
     * Set dosageForm
     *
     * @param \UtilBundle\Entity\DosageForm $dosageForm
     * @return DosageFormAction
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
}
