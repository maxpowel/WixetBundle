<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profile")
 */
class UserProfile implements Timestampable
{
	public function __construct()
    {
        $this->updates = new \Doctrine\Common\Collections\ArrayCollection();
        $this->private_messages_collections = new \Doctrine\Common\Collections\ArrayCollection();
        $this->favourites = new \Doctrine\Common\Collections\ArrayCollection();
        $this->extensions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->itemContainers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->profile_groups = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\OneToOne(targetEntity="Wixet\WixetBundle\Entity\User", inversedBy="profile")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\ProfileUpdate", mappedBy="profile", fetch="EXTRA_LAZY")
     */
	protected $updates;
	
	
	/**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", mappedBy="profile")
     */
	protected $private_messages_collections;
	
	/**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\Favourite", mappedBy="profiles")
     */
	protected $favourites;
	
	/**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\ItemContainer", mappedBy="profile")
     */
	protected $item_containers;
	
        
	/**
	* @ORM\OneToOne(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection")
	*/
	protected $main_private_message_collection;
	
	/**
	* @ORM\OneToOne(targetEntity="Wixet\WixetBundle\Entity\ItemContainer")
	*/
	protected $main_item_container;
	
	/**
	* @ORM\OneToOne(targetEntity="Wixet\WixetBundle\Entity\ProfileGroup")
	*/
	protected $main_group;
	
	/**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\MediaItem", inversedBy="id")
     * @ORM\JoinColumn(name="main_media_item_id", referencedColumnName="id")
     */
	protected $main_media_item;
        
    /**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\ProfileGroup", mappedBy="profiles")
     */
    protected $profile_groups;
    
    /**
    * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\UserProfileExtension", mappedBy="profile")
    */
    protected $extensions;
    
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

    public function getFirstName() {
        return $this->first_name;
    }

    public function setFirstName($first_name) {
        $this->first_name = $first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function setLastName($last_name) {
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
    
    public function getExtensions() {
    	return $this->extensions;
    }

    public function setUpdates($updates) {
        $this->updates = $updates;
    }
    
    public function setExtensions($ex) {
    	$this->extensions = $ex;
    }

    public function getPrivateMessagesCollections() {
        return $this->private_messages_collections;
    }
    
    public function getMainPrivateMessageCollection() {
    	return $this->main_private_message_collection;
    }
    
    public function getMainItemContainer() {
    	return $this->main_item_container;
    }


    public function getFavourites() {
        return $this->favourites;
    }

    public function setFavourites($favourites) {
        $this->favourites = $favourites;
    }

    public function getItemContainers() {
        return $this->item_containers;
    }

    public function setMainItemContainer($ic) {
    	$this->main_item_container = $ic;
    }
    
    public function setMainPrivateMessageCollection($coll) {
    	$this->main_private_message_collection = $coll;
    }
    
    public function setMainGroup($m) {
    	$this->main_group = $m;
    }
    
    public function getMainGroup() {
    	return $this->main_group;
    }
    
    /*public function setAlbums($albums) {
        $this->albums = $albums;
    }*/

    public function getCreated() {
        return $this->created;
    }



    public function getUpdated() {
        return $this->updated;
    }

    public function getProfile(){
    	return $this;
    }
    
    public function setMainMediaItem($item){
    	$this->main_media_item = $item;
    }
    
    public function getMainMediaItem(){
    	return $this->main_media_item; 
    }


}
