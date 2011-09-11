<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profile")
 * @Gedmo\Loggable
 */
class UserProfile implements Timestampable
{
	public function __construct()
    {
        $this->updates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->private_messages_collections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favourites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->albums = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\User", inversedBy="profiles")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
     protected $user;
    
    /**
     * @ORM\Column(type="string")
     */
     protected $first_name;
    
	/**
     * @ORM\Column(type="string")
     */
     protected $last_name; 
    
    /**
     * @ORM\Column(type="boolean")
     */
     protected $public; 
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\City", inversedBy="id")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="id")
     */
     private $city;
     
    /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\ProfileUpdate", mappedBy="profile")
     */
	protected $updates;
	
	
	/**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", mappedBy="profile")
     */
	protected $private_messages_collections;
	
	/**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\Favourite", mappedBy="profile")
     */
	protected $favourites;
	
	/**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\Album", mappedBy="profile")
     */
	protected $albums;
	
        
        
    /**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\ProfileGroup", mappedBy="profiles")
     */
    protected $profile_groups;
    
    
	/**
     * @var datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created;
     
    /**
     * @var datetime $updated
     *
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="update")
     */
    private $updated;
    
    public function getId() {
        return $this->id;
    }

    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getFirst_name() {
        return $this->first_name;
    }

    public function setFirst_name($first_name) {
        $this->first_name = $first_name;
    }

    public function getLast_name() {
        return $this->last_name;
    }

    public function setLast_name($last_name) {
        $this->last_name = $last_name;
    }

    public function getPublic() {
        return $this->public;
    }

    public function setPublic($public) {
        $this->public = $public;
    }

    public function getCity() {
        return $this->city;
    }

    public function setCity($city) {
        $this->city = $city;
    }

    public function getUpdates() {
        return $this->updates;
    }

    public function setUpdates($updates) {
        $this->updates = $updates;
    }

    public function getPrivate_messages_collections() {
        return $this->private_messages_collections;
    }

    public function setPrivate_messages_collections($private_messages_collections) {
        $this->private_messages_collections = $private_messages_collections;
    }

    public function getFavourites() {
        return $this->favourites;
    }

    public function setFavourites($favourites) {
        $this->favourites = $favourites;
    }

    public function getAlbums() {
        return $this->albums;
    }

    public function setAlbums($albums) {
        $this->albums = $albums;
    }

    public function getCreated() {
        return $this->created;
    }



    public function getUpdated() {
        return $this->updated;
    }



}
