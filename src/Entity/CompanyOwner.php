<?php

namespace App\Entity;

use App\Repository\CompanyOwnerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyOwnerRepository::class)]
//#[ORM\InheritanceType(value:'JOINED')]
//#[ORM\DiscriminatorColumn(name:'type', columnDefinition:'DEFAULT "APP"')]
//#[ORM\DiscriminatorMap(value:{"APP"="Applicant", "CPY"="CompanyOwner" } )]

class CompanyOwner extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'owner', cascade: ['persist', 'remove'])]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
