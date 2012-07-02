<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_permission")
 */
class ProfilePermission
{
    
	public function getId() {
		return $this->id;
	}
	
    public function getReadGranted() {
        return $this->read_granted;
    }

    public function setReadGranted($read_granted) {
        $this->read_granted = $read_granted;
    }

    public function getReadDenied() {
        return $this->read_denied;
    }

    public function setReadDenied($read_denied) {
        $this->read_denied = $read_denied;
    }

    public function getWriteGranted() {
        return $this->write_granted;
    }

    public function setWriteGranted($write_granted) {
        $this->write_granted = $write_granted;
    }

    public function getWriteDenied() {
        return $this->write_denied;
    }

    public function setWriteDenied($write_denied) {
        $this->write_denied = $write_denied;
    }

    public function getObjectId() {
        return $this->object_id;
    }

    public function setObjectId($id) {
        $this->object_id = $id;
    }

    public function getProfile() {
        return $this->profile;
    }

    public function setProfile($profile) {
        $this->profile = $profile;
    }

    public function getOwner() {
        return $this->owner;
    }

    public function setOwner($owner) {
        $this->owner = $owner;
    }

    public function getObjectType() {
        return $this->objectType;
    }

    public function setObjectType($objectType) {
        $this->objectType = $objectType;
    }

    public function getObjectCreationTime() {
        return $this->object_creation_time;
    }

    public function setObjectCreationTime($object_creation_time) {
        $this->object_creation_time = $object_creation_time;
    }

        
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\Column(type="boolean")
     */
     protected $read_granted; 
     
    /**
     * @ORM\Column(type="boolean")
     */
     protected $read_denied; 
     
    /**
     * @ORM\Column(type="boolean")
     */
     protected $write_granted; 
      
    /**
     * @ORM\Column(type="boolean")
     */
     protected $write_denied; 
     
    /**
     * @ORM\Column(type="integer")
     */
     protected $object_id; 
     
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=true)
     */
     protected $owner;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false)
     */
     protected $objectType;
     

     /**
     * @ORM\Column(type="datetime")
     */
     protected $object_creation_time; 
    
}
