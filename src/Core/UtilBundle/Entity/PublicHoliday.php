<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PublicHoliday
 *
 * @ORM\Table(name="public_holiday")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\PublicHolidayRepository")
 */
class PublicHoliday
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
     * @ORM\Column(name="public_date_name", type="string", length=250, nullable=true)
     */
    private $publicDateName;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="public_date", type="date", nullable=true)
     */
    private $publicDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deleted_on", type="datetime", nullable=true)
     */
    private $deletedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;



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
     * Set publicDateName
     *
     * @param string $publicDateName
     * @return PublicHoliday
     */
    public function setPublicDateName($publicDateName)
    {
        $this->publicDateName = $publicDateName;

        return $this;
    }

    /**
     * Get publicDateName
     *
     * @return string 
     */
    public function getPublicDateName()
    {
        return $this->publicDateName;
    }

    /**
     * Set publicDate
     *
     * @param \DateTime $publicDate
     * @return PublicHoliday
     */
    public function setPublicDate($publicDate)
    {
        $this->publicDate = $publicDate;

        return $this;
    }

    /**
     * Get publicDate
     *
     * @return \DateTime 
     */
    public function getPublicDate()
    {
        return $this->publicDate;
    }

    /**
     * Set deletedOn
     *
     * @param \DateTime $deletedOn
     * @return PublicHoliday
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     * @return PublicHoliday
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
}
