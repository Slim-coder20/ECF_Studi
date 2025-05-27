<?php
namespace App\Model;

use DateTimeInterface;

class TrajetSearch
{
    public ?string $villeDepart = null;
    public ?string $villeArrivee = null;
    public ?DateTimeInterface $date = null;
    public ?bool $ecoloOnly = null;
    public ?int $prixMax = null;
    public ?int $dureeMax = null; // en minutes
    public ?int $noteMin = null;
}
