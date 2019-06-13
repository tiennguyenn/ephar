<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroDocumentType
 *
 * @ORM\Table(name="xero_document_type")
 * @ORM\Entity
 */
class XeroDocumentType
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
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="friendly_name", type="string", length=20, nullable=false)
     */
    private $friendlyName;

    /**
     * @var string
     *
     * @ORM\Column(name="identify_name", type="string", length=50, nullable=false)
     */
    private $identifyName;


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
     * @return XeroDocumentType
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
     * Set friendlyName
     *
     * @param string $friendlyName
     *
     * @return XeroDocumentType
     */
    public function setFriendlyName($friendlyName)
    {
        $this->friendlyName = $friendlyName;

        return $this;
    }

    /**
     * Get friendlyName
     *
     * @return string
     */
    public function getFriendlyName()
    {
        return $this->friendlyName;
    }

    /**
     * Set identifyName
     *
     * @param string $identifyName
     *
     * @return XeroDocumentType
     */
    public function setIdentifyName($identifyName)
    {
        $this->identifyName = $identifyName;

        return $this;
    }

    /**
     * Get identifyName
     *
     * @return string
     */
    public function getIdentifyName()
    {
        return $this->identifyName;
    }
}
