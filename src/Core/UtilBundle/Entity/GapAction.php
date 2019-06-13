<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * GapAction
 *
 * @ORM\Table(name="gap_action")
 * @ORM\Entity
 */
class GapAction
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
     * @ORM\Column(name="action_name", type="string", length=250, nullable=true)
     */
    private $actionName;



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
     * Set actionName
     *
     * @param string $actionName
     *
     * @return GapAction
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;

        return $this;
    }

    /**
     * Get actionName
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }
}
