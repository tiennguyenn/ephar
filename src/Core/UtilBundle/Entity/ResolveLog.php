<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveLog
 *
 * @ORM\Table(name="resolve_log", indexes={@ORM\Index(name="FK_resolve_log", columns={"resolve_id"}), @ORM\Index(name="FK_resolve_log_1", columns={"log_id"})})
 * @ORM\Entity
 */
class ResolveLog
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \Log
     *
     * @ORM\ManyToOne(targetEntity="Log")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="log_id", referencedColumnName="id")
     * })
     */
    private $log;

    /**
     * @var \Resolve
     *
     * @ORM\ManyToOne(targetEntity="Resolve")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="resolve_id", referencedColumnName="id")
     * })
     */
    private $resolve;



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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return ResolveLog
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
     * Set log
     *
     * @param \UtilBundle\Entity\Log $log
     *
     * @return ResolveLog
     */
    public function setLog(\UtilBundle\Entity\Log $log = null)
    {
        $this->log = $log;

        return $this;
    }

    /**
     * Get log
     *
     * @return \UtilBundle\Entity\Log
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveLog
     */
    public function setResolve(\UtilBundle\Entity\Resolve $resolve = null)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * Get resolve
     *
     * @return \UtilBundle\Entity\Resolve
     */
    public function getResolve()
    {
        return $this->resolve;
    }
}
