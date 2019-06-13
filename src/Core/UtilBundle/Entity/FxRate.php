<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FxRate
 *
 * @ORM\Table(name="fx_rate")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\FxRateRepository")
 */
class FxRate
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
     * @ORM\Column(name="currency_from", type="string", length=3, nullable=true)
     */
    private $currencyFrom;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_to", type="string", length=3, nullable=true)
     */
    private $currencyTo;

    /**
     * @var float
     *
     * @ORM\Column(name="rate", type="float", precision=10, scale=0, nullable=true)
     */
    private $rate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_on", type="datetime", nullable=true)
     */
    private $createOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_on", type="datetime", nullable=true)
     */
    private $updatedOn;


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
     * Set currencyFrom
     *
     * @param string $currencyFrom
     * @return FxRate
     */
    public function setCurrencyFrom($currencyFrom)
    {
        $this->currencyFrom = $currencyFrom;

        return $this;
    }

    /**
     * Get currencyFrom
     *
     * @return string 
     */
    public function getCurrencyFrom()
    {
        return $this->currencyFrom;
    }

    /**
     * Set currencyTo
     *
     * @param string $currencyTo
     * @return FxRate
     */
    public function setCurrencyTo($currencyTo)
    {
        $this->currencyTo = $currencyTo;

        return $this;
    }

    /**
     * Get currencyTo
     *
     * @return string 
     */
    public function getCurrencyTo()
    {
        return $this->currencyTo;
    }

    /**
     * Set rate
     *
     * @param float $rate
     * @return FxRate
     */
    public function setRate($rate)
    {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float 
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set createOn
     *
     * @param \DateTime $createOn
     * @return FxRate
     */
    public function setCreateOn($createOn)
    {
        $this->createOn = $createOn;

        return $this;
    }

    /**
     * Get createOn
     *
     * @return \DateTime 
     */
    public function getCreateOn()
    {
        return $this->createOn;
    }

    /**
     * Set updatedOn
     *
     * @param \DateTime $updatedOn
     * @return FxRate
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
}
