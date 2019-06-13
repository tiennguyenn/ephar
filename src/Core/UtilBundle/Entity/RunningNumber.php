<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RunningNumber
 *
 * @ORM\Table(name="running_number")
 * @ORM\Entity
 */
class RunningNumber
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
     * @ORM\Column(name="running_number_code", type="string", length=10, nullable=true)
     */
    private $runningNumberCode;

    /**
     * @var integer
     *
     * @ORM\Column(name="running_number_value", type="integer", nullable=true)
     */
    private $runningNumberValue;



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
     * Set runningNumberCode
     *
     * @param string $runningNumberCode
     *
     * @return RunningNumber
     */
    public function setRunningNumberCode($runningNumberCode)
    {
        $this->runningNumberCode = $runningNumberCode;

        return $this;
    }

    /**
     * Get runningNumberCode
     *
     * @return string
     */
    public function getRunningNumberCode()
    {
        return $this->runningNumberCode;
    }

    /**
     * Set runningNumberValue
     *
     * @param integer $runningNumberValue
     *
     * @return RunningNumber
     */
    public function setRunningNumberValue($runningNumberValue)
    {
        $this->runningNumberValue = $runningNumberValue;

        return $this;
    }

    /**
     * Get runningNumberValue
     *
     * @return integer
     */
    public function getRunningNumberValue()
    {
        return $this->runningNumberValue;
    }
}
