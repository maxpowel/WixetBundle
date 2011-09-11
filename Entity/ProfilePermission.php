<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_permission")
 */
class ProfilePermission
{
    
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

    public function getRealItemId() {
        return $this->real_item_id;
    }

    public function setRealItemId($real_item_id) {
        $this->real_item_id = $real_item_id;
    }

    public function getProfile() {
        return $this->profile;
    }

    public function setProfile($profile) {
        $this->profile = $profile;
    }

    public function getAlbum() {
        return $this->album;
    }

    public function setAlbum($album) {
        $this->album = $album;
    }

    public function getObjectType() {
        return $this->object_type;
    }

    public function setObjectType($object_type) {
        $this->object_type = $object_type;
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
     protected $real_item_id; 
     
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\Album")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id", nullable=false)
     */
     protected $album;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false)
     */
     protected $object_type;
     

     /**
     * @ORM\Column(type="datetime")
     */
     protected $object_creation_time; 
    
}