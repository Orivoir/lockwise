<?php

namespace App\Entity;

use Faker\Factory;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{

    public const FACTORY = true;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @NotNull( message="plateform cant be empty" )
     */
    private $plateform;

    /**
     * @ORM\Column(type="text")
     * @NotNull( message="password cant be empty" )
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt = NULL;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isRemove = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFavorite = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $login = NULL;

    /**
     * @ORM\Column(type="datetime" , nullable=true )
     */
    private $removeAt = NULL;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\CodeRecup", 
     *      mappedBy="account", 
     *      orphanRemoval=true,
     *      cascade={"persist"} ,
     *      fetch="EAGER"
     * )
     */
    private $codeRecups;

    /**
     * @var factory is an factory entity to build
     */
    public function __construct( $factory = false ) {

        if( $factory ) {

            $faker = Factory::create('fr_FR') ;
            $this->createAt = $faker->dateTimeBetween('-15days' , 'now' );

        } else {
            $this->createAt = new \DateTime() ;
        }
        $this->codeRecups = new ArrayCollection();
    }

    /**
     * @return DateTimeInterface attribute represent last update `createAt` if not update but `updateAt` if already update
     */
    public function getRefUpdate(): \DateTimeInterface {

        return $this->updateAt ?? $this->createAt;
    }
    
    /**
     * @return int second time between last update and now 
     */
    public function getDiffLastUpdate(): ?int {

        $refUpdate = $this->getRefUpdate() ;

        return ( new \DateTime() )->getTimestamp() - $refUpdate->getTimestamp() ;
    }

    /**
     * @return int days time between last update and now
     */
    public function getDiffDaysLastUpdate(): ?int {

        return \round( $this->getDiffLastUpdate()  / 60 / 60 / 24  ) ;
    }

    /**
     * @return int week time between last update and now
     */
    public function getDiffWeekLastUpdate(): ?int {

        return \floor( $this->getDiffLastUpdate()  / 60 / 60 / 24 / 7  ) ;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlateform(): ?string
    {
        return $this->plateform;
    }

    public function setPlateform(string $plateform): self
    {
        $this->plateform = $plateform;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeInterface
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeInterface $createAt): self
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getIsRemove(): ?bool
    {
        return $this->isRemove;
    }

    public function setIsRemove(bool $isRemove): self
    {
        $this->isRemove = $isRemove;

        if( $this->isRemove ) {
            $this->removeAt = new \DateTime();
            $this->isFavorite = false;
        }

        return $this;
    }

    public function getIsFavorite(): ?bool
    {
        return $this->isFavorite;
    }

    /**
     * warn low performence getter
     */
    public function getSlug(): ?string {

        return ( new Slugify() )->slugify( $this->plateform ) ;
    }

    public function setIsFavorite(bool $isFavorite): self
    {

        if( $this->isRemove ) return $this;

        $this->isFavorite = $isFavorite;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getRemoveAt(): ?\DateTimeInterface
    {
        return $this->removeAt;
    }

    public function setRemoveAt(\DateTimeInterface $removeAt): self
    {
        $this->removeAt = $removeAt;

        return $this;
    }

    /**
     * @return Collection|CodeRecup[]
     */
    public function getCodeRecups(): Collection
    {
        $codeFilter = new ArrayCollection() ;

        foreach( $this->codeRecups as $codeRecup ) {

            if( !$codeRecup->getIsRemove() )
                $codeFilter[] = $codeRecup;
        }

        return $codeFilter ;
    }

    
    public function removeCodeRecup(CodeRecup $code) {}

    public function addCodeRecup(CodeRecup $codeRecup): self
    {
        if (!$this->codeRecups->contains($codeRecup)) {
            $this->codeRecups[] = $codeRecup;
            $codeRecup->setAccount($this);
        }

        return $this;
    }

}
