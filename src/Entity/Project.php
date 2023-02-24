<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProjectRepository::class)
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=TimeLog::class, mappedBy="project")
     */
    private $timelog;

    public function __construct()
    {
        $this->timelog = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, TimeLog>
     */
    public function getTimelog(): Collection
    {
        return $this->timelog;
    }

    public function addTimelog(TimeLog $timelog): self
    {
        if (!$this->timelog->contains($timelog)) {
            $this->timelog[] = $timelog;
            $timelog->setProject($this);
        }

        return $this;
    }

    public function removeTimelog(TimeLog $timelog): self
    {
        if ($this->timelog->removeElement($timelog)) {
            // set the owning side to null (unless already changed)
            if ($timelog->getProject() === $this) {
                $timelog->setProject(null);
            }
        }

        return $this;
    }
}
