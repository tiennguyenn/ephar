<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PackMeasure
 *
 * @ORM\Table(name="pack_measure")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PackMeasureRepository")
 */
class PackMeasure
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
     * @ORM\Column(name="short_name", type="string", length=50, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(name="plural_name", type="string", length=100, nullable=false)
     */
    private $pluralName;



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
     * Set shortName
     *
     * @param string $shortName
     * @return PackMeasure
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
     * Set pluralName
     *
     * @param string $pluralName
     * @return PackMeasure
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
}
