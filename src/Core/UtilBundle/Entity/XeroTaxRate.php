<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroTaxRate
 *
 * @ORM\Table(name="xero_tax_rate", indexes={@ORM\Index(name="gmeds_tax_rate_id", columns={"gmeds_tax_rate_id"})})
 * @ORM\Entity
 */
class XeroTaxRate
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
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;


    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=200, nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="gmeds_tax_rate_id", type="integer", nullable=false)
     */
    private $gmedsTaxRateId;



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
     * @return XeroTaxRate
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
     * Set gmedsTaxRateId
     *
     * @param integer $gmedsTaxRateId
     *
     * @return XeroTaxRate
     */
    public function setGmedsTaxRateId($gmedsTaxRateId)
    {
        $this->gmedsTaxRateId = $gmedsTaxRateId;

        return $this;
    }

    /**
     * Get gmedsTaxRateId
     *
     * @return integer
     */
    public function getGmedsTaxRateId()
    {
        return $this->gmedsTaxRateId;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return XeroTaxRate
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}
