<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DosageRoute
 *
 * @ORM\Table(name="dosage_route")
 * @ORM\Entity
 */
class DosageRoute
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
     * @ORM\Column(name="definition", type="string", length=500, nullable=true)
     */
    private $definition;

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
     * @return DosageRoute
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
     * Set shortName
     *
     * @param string $shortName
     * @return DosageRoute
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
     * @return DosageRoute
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
     * @return DosageRoute
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
     * Set definition
     *
     * @param string $definition
     * @return DosageRoute
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get definition
     *
     * @return string 
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return DosageRoute
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
     * @return DosageRoute
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
     * @return DosageRoute
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
