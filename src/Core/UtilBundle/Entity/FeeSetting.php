<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeeSetting
 *
 * @ORM\Table(name="fee_setting")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\FeeSettingRepository")
 */
class FeeSetting
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
     * @var float
     *
     * @ORM\Column(name="fee", type="float", precision=10, scale=0, nullable=false)
     */
    private $fee;

    /**
     * @var float
     *
     * @ORM\Column(name="new_fee", type="float", precision=10, scale=0, nullable=true)
     */
    private $newFee;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="effect_date", type="datetime", nullable=true)
     */
    private $effectDate;



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
     * Set fee
     *
     * @param float $fee
     * @return FeeSetting
     */
    public function setFee($fee)
    {
        $this->fee = $fee;

        return $this;
    }

    /**
     * Get fee
     *
     * @return float 
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * Set newFee
     *
     * @param float $newFee
     * @return FeeSetting
     */
    public function setNewFee($newFee)
    {
        $this->newFee = $newFee;

        return $this;
    }

    /**
     * Get newFee
     *
     * @return float 
     */
    public function getNewFee()
    {
        return $this->newFee;
    }

    /**
     * Set effectDate
     *
     * @param \DateTime $effectDate
     * @return FeeSetting
     */
    public function setEffectDate($effectDate)
    {
        $this->effectDate = $effectDate;

        return $this;
    }

    /**
     * Get effectDate
     *
     * @return \DateTime 
     */
    public function getEffectDate()
    {
        return $this->effectDate;
    }
}
