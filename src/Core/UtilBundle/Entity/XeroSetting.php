<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * XeroSetting
 *
 * @ORM\Table(name="xero_setting")
 * @ORM\Entity(repositoryClass="UtilBundle\Repository\XeroSettingRepository")
 */
class XeroSetting
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
     * @ORM\Column(name="gmeds_code", type="string", length=50, nullable=false)
     */
    private $gmedsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="api_token", type="string", length=255, nullable=false)
     */
    private $apiToken;

    /**
     * @var string
     *
     * @ORM\Column(name="api_secret", type="string", length=255, nullable=false)
     */
    private $apiSecret;



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
     * Set gmedsCode
     *
     * @param string $gmedsCode
     *
     * @return XeroSetting
     */
    public function setGmedsCode($gmedsCode)
    {
        $this->gmedsCode = $gmedsCode;

        return $this;
    }

    /**
     * Get gmedsCode
     *
     * @return string
     */
    public function getGmedsCode()
    {
        return $this->gmedsCode;
    }

    /**
     * Set apiToken
     *
     * @param string $apiToken
     *
     * @return XeroSetting
     */
    public function setApiToken($apiToken)
    {
        $this->apiToken = $apiToken;

        return $this;
    }

    /**
     * Get apiToken
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->apiToken;
    }

    /**
     * Set apiSecret
     *
     * @param string $apiSecret
     *
     * @return XeroSetting
     */
    public function setApiSecret($apiSecret)
    {
        $this->apiSecret = $apiSecret;

        return $this;
    }

    /**
     * Get apiSecret
     *
     * @return string
     */
    public function getApiSecret()
    {
        return $this->apiSecret;
    }
}
