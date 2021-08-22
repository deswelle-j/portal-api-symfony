<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 *  @UniqueEntity(
 *  fields={"email"},
 *  message="Cet email est déjà utilisé"
 * )
 */
class User
{
    /**
     * @Groups({"usersList"})
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"usersList"})
     * @Assert\Email(
     *  message = "Le champ email est invalide."
     * )
     * @Assert\NotBlank
     * @Assert\Length(
     *  minMessage = "Le email doit comporter au moins 8 caractères.",
     *  min = 8
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"usersList"})
     * @Assert\NotBlank
     * @Assert\Length(
     *  minMessage = "Le prenom doit comporter au moins 8 caractères.",
     *  min = 8
     * )
     */
    private $firstname;

    /**

     * @ORM\Column(type="string", length=255)
     * @Groups({"usersList"})
     * @Assert\NotBlank
     * @Assert\Length(
     *  minMessage = "Le mom doit comporter au moins 8 caractères.",
     *  min = 8
     * )
     */
    private $lastname;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"usersList"})
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}