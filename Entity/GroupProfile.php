<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="group_profile")
 * @Gedmo\Loggable
 */
class GroupProfile implements Timestampable
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
     protected $name;
     
    /**
     * @ORM\Column(type="boolean")
     */
     protected $public; 
     
     /**
	 * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\User", inversedBy="group_profiles")
	 * @ORM\JoinTable(name="user_group_profile",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="group_profile_id", referencedColumnName="id")}
	 *      )
	 */
	private $users;

     
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
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\GroupProfileType")
     * @ORM\JoinColumn(name="group_profile_type_id", referencedColumnName="id", nullable=false)
     */
     protected $type;
	
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
    
}
