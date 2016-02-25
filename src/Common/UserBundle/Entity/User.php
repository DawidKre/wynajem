<?php

namespace Common\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="Common\UserBundle\Repository\UserRepository")
 * @ORM\HasLifecycleCallbacks()
 *
 * @UniqueEntity(fields={"username"})
 * @UniqueEntity(fields={"email"})
 *
 */
class User implements AdvancedUserInterface, \Serializable
{


    const DEFAULT_AVATAR = 'default-avatar.jpg';
    const UPLOAD_DIR = 'uploads/avatars/';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     *
     * @Assert\NotBlank(
     *     groups={"Registration", "ChangeDetails"}
     * )
     * @Assert\Length(
     *     min="5",
     *     max="20",
     *     groups={"Registration", "ChangeDetails"}
     * )
     */
    private $username;

    /**
     * @ORM\Column(type="string", length = 120, unique = true)
     *
     * @Assert\NotBlank(
     *      groups = {"Registration"}
     * )
     *
     * @Assert\Email(
     *      groups = {"Registration"}
     * )
     *
     * @Assert\Length(
     *      max = 120,
     *      groups = {"Registration"}
     * )
     */
    private $email;
    /**
     * @ORM\Column(type="string", length=64)
     *
     */
    private $password;


    /**
     * @Assert\NotBlank(
     *      groups = {"Registration", "ChangePassword"}
     * )
     *
     * @Assert\Length(
     *      min = 8,
     *      groups = {"Registration", "ChangePassword"}
     * )
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="boolean", name="is_account_non_expired")
     */
    private $isAccountNonExpired = true;

    /**
     * @ORM\Column(type="boolean", name="is_account_non_locked")
     */
    private $isAccountNonLocked = true;

    /**
     * @ORM\Column(type="boolean", name="is_credentials_non_expired")
     */
    private $isCredentialsNonExpired = true;

    /**
     * @ORM\Column(type="boolean", name="is_enabled")
     */
    private $isEnabled = false;

    /**
     * @ORM\Column(type="array", name="roles")
     */
    private $roles;

    /**
     * @ORM\Column(type="string", name="action_token", nullable=true, length=20)
     */
    private $actionToken;

    /**
     * @ORM\Column(type="datetime", name="register_date")
     */
    private $registerDate;

    /**
     * @ORM\Column(type="string", name="avatar", length=100, nullable=true)
     */
    private $avatar;


    /**
     * @var UploadedFile
     *
     * @Assert\Image(
     *      minWidth = 50,
     *      maxWidth = 250,
     *      minHeight = 50,
     *      maxHeight = 250,
     *      maxSize = "1M",
     *      groups = {"ChangeDetails"}
     * )
     */
    private $avatarFile;

    private $avatarTemp;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $updateDate;

    public function __construct()
    {
        $this->registerDate = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return Role[] The user roles
     */
    public function getRoles()
    {
        if (empty($this->roles)) {
            return array('ROLE_USER');
        }

        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {

        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

        $this->plainPassword = null;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw an AccountExpiredException and prevent login.
     *
     * @return bool true if the user's account is non expired, false otherwise
     *
     * @see AccountExpiredException
     */
    public function isAccountNonExpired()
    {
        return $this->isAccountNonExpired;
    }

    /**
     * Checks whether the user is locked.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a LockedException and prevent login.
     *
     * @return bool true if the user is not locked, false otherwise
     *
     * @see LockedException
     */
    public function isAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a CredentialsExpiredException and prevent login.
     *
     * @return bool true if the user's credentials are non expired, false otherwise
     *
     * @see CredentialsExpiredException
     */
    public function isCredentialsNonExpired()
    {
        return $this->isAccountNonExpired;
    }

    /**
     * Checks whether the user is enabled.
     *
     * Internally, if this method returns false, the authentication system
     * will throw a DisabledException and prevent login.
     *
     * @return bool true if the user is enabled, false otherwise
     *
     * @see DisabledException
     */
    public function isEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set isAccountNonExpired
     *
     * @param boolean $isAccountNonExpired
     * @return User
     */
    public function setIsAccountNonExpired($isAccountNonExpired)
    {
        $this->isAccountNonExpired = $isAccountNonExpired;

        return $this;
    }

    /**
     * Get isAccountNonExpired
     *
     * @return boolean
     */
    public function getIsAccountNonExpired()
    {
        return $this->isAccountNonExpired;
    }

    /**
     * Set isAccountNonLocked
     *
     * @param boolean $isAccountNonLocked
     * @return User
     */
    public function setIsAccountNonLocked($isAccountNonLocked)
    {
        $this->isAccountNonLocked = $isAccountNonLocked;

        return $this;
    }

    /**
     * Get isAccountNonLocked
     *
     * @return boolean
     */
    public function getIsAccountNonLocked()
    {
        return $this->isAccountNonLocked;
    }

    /**
     * Set isCredentialsNonExpired
     *
     * @param boolean $isCredentialsNonExpired
     * @return User
     */
    public function setIsCredentialsNonExpired($isCredentialsNonExpired)
    {
        $this->isCredentialsNonExpired = $isCredentialsNonExpired;

        return $this;
    }

    /**
     * Get isCredentialsNonExpired
     *
     * @return boolean
     */
    public function getIsCredentialsNonExpired()
    {
        return $this->isCredentialsNonExpired;
    }

    /**
     * Set isEnabled
     *
     * @param boolean $isEnabled
     * @return User
     */
    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;

        return $this;
    }

    /**
     * Get isEnabled
     *
     * @return boolean
     */
    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * Set roles
     *
     * @param array $roles
     * @return User
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Set actionToken
     *
     * @param string $actionToken
     * @return User
     */
    public function setActionToken($actionToken)
    {
        $this->actionToken = $actionToken;

        return $this;
    }

    /**
     * Get actionToken
     *
     * @return string
     */
    public function getActionToken()
    {
        return $this->actionToken;
    }

    /**
     * Set registerDate
     *
     * @param \DateTime $registerDate
     * @return User
     */
    public function setRegisterDate($registerDate)
    {
        $this->registerDate = $registerDate;

        return $this;
    }

    /**
     * Get registerDate
     *
     * @return \DateTime
     */
    public function getRegisterDate()
    {
        return $this->registerDate;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        if (null == $this->avatar) {
            return User::UPLOAD_DIR.User::DEFAULT_AVATAR;
        }

        return User::UPLOAD_DIR.$this->avatar;
    }

    /**
     * @return UploadedFile
     */
    public function getAvatarFile()
    {
        return $this->avatarFile;
    }

    /**
     * @param UploadedFile $avatarFile
     * @return User
     */
    public function setAvatarFile(UploadedFile $avatarFile)
    {
        $this->avatarFile = $avatarFile;
        $this->updateDate = new \DateTime();

        return $this;
    }

    /**
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     * @since 5.1.0
     */
    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->username,
                $this->password,
            )
        );
    }

    /**
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->username,
            $this->password
            ) = unserialize($serialized);
    }

    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return User
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preSave()
    {
        if (null !== $this->getAvatarFile()) {
            if (null !== $this->avatar) {
                $this->avatarTemp = $this->avatar;
            }
            $avatarName = sha1(uniqid(null, true));

            $this->avatar = $avatarName.'.'.$this->getAvatarFile()->guessExtension();
        }
    }


    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function postSave()
    {
        if (null !== $this->getAvatarFile()) {
            $this->getAvatarFile()->move($this->getUploadRootDir(), $this->avatar);
            unset($this->avatarFile);

            if (null !== $this->avatarTemp) {
                unlink($this->getUploadRootDir().$this->avatarTemp);
                unset($this->avatarTemp);
            }
        }
    }

    protected function getUploadRootDir()
    {
        return __DIR__.'/../../../../web/'.User::UPLOAD_DIR;
    }

    public function setAccountExpired($accountNonExpired)
    {
        $this->isAccountNonExpired = !$accountNonExpired;

        return $this;
    }

    public function getAccountExpired()
    {
        return !$this->isAccountNonExpired;
    }

    public function setAccountLocked($accountNonLocked)
    {
        $this->isAccountNonLocked = !$accountNonLocked;

        return $this;
    }

    public function getAccountLocked()
    {
        return !$this->isAccountNonLocked;
    }

    public function setCredentialsExpired($credentialsNonExpired)
    {
        $this->isCredentialsNonExpired = !$credentialsNonExpired;

        return $this;
    }

    public function getCredentialsExpired()
    {
        return !$this->isCredentialsNonExpired;
    }
}
