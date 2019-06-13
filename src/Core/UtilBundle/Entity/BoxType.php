<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BoxType
 *
 * @ORM\Table(name="box_type")
 * @ORM\Entity
 */
class BoxType
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
     * @ORM\Column(name="supplier_size_code", type="string", length=50, nullable=false)
     */
    private $supplierSizeCode;

    /**
     * @var string
     *
     * @ORM\Column(name="size_code", type="string", length=50, nullable=false)
     */
    private $sizeCode;

    /**
     * @var float
     *
     * @ORM\Column(name="length", type="float", precision=10, scale=0, nullable=false)
     */
    private $length;

    /**
     * @var float
     *
     * @ORM\Column(name="width", type="float", precision=10, scale=0, nullable=false)
     */
    private $width;

    /**
     * @var float
     *
     * @ORM\Column(name="height", type="float", precision=10, scale=0, nullable=false)
     */
    private $height;

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
     * Set supplierSizeCode
     *
     * @param string $supplierSizeCode
     * @return BoxType
     */
    public function setSupplierSizeCode($supplierSizeCode)
    {
        $this->supplierSizeCode = $supplierSizeCode;

        return $this;
    }

    /**
     * Get supplierSizeCode
     *
     * @return string 
     */
    public function getSupplierSizeCode()
    {
        return $this->supplierSizeCode;
    }

    /**
     * Set sizeCode
     *
     * @param string $sizeCode
     * @return BoxType
     */
    public function setSizeCode($sizeCode)
    {
        $this->sizeCode = $sizeCode;

        return $this;
    }

    /**
     * Get sizeCode
     *
     * @return string 
     */
    public function getSizeCode()
    {
        return $this->sizeCode;
    }

    /**
     * Set length
     *
     * @param float $length
     * @return BoxType
     */
    public function setLength($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get length
     *
     * @return float 
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Set width
     *
     * @param float $width
     * @return BoxType
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width
     *
     * @return float 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height
     *
     * @param float $height
     * @return BoxType
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height
     *
     * @return float 
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return BoxType
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
     * @return BoxType
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
     * @return BoxType
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
