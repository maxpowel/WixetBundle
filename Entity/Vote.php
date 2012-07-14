<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="vote")
 */
class Vote
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="updates")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
    
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false)
     */
     protected $objectType;

     /**
     * @ORM\Column(type="integer")
     */
     protected $object_id;
     
     /**
     * @ORM\Column(type="boolean")
     */
     protected $like;
     
     
     public function getId() {
     	return $this->id;
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
     
     public function setLike($val) {
     	$this->like = $val;
     }
     
     public function getLike() {
     	return $this->like;
     }
    
}
