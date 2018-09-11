<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MasterRepository")
 * @UniqueEntity("email")
 */
class Master implements UserInterface
{
    /**
     * @Groups("master")
     * @Groups("Company")
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @Groups("master")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $firstname;
    /**
     * @Groups("master")
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $lastname;
    /**
     * @Groups("master")
     * @Groups("Company")
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email.",
     *     checkMX = true
     * )
     * @Assert\NotBlank()
     */
    private $email;
    /**
     * @Groups("master")
     * @ORM\Column(type="string", length=255)
     */
    private $apiKey;
    /**
     * @Groups("master")
     *@Groups("master")
     * @ORM\OneToOne(targetEntity="App\Entity\Company", inversedBy="master")
     */
    private $company;
    /**
     * @ORM\Column(type="simple_array")
     */
    private $roles;
    public function __construct()
    {
        $this->roles = array('ROLE_USER');
        $this->apiKey = uniqid('', true);
    }
    public function getId(): ?int
    {
        return $this->id;
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
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }
    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;
        return $this;
    }
    public function getCompany(): ?Company
    {
        return $this->company;
    }
    public function setCompany(?Company $company): self
    {
        $this->company = $company;
        return $this;
    }
    public function getRoles(): array
    {
        return $this->roles;
    }
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }
    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}