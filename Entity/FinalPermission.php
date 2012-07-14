<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="final_permission")
 */
class FinalPermission
{
    
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
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ItemContainer")
     * @ORM\JoinColumn(name="item_container_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
     protected $itemContainer;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $owner;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false)
     */
     protected $objectType;
     
	
    /**
     * @ORM\Column(type="date")
     */
     protected $object_creation_time; 
     
     
     public function getReadGranted(){
     	return $this->read_granted;
     }
     
     public function getReadDenied(){
     	return $this->read_denied;
     }
     
     public function getWriteGranted(){
     	return $this->write_granted;
     }
      
     public function getWriteDenied(){
     	return $this->write_denied;
     }
    
}
