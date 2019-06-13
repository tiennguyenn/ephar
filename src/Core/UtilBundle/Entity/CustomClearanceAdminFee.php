<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomClearanceAdminFee
 *
 * @ORM\Table(name="custom_clearance_admin_fee", indexes={@ORM\Index(name="FK_custom_clearcance_admin_fee", columns={"country_id"}), @ORM\Index(name="FK_custom_clearcance_admin_fee_setting", columns={"fee_setting_id"})})
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\CustomClearanceAdminFeeRepository")
 */
class CustomClearanceAdminFee
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
     * @var \FeeSetting
     *
     * @ORM\ManyToOne(targetEntity="FeeSetting")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fee_setting_id", referencedColumnName="id")
     * })
     */
    private $feeSetting;

    /**
     * @var \Country
     *
     * @ORM\ManyToOne(targetEntity="Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;



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
     * Set feeSetting
     *
     * @param \UtilBundle\Entity\FeeSetting $feeSetting
     * @return CustomClearanceAdminFee
     */
    public function setFeeSetting(\UtilBundle\Entity\FeeSetting $feeSetting = null)
    {
        $this->feeSetting = $feeSetting;

        return $this;
    }

    /**
     * Get feeSetting
     *
     * @return \UtilBundle\Entity\FeeSetting 
     */
    public function getFeeSetting()
    {
        return $this->feeSetting;
    }

    /**
     * Set country
     *
     * @param \UtilBundle\Entity\Country $country
     * @return CustomClearanceAdminFee
     */
    public function setCountry(\UtilBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \UtilBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
}
