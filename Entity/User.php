<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\Loggable
 */
class User extends BaseUser implements Timestampable
{
	public function __construct()
    {
        parent::__construct();
        
        $this->profiles = new \Doctrine\Common\Collections\ArrayCollection();
        // your own logic
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", mappedBy="user")
     */
    protected $profile;
	

    /**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    
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
	/**
	 * @return the $id
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * @return the $profile
	 */
	public function getProfile() {
		return $this->profile;
	}

	/**
	 * @return the $groups
	 */
	public function getGroups() {
		return $this->groups;
	}

	/**
	 * @return the $created
	 */
	public function getCreated() {
		return $this->created;
	}

	/**
	 * @return the $updated
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param field_type $id
	 */
	public function setId($id) {
		$this->id = $id;
	}

	/**
	 * @param field_type $profile
	 */
	public function setProfile($profile) {
		$this->profile = $profile;
	}

	/**
	 * @param field_type $groups
	 */
	public function setGroups($groups) {
		$this->groups = $groups;
	}

	/**
	 * @param datetime $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

	/**
	 * @param datetime $updated
	 */
	public function setUpdated($updated) {
		$this->updated = $updated;
	}

}
