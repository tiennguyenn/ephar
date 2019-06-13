<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DosageForm
 *
 * @ORM\Table(name="dosage_form")
 * @ORM\Entity
 */
class DosageForm
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
     * @ORM\Column(name="name", type="string", length=150, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="plural_name", type="string", length=150, nullable=true)
     */
    private $pluralName;

    /**
     * @var string
     *
     * @ORM\Column(name="short_name", type="string", length=50, nullable=true)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="FDACode", type="string", length=10, nullable=true)
     */
    private $fdacode;

    /**
     * @var string
     *
     * @ORM\Column(name="NCIConceptID", type="string", length=20, nullable=true)
     */
    private $nciconceptid;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=true)
     */
    private $description;

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
     * Set name
     *
     * @param string $name
     *
     * @return DosageForm
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
     * Set pluralName
     *
     * @param string $pluralName
     *
     * @return DosageForm
     */
    public function setPluralName($pluralName)
    {
        $this->pluralName = $pluralName;

        return $this;
    }

    /**
     * Get pluralName
     *
     * @return string
     */
    public function getPluralName()
    {
        return $this->pluralName;
    }

    /**
     * Set shortName
     *
     * @param string $shortName
     *
     * @return DosageForm
     */
    public function setShortName($shortName)
    {
        $this->shortName = $shortName;

        return $this;
    }

    /**
     * Get shortName
     *
     * @return string
     */
    public function getShortName()
    {
        return $this->shortName;
    }

    /**
     * Set fdacode
     *
     * @param string $fdacode
     *
     * @return DosageForm
     */
    public function setFdacode($fdacode)
    {
        $this->fdacode = $fdacode;

        return $this;
    }

    /**
     * Get fdacode
     *
     * @return string
     */
    public function getFdacode()
    {
        return $this->fdacode;
    }

    /**
     * Set nciconceptid
     *
     * @param string $nciconceptid
     *
     * @return DosageForm
     */
    public function setNciconceptid($nciconceptid)
    {
        $this->nciconceptid = $nciconceptid;

        return $this;
    }

    /**
     * Get nciconceptid
     *
     * @return string
     */
    public function getNciconceptid()
    {
        return $this->nciconceptid;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return DosageForm
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return DosageForm
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
     *
     * @return DosageForm
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
     *
     * @return DosageForm
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
