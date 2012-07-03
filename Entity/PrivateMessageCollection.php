<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="private_message_collection")
 */
class PrivateMessageCollection implements Timestampable
{
	public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


	/**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="private_messages_collections")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
    /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", mappedBy="private_message_collection")
     */
	protected $messages;

    /**
     * @ORM\Column(type="string")
     */
    private $name;

    
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
	 * @return the $messages
	 */
	public function getMessages() {
		return $this->messages;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
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
	 * @param field_type $messages
	 */
	public function setMessages($messages) {
		$this->messages = $messages;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
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
