<?php

namespace AM\UserBundle\Entity;


use AM\MusicBundle\Entity\Music;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * Administrator
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="AM\UserBundle\Repository\UserRepository")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements AdvancedUserInterface
{

    const ROLE_USER = 'ROLE_USER';
    const ROLE_MODERATOR = 'ROLE_MODERATOR';
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min=2, max=30)
     * @Assert\NotBlank()
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $password;

    /**
     * Ne seras pas stocké en BDD.
     * @Assert\Length(min=6)
     * @Assert\NotBlank()
     */
    protected $plainPassword;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $salt;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email()
     * @Assert\NotBlank()
     */
    protected $email;

    /**
     * @ORM\Column(type="array", length=255, unique=true)
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $enabled;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $accountNonLocked;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $accountNonExpired;
    /**
     * @ORM\Column(type="boolean")
     */
    protected $credentialsNonExpired;

    /**
     * @ORM\OneToMany(targetEntity="AM\MusicBundle\Entity\Music", mappedBy="user")
     */
    protected $musics;

    /**
     * @ORM\ManyToMany(targetEntity="AM\MusicBundle\Entity\Music", cascade={"persist"})
     * @ORM\JoinTable(name="UserFavMusic",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="music_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    protected $favMusics;

    //protected $playlists;



    public function __construct()
    {
        $this->musics = new ArrayCollection();
        $this->favMusics = new ArrayCollection();

        $this->salt = uniqid();
        $this->roles = array(self::ROLE_USER);
        $this->enabled = $this->accountNonLocked = $this->accountNonExpired = $this->credentialsNonExpired = true;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword()
    {

        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles()
    {
        $roles = $this->roles;

        $roles[] = self::ROLE_USER;

        return array_unique($roles);
    }

    public function addRole($role)
    {
        $role = strtoupper($role);
        if(!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function setRoles($roles)
    {
        $this->roles = array();
        foreach($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function removeRole($role)
    {
        $role = strtoupper($role);
        if(in_array($role, $this->roles)) {
            $roleKey = array_search($role, $this->roles);
            unset($this->roles[$roleKey]);
        }

        return $this;
    }

  /*  public function setFavMusics($favMusics)
    {
        $this->favMusics = array();
        foreach($favMusics as $favMusic) {
            $this->addRole($favMusic);
        }

        return $this;
    }
*/
    public function getFavMusics()
    {
        return $this->favMusics;
    }

    public function addFavMusic(Music $music)
    {
        $this->favMusics[] = $music;

        return $this;
    }

    public function removeFavMusic(Music $music)
    {
        $this->favMusics->removeElement($music);

        return $this;
    }

    public function haveFav(Music $music)
    {
        return $this->favMusics->contains($music);
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    public function setAccountNonLocked($accountNonLocked)
    {
        $this->accountNonLocked = $accountNonLocked;

        return $this;
    }

    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    public function setAccountNonExpired($accountNonExpired)
    {
        $this->accountNonExpired = $accountNonExpired;

        return $this;
    }

    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    public function setCredentialsNonExpired($credentialsNonExpired)
    {
        $this->credentialsNonExpired = $credentialsNonExpired;

        return $this;
    }
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    public static function getExistingRoles()
    {
        return array(
            self::ROLE_USER => 'user',
            self::ROLE_MODERATOR => 'moderator',
        );
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function getAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    public function getAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    public function getCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    public function addMusic(Music $musics)
    {
        $this->musics[] = $musics;

        return $this;
    }

    public function removeMusic(Music $musics)
    {
        $this->musics->removeElement($musics);
    }

    public function getMusics()
    {
        return $this->musics;
    }
}
