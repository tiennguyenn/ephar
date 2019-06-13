<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CountryRepository")
 * @ORM\NamedNativeQueries({
 *     @ORM\NamedNativeQuery(
 *          name="get_by_preferred_country",
 *          resultSetMapping= "m1",
 *          query="SELECT * FROM country
                        ORDER BY 
                            (CASE WHEN country.code IN ('SG', 'MY', 'ID') THEN country.code end) DESC,
                            (CASE WHEN country.code NOT IN ('SG', 'MY', 'ID') THEN id end) ASC"
 *     )
 * })
 * @ORM\SqlResultSetMappings({
 *      @ORM\SqlResultSetMapping(
 *          name    = "m1",
 *          entities= {
 *              @ORM\EntityResult(
 *                  entityClass = "__CLASS__",
 *                  fields      = {
 *                      @ORM\FieldResult(name = "id", column="id"),
 *                      @ORM\FieldResult(name = "code", column="code"),
 *                      @ORM\FieldResult(name = "name", column="name"),
 *                      @ORM\FieldResult(name = "phoneCode", column="phone_code"),
 *                      @ORM\FieldResult(name = "codeAthree", column="code_athree")
 *                  }
 *              ),
 *          },
 *      )
 *})
 */
class Country
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
     * @ORM\Column(name="code", type="string", length=2, nullable=true)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="code_athree", type="string", length=3, nullable=true)
     */
    private $codeAthree;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="phone_code", type="string", length=10, nullable=true)
     */
    private $phoneCode;

    /**
     * @var float
     *
     * @ORM\Column(name="customs_tax", type="float", precision=10, scale=0, nullable=true)
     */
    private $customsTax;

    /**
     * @var float
     *
     * @ORM\Column(name="threshold_tax", type="float", precision=10, scale=0, nullable=true)
     */
    private $thresholdTax;

    /**
     * @var float
     *
     * @ORM\Column(name="customs_clearance_platform_percentage", type="float", precision=10, scale=0, nullable=true)
     */
    private $customsClearancePlatformPercentage;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;

    /**
     * @ORM\OneToMany(targetEntity="City", mappedBy="country")
     */
    private $cities;

    public function __construct()
    {
        $this->cities = new ArrayCollection();
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
     * Set code
     *
     * @param string $code
     * @return Country
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

    /**
     * Set codeAthree
     *
     * @param string $codeAthree
     * @return Country
     */
    public function setCodeAthree($codeAthree)
    {
        $this->codeAthree = $codeAthree;

        return $this;
    }

    /**
     * Get codeAthree
     *
     * @return string 
     */
    public function getCodeAthree()
    {
        return $this->codeAthree;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Country
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
     * Set phoneCode
     *
     * @param string $phoneCode
     * @return Country
     */
    public function setPhoneCode($phoneCode)
    {
        $this->phoneCode = $phoneCode;

        return $this;
    }

    /**
     * Get phoneCode
     *
     * @return string 
     */
    public function getPhoneCode()
    {
        return $this->phoneCode;
    }

    /**
     * Set customsTax
     *
     * @param float $customsTax
     * @return Country
     */
    public function setCustomsTax($customsTax)
    {
        $this->customsTax = $customsTax;

        return $this;
    }

    /**
     * Get customsTax
     *
     * @return float 
     */
    public function getCustomsTax()
    {
        return $this->customsTax;
    }

    /**
     * Set thresholdTax
     *
     * @param float $thresholdTax
     * @return Country
     */
    public function setThresholdTax($thresholdTax)
    {
        $this->thresholdTax = $thresholdTax;

        return $this;
    }

    /**
     * Get thresholdTax
     *
     * @return float 
     */
    public function getThresholdTax()
    {
        return $this->thresholdTax;
    }

    /**
     * Set customsClearancePlatformPercentage
     *
     * @param float $customsClearancePlatformPercentage
     * @return Country
     */
    public function setCustomsClearancePlatformPercentage($customsClearancePlatformPercentage)
    {
        $this->customsClearancePlatformPercentage = $customsClearancePlatformPercentage;

        return $this;
    }

    /**
     * Get customsClearancePlatformPercentage
     *
     * @return float 
     */
    public function getCustomsClearancePlatformPercentage()
    {
        return $this->customsClearancePlatformPercentage;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return Country
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
     * Add cities
     *
     * @param \UtilBundle\Entity\City $cities
     * @return Country
     */
    public function addCity(\UtilBundle\Entity\City $cities)
    {
        $this->cities[] = $cities;

        return $this;
    }

    /**
     * Remove cities
     *
     * @param \UtilBundle\Entity\City $cities
     */
    public function removeCity(\UtilBundle\Entity\City $cities)
    {
        $this->cities->removeElement($cities);
    }

    /**
     * Get cities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCities()
    {
        return $this->cities;
    }
}
