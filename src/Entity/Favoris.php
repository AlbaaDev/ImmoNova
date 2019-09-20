<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FavorisRepository")
 */
class Favoris
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $items = [];

    /**
     * @ORM\Column(type="string")
     *
     */
    private $session_id;

    /**
     * @OneToOne(targetEntity="User", inversedBy="favoris")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    public function __construct($favorisArray, $session_id, ?User $user) {
        $this->items = $favorisArray;
        $this->session_id = $session_id;
        $this->user = $user;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getItems(): ?array
    {
        return $this->items;
    }

    public function setItems(?array $items): self
    {
        $this->items = $items;

        return $this;
    }

    public function addItems(?array $items) : self {
        $this->items = array_merge($items, $this->items);
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionId() : string {
        return $this->session_id;
    }

    /**
     * @param mixed $session_id
     *
     * @return Favoris
     */
    public function setSessionId($session_id) {
        $this->session_id = $session_id;
        return $this;
    }

    /**
     * @param mixed $user
     *
     * @return Favoris
     */
    public function setUser(?User $user) : self {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser() {
        return $this->user;
    }

}
