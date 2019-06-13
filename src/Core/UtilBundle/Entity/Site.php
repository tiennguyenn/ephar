<?php

namespace UtilBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table(name="site")
 * @ORM\Entity
 */
class Site
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=300, nullable=false)
     */
    private $url;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", length=2, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @ORM\OneToMany(targetEntity="XeroSiteBatch", mappedBy="site", cascade={"persist", "remove" })
     */
    private $siteBatches;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->siteBatches = new ArrayCollection();
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
     * @return Site
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Site
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
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return Site
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

    /**
     * Add siteBatch
     *
     * @param \UtilBundle\Entity\XeroSyncData $siteBatch
     *
     * @return Site
     */
    public function addSiteBatch(\UtilBundle\Entity\XeroSyncData $siteBatch)
    {
        $this->siteBatches[] = $siteBatch;

        return $this;
    }

    /**
     * Remove siteBatch
     *
     * @param \UtilBundle\Entity\XeroSyncData $siteBatch
     */
    public function removeSiteBatch(\UtilBundle\Entity\XeroSyncData $siteBatch)
    {
        $this->siteBatches->removeElement($siteBatch);
    }

    /**
     * Get siteBatches
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSiteBatches()
    {
        return $this->siteBatches;
    }

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Site
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Site
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    public function getDisplaySite()
    {
        return $this->name . ' (' . $this->url . ')';
    }
}
