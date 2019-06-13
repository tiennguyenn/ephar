<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DrugGroup
 *
 * @ORM\Table(name="drug_group")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\DrugGroupRepository")
 */
class DrugGroup
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
     * @ORM\Column(name="name", type="string", length=250, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="local_price_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $localPricePercentage;

    /**
     * @var float
     *
     * @ORM\Column(name="overseas_price_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $overseasPricePercentage;



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
     * @return DrugGroup
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
     * Set description
     *
     * @param string $description
     * @return DrugGroup
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
     * Set localPricePercentage
     *
     * @param float $localPricePercentage
     * @return DrugGroup
     */
    public function setLocalPricePercentage($localPricePercentage)
    {
        $this->localPricePercentage = $localPricePercentage;

        return $this;
    }

    /**
     * Get localPricePercentage
     *
     * @return float 
     */
    public function getLocalPricePercentage()
    {
        return $this->localPricePercentage;
    }

    /**
     * Set overseasPricePercentage
     *
     * @param float $overseasPricePercentage
     * @return DrugGroup
     */
    public function setOverseasPricePercentage($overseasPricePercentage)
    {
        $this->overseasPricePercentage = $overseasPricePercentage;

        return $this;
    }

    /**
     * Get overseasPricePercentage
     *
     * @return float 
     */
    public function getOverseasPricePercentage()
    {
        return $this->overseasPricePercentage;
    }
}
