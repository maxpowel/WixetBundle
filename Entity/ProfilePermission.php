<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_permission")
 */
class ProfilePermission
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
     
	
    
}
