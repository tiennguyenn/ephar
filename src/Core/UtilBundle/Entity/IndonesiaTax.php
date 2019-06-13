<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IndonesiaTax
 *
 * @ORM\Table(name="indonesia_tax")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\IndonesiaTaxRepository")
 */
class IndonesiaTax
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
     * @ORM\Column(name="tax_name", type="string", length=250, nullable=true)
     */
    private $taxName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="tax_value", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxValue;

    /**
     * @var float
     *
     * @ORM\Column(name="tax_value_new", type="float", precision=10, scale=0, nullable=true)
     */
    private $taxValueNew;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effect_date", type="datetime", nullable=true)
     */
    private $effectDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;



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
     * Set taxName
     *
     * @param string $taxName
     *
     * @return IndonesiaTax
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;

        return $this;
    }

    /**
     * Get taxName
     *
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return IndonesiaTax
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
     * Set taxValue
     *
     * @param float $taxValue
     *
     * @return IndonesiaTax
     */
    public function setTaxValue($taxValue)
    {
        $this->taxValue = $taxValue;

        return $this;
    }

    /**
     * Get taxValue
     *
     * @return float
     */
    public function getTaxValue()
    {
        return $this->taxValue;
    }

    /**
     * Set taxValueNew
     *
     * @param float $taxValueNew
     *
     * @return IndonesiaTax
     */
    public function setTaxValueNew($taxValueNew)
    {
        $this->taxValueNew = $taxValueNew;

        return $this;
    }

    /**
     * Get taxValueNew
     *
     * @return float
     */
    public function getTaxValueNew()
    {
        return $this->taxValueNew;
    }

    /**
     * Set effectDate
     *
     * @param \DateTime $effectDate
     *
     * @return IndonesiaTax
     */
    public function setEffectDate($effectDate)
    {
        $this->effectDate = $effectDate;

        return $this;
    }

    /**
     * Get effectDate
     *
     * @return \DateTime
     */
    public function getEffectDate()
    {
        return $this->effectDate;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     *
     * @return IndonesiaTax
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
}
