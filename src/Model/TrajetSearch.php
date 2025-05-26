<?php
namespace App\Model;

use DateTimeInterface;

class TrajetSearch
{
    public ?string $villeDepart = null;
    public ?string $villeArrivee = null;
    public ?DateTimeInterface $date = null;
}
