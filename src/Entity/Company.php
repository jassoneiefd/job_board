<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: JobOffer::class, orphanRemoval: true)]
    private Collection $jobOffers;

    #[ORM\OneToOne(mappedBy: 'company', cascade: ['persist', 'remove'])]
    private ?CompanyOwner $owner = null;

    public function __construct()
    {
        $this->jobOffers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, JobOffer>
     */
    public function getJobOffers(): Collection
    {
        return $this->jobOffers;
    }

    public function addJobOffer(JobOffer $jobOffer): static
    {
        if (!$this->jobOffers->contains($jobOffer)) {
            $this->jobOffers->add($jobOffer);
            $jobOffer->setCompany($this);
        }

        return $this;
    }

    public function removeJobOffer(JobOffer $jobOffer): static
    {
        if ($this->jobOffers->removeElement($jobOffer)) {
            // set the owning side to null (unless already changed)
            if ($jobOffer->getCompany() === $this) {
                $jobOffer->setCompany(null);
            }
        }

        return $this;
    }

    public function __toString() 

    {
        return $this->getName();
    }

    public function getOwner(): ?CompanyOwner
    {
        return $this->owner;
    }

    public function setOwner(?CompanyOwner $owner): static
    {
        // unset the owning side of the relation if necessary
        if ($owner === null && $this->owner !== null) {
            $this->owner->setCompany(null);
        }

        // set the owning side of the relation if necessary
        if ($owner !== null && $owner->getCompany() !== $this) {
            $owner->setCompany($this);
        }

        $this->owner = $owner;

        return $this;
    }



}
