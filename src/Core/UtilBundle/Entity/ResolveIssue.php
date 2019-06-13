<?php

namespace UtilBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ResolveIssue
 *
 * @ORM\Table(name="resolve_issue", indexes={@ORM\Index(name="FK_resolve_issue", columns={"resolve_id"}), @ORM\Index(name="FK_resolve_issue_1", columns={"issue_id"})})
 * @ORM\Entity
 */
class ResolveIssue
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
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var \Issue
     *
     * @ORM\ManyToOne(targetEntity="Issue")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="issue_id", referencedColumnName="id")
     * })
     */
    private $issue;

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
     * @return ResolveIssue
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
     * Set issue
     *
     * @param \UtilBundle\Entity\Issue $issue
     *
     * @return ResolveIssue
     */
    public function setIssue(\UtilBundle\Entity\Issue $issue = null)
    {
        $this->issue = $issue;

        return $this;
    }

    /**
     * Get issue
     *
     * @return \UtilBundle\Entity\Issue
     */
    public function getIssue()
    {
        return $this->issue;
    }

    /**
     * Set resolve
     *
     * @param \UtilBundle\Entity\Resolve $resolve
     *
     * @return ResolveIssue
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
