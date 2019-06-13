<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroComponentFunction
 *
 * @ORM\Table(name="xero_component_function", indexes={@ORM\Index(name="FK_xero_component_function", columns={"xero_component_id"})})
 * @ORM\Entity
 */
class XeroComponentFunction
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
     * @ORM\Column(name="function", type="string", length=250, nullable=true)
     */
    private $function;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="integer", nullable=true)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_tmp", type="integer", nullable=true)
     */
    private $numberTmp;

    /**
     * @var string
     *
     * @ORM\Column(name="description_tmp", type="text", length=65535, nullable=true)
     */
    private $descriptionTmp;

    /**
     * @var \XeroComponent
     *
     * @ORM\ManyToOne(targetEntity="XeroComponent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_component_id", referencedColumnName="id")
     * })
     */
    private $xeroComponent;



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
     * Set function
     *
     * @param string $function
     *
     * @return XeroComponentFunction
     */
    public function setFunction($function)
    {
        $this->function = $function;

        return $this;
    }

    /**
     * Get function
     *
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return XeroComponentFunction
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return boolean
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return XeroComponentFunction
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
     * Set numberTmp
     *
     * @param integer $numberTmp
     *
     * @return XeroComponentFunction
     */
    public function setNumberTmp($numberTmp)
    {
        $this->numberTmp = $numberTmp;

        return $this;
    }

    /**
     * Get numberTmp
     *
     * @return integer
     */
    public function getNumberTmp()
    {
        return $this->numberTmp;
    }

    /**
     * Set descriptionTmp
     *
     * @param string $descriptionTmp
     *
     * @return XeroComponentFunction
     */
    public function setDescriptionTmp($descriptionTmp)
    {
        $this->descriptionTmp = $descriptionTmp;

        return $this;
    }

    /**
     * Get descriptionTmp
     *
     * @return string
     */
    public function getDescriptionTmp()
    {
        return $this->descriptionTmp;
    }

    /**
     * Set xeroComponent
     *
     * @param \UtilBundle\Entity\XeroComponent $xeroComponent
     *
     * @return XeroComponentFunction
     */
    public function setXeroComponent(\UtilBundle\Entity\XeroComponent $xeroComponent = null)
    {
        $this->xeroComponent = $xeroComponent;

        return $this;
    }

    /**
     * Get xeroComponent
     *
     * @return \UtilBundle\Entity\XeroComponent
     */
    public function getXeroComponent()
    {
        return $this->xeroComponent;
    }
}
