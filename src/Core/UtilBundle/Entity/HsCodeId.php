<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HsCodeId
 *
 * @ORM\Table(name="hs_code_id", uniqueConstraints={@ORM\UniqueConstraint(name="hs_code", columns={"hs_code"})})
 * @ORM\Entity
 */
class HsCodeId
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
     * @ORM\Column(name="hs_code", type="string", length=50, nullable=false)
     */
    private $hsCode;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_id", type="text", length=65535, nullable=false)
     */
    private $descId;

    /**
     * @var string
     *
     * @ORM\Column(name="desc_en", type="text", length=65535, nullable=false)
     */
    private $descEn;

    /**
     * @var string
     *
     * @ORM\Column(name="import_duty", type="string", length=50, nullable=false)
     */
    private $importDuty;



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
     * Set hsCode
     *
     * @param string $hsCode
     *
     * @return HsCodeId
     */
    public function setHsCode($hsCode)
    {
        $this->hsCode = $hsCode;

        return $this;
    }

    /**
     * Get hsCode
     *
     * @return string
     */
    public function getHsCode()
    {
        return $this->hsCode;
    }

    /**
     * Set descId
     *
     * @param string $descId
     *
     * @return HsCodeId
     */
    public function setDescId($descId)
    {
        $this->descId = $descId;

        return $this;
    }

    /**
     * Get descId
     *
     * @return string
     */
    public function getDescId()
    {
        return $this->descId;
    }

    /**
     * Set descEn
     *
     * @param string $descEn
     *
     * @return HsCodeId
     */
    public function setDescEn($descEn)
    {
        $this->descEn = $descEn;

        return $this;
    }

    /**
     * Get descEn
     *
     * @return string
     */
    public function getDescEn()
    {
        return $this->descEn;
    }

    /**
     * Set importDuty
     *
     * @param string $importDuty
     *
     * @return HsCodeId
     */
    public function setImportDuty($importDuty)
    {
        $this->importDuty = $importDuty;

        return $this;
    }

    /**
     * Get importDuty
     *
     * @return string
     */
    public function getImportDuty()
    {
        return $this->importDuty;
    }
}
