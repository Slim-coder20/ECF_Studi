<?php

namespace App\Entity;

use App\Repository\VehiculeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehiculeRepository::class)]
class Vehicule
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $marque = null;

    #[ORM\Column(length: 255)]
    private ?string $modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $couleur = null;

    #[ORM\Column(length: 255)]
    private ?string $plaqueImmatriculation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $datePremiereImmatriculation = null;

    #[ORM\Column]
    private ?int $nbPlaces = null;

    #[ORM\Column]
    private  bool $isElectrique = false;

    #[ORM\Column]
    private  bool $accepteFumeur = false; 
   
    #[ORM\Column]
    private  bool $accepteAnimaux = false;

    #[ORM\ManyToOne(inversedBy: 'vehicules')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $proprietaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMarque(): ?string
    {
        return $this->marque;
    }

    public function setMarque(string $marque): static
    {
        $this->marque = $marque;

        return $this;
    }

    public function getModele(): ?string
    {
        return $this->modele;
    }

    public function setModele(string $modele): static
    {
        $this->modele = $modele;

        return $this;
    }

    public function getCouleur(): ?string
    {
        return $this->couleur;
    }

    public function setCouleur(?string $couleur): static
    {
        $this->couleur = $couleur;

        return $this;
    }

    public function getPlaqueImmatriculation(): ?string
    {
        return $this->plaqueImmatriculation;
    }

    public function setPlaqueImmatriculation(string $plaqueImmatriculation): static
    {
        $this->plaqueImmatriculation = $plaqueImmatriculation;

        return $this;
    }

    public function getDatePremiereImmatriculation(): ?\DateTime
    {
        return $this->datePremiereImmatriculation;
    }

    public function setDatePremiereImmatriculation(?\DateTime $date): static
    {
        $this->datePremiereImmatriculation = $date;

        return $this;
    }

    public function getNbPlaces(): ?int
    {
        return $this->nbPlaces;
    }

    public function setNbPlaces(int $nbPlaces): static
    {
        $this->nbPlaces = $nbPlaces;

        return $this;
    }

    public function isElectrique(): bool
    {
        return $this->isElectrique;
    }

    public function setIsElectrique(bool $isElectrique): static
    {
        $this->isElectrique = $isElectrique;

        return $this;
    }

    public function isAccepteFumeur(): bool
    {
        return $this->accepteFumeur;
    }

    public function setAccepteFumeur(bool $accepteFumeur): static
    {
        $this->accepteFumeur = $accepteFumeur;

        return $this;
    }

    public function isAccepteAnimaux(): bool
    {
        return $this->accepteAnimaux;
    }

    public function setAccepteAnimaux(bool $accepteAnimaux): static
    {
        $this->accepteAnimaux = $accepteAnimaux;

        return $this;
    }

    public function getProprietaire(): ?User
    {
        return $this->proprietaire;
    }

    public function setProprietaire(?User $proprietaire): static
    {
        $this->proprietaire = $proprietaire;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
