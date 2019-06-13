<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroSaleLineOption
 *
 * @ORM\Table(name="xero_sale_line_option", indexes={@ORM\Index(name="FK_xero_sale_line_option", columns={"xero_sale_line_id"})})
 * @ORM\Entity
 */
class XeroSaleLineOption
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
     * @ORM\Column(name="option_name", type="string", length=250, nullable=true)
     */
    private $optionName;

    /**
     * @var string
     *
     * @ORM\Column(name="option_value", type="string", length=250, nullable=true)
     */
    private $optionValue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \XeroSaleLine
     *
     * @ORM\ManyToOne(targetEntity="XeroSaleLine")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_sale_line_id", referencedColumnName="id")
     * })
     */
    private $xeroSaleLine;



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
     * Set optionName
     *
     * @param string $optionName
     *
     * @return XeroSaleLineOption
     */
    public function setOptionName($optionName)
    {
        $this->optionName = $optionName;

        return $this;
    }

    /**
     * Get optionName
     *
     * @return string
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Set optionValue
     *
     * @param string $optionValue
     *
     * @return XeroSaleLineOption
     */
    public function setOptionValue($optionValue)
    {
        $this->optionValue = $optionValue;

        return $this;
    }

    /**
     * Get optionValue
     *
     * @return string
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroSaleLineOption
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
     * Set xeroSaleLine
     *
     * @param \UtilBundle\Entity\XeroSaleLine $xeroSaleLine
     *
     * @return XeroSaleLineOption
     */
    public function setXeroSaleLine(\UtilBundle\Entity\XeroSaleLine $xeroSaleLine = null)
    {
        $this->xeroSaleLine = $xeroSaleLine;

        return $this;
    }

    /**
     * Get xeroSaleLine
     *
     * @return \UtilBundle\Entity\XeroSaleLine
     */
    public function getXeroSaleLine()
    {
        return $this->xeroSaleLine;
    }
}
