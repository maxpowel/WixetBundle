<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="private_message",indexes={@ORM\index(name="conversation", columns={"conversation_id"})})
 */
class PrivateMessage implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
     private $author;
    
    /**
     * @ORM\Column(type="string", length=1000)
     */
     protected $body; 
     
     
    /**
     * @ORM\Column(type="string")
     */
     protected $subject; 

     /**
     * @ORM\Column(type="boolean")
     */
     protected $isRoot; 
     
    /**
 	* @ORM\Column(type="string", columnDefinition="CHAR(13) NOT NULL")
 	*/
     protected $conversation_id; 
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", inversedBy="messages")
     * @ORM\JoinColumn(name="private_message_collection_id", referencedColumnName="id", nullable=true)
     */
     private $private_message_collection;
    
    
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
	 * @return the $author
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * @return the $body
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * @return the $subject
	 */
	public function getSubject() {
		return $this->subject;
	}

	/**
	 * @return the $isRoot
	 */
	public function getIsRoot() {
		return $this->isRoot;
	}

	/**
	 * @return the $conversation_id
	 */
	public function getConversationId() {
		return $this->conversation_id;
	}

	/**
	 * @return the $private_message_collection
	 */
	public function getPrivateMessageCollection() {
		return $this->private_message_collection;
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
	 * @param field_type $author
	 */
	public function setAuthor($author) {
		$this->author = $author;
	}

	/**
	 * @param field_type $body
	 */
	public function setBody($body) {
		$this->body = $body;
	}

	/**
	 * @param field_type $subject
	 */
	public function setSubject($subject) {
		$this->subject = $subject;
	}

	/**
	 * @param field_type $isRoot
	 */
	public function setIsRoot($isRoot) {
		$this->isRoot = $isRoot;
	}

	/**
	 * @param field_type $conversation_id
	 */
	public function setConversationId($conversation_id) {
		$this->conversation_id = $conversation_id;
	}

	/**
	 * @param field_type $private_message_collection
	 */
	public function setPrivateMessageCollection($private_message_collection) {
		$this->private_message_collection = $private_message_collection;
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
