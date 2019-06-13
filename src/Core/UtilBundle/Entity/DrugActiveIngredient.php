<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DrugActiveIngredient
 *
 * @ORM\Table(name="drug_active_ingredient", indexes={@ORM\Index(name="drug_id", columns={"drug_id"}), @ORM\Index(name="active_ingredient_id", columns={"active_ingredient_id"})})
 * @ORM\Entity
 */
class DrugActiveIngredient
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
     * @var \Drug
     *
     * @ORM\ManyToOne(targetEntity="Drug")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="drug_id", referencedColumnName="id")
     * })
     */
    private $drug;

    /**
     * @var \ActiveIngredient
     *
     * @ORM\ManyToOne(targetEntity="ActiveIngredient")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="active_ingredient_id", referencedColumnName="id")
     * })
     */
    private $activeIngredient;



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
     * @return DrugActiveIngredient
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
     * @return DrugActiveIngredient
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
     * Set drug
     *
     * @param \UtilBundle\Entity\Drug $drug
     * @return DrugActiveIngredient
     */
    public function setDrug(\UtilBundle\Entity\Drug $drug = null)
    {
        $this->drug = $drug;

        return $this;
    }

    /**
     * Get drug
     *
     * @return \UtilBundle\Entity\Drug 
     */
    public function getDrug()
    {
        return $this->drug;
    }

    /**
     * Set activeIngredient
     *
     * @param \UtilBundle\Entity\ActiveIngredient $activeIngredient
     * @return DrugActiveIngredient
     */
    public function setActiveIngredient(\UtilBundle\Entity\ActiveIngredient $activeIngredient = null)
    {
        $this->activeIngredient = $activeIngredient;

        return $this;
    }

    /**
     * Get activeIngredient
     *
     * @return \UtilBundle\Entity\ActiveIngredient 
     */
    public function getActiveIngredient()
    {
        return $this->activeIngredient;
    }
}
