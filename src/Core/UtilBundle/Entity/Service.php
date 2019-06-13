<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service
 *
 * @ORM\Table(name="service")
 * @ORM\Entity
 */
class Service
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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=500, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="USI", type="string", length=50, nullable=false)
     */
    private $usi;

    /**
     * @var string
     *
     * @ORM\Column(name="cost_price", type="string", length=10, nullable=true)
     */
    private $costPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="list_price", type="string", length=10, nullable=true)
     */
    private $listPrice;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=false)
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
     * @return Service
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
     * @return Service
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
     * Set usi
     *
     * @param string $usi
     * @return Service
     */
    public function setUsi($usi)
    {
        $this->usi = $usi;

        return $this;
    }

    /**
     * Get usi
     *
     * @return string 
     */
    public function getUsi()
    {
        return $this->usi;
    }

    /**
     * Set costPrice
     *
     * @param string $costPrice
     * @return Service
     */
    public function setCostPrice($costPrice)
    {
        $this->costPrice = $costPrice;

        return $this;
    }

    /**
     * Get costPrice
     *
     * @return string 
     */
    public function getCostPrice()
    {
        return $this->costPrice;
    }

    /**
     * Set listPrice
     *
     * @param string $listPrice
     * @return Service
     */
    public function setListPrice($listPrice)
    {
        $this->listPrice = $listPrice;

        return $this;
    }

    /**
     * Get listPrice
     *
     * @return string 
     */
    public function getListPrice()
    {
        return $this->listPrice;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return Service
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
     * @return Service
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
     * @return Service
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
