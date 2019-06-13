<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * XeroComponent
 *
 * @ORM\Table(name="xero_component", indexes={@ORM\Index(name="xero_event_trigger_id", columns={"xero_event_trigger_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroComponentRepository")
 */
class XeroComponent
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
     * @var \XeroEventTrigger
     *
     * @ORM\ManyToOne(targetEntity="XeroEventTrigger", cascade={"persist", "remove" })
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xero_event_trigger_id", referencedColumnName="id")
     * })
     */
    private $xeroEventTrigger;

    /**
     * @ORM\OneToMany(targetEntity="XeroComponentFunction", mappedBy="xeroComponent", cascade={"persist", "remove" })
     */
    private $functions;

    /**
     * @ORM\OneToMany(targetEntity="XeroMapping", mappedBy="xeroComponent", cascade={"persist", "remove" })
     */
    private $maps;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->functions = new ArrayCollection();
        $this->maps = new ArrayCollection();
    }

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
     * @return XeroComponent
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
     * Set xeroEventTrigger
     *
     * @param \UtilBundle\Entity\XeroEventTrigger $xeroEventTrigger
     *
     * @return XeroComponent
     */
    public function setXeroEventTrigger(\UtilBundle\Entity\XeroEventTrigger $xeroEventTrigger = null)
    {
        $this->xeroEventTrigger = $xeroEventTrigger;

        return $this;
    }

    /**
     * Get xeroEventTrigger
     *
     * @return \UtilBundle\Entity\XeroEventTrigger
     */
    public function getXeroEventTrigger()
    {
        return $this->xeroEventTrigger;
    }

    /**
     * Add function
     *
     * @param \UtilBundle\Entity\XeroComponentFunction $function
     *
     * @return XeroComponent
     */
    public function addFunction(\UtilBundle\Entity\XeroComponentFunction $function)
    {
        $function->setXeroComponent($this);
        $this->functions[] = $function;

        return $this;
    }

    /**
     * Remove function
     *
     * @param \UtilBundle\Entity\XeroComponentFunction $function
     */
    public function removeFunction(\UtilBundle\Entity\XeroComponentFunction $function)
    {
        $this->functions->removeElement($function);
    }

    /**
     * Get functions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFunctions()
    {
        return $this->functions;
    }

    /**
     * Add map
     *
     * @param \UtilBundle\Entity\XeroMapping $map
     *
     * @return XeroComponent
     */
    public function addMap(\UtilBundle\Entity\XeroMapping $map)
    {
        $this->maps[] = $map;

        return $this;
    }

    /**
     * Remove map
     *
     * @param \UtilBundle\Entity\XeroMapping $map
     */
    public function removeMap(\UtilBundle\Entity\XeroMapping $map)
    {
        $this->maps->removeElement($map);
    }

    /**
     * Get maps
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMaps()
    {
        return $this->maps;
    }
}
