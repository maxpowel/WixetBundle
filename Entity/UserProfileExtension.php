<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_profile_extension")
 */
class UserProfileExtension implements Timestampable
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="extensions")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
    
    /**
     * @ORM\Column(type="string")
     */
     protected $title; 
     
    /**
     * @ORM\Column(columnDefinition="TEXT NOT NULL")
     */
     protected $body; 
	
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
    
    
    public function getId(){
    	return $this->id;
    }
    
    public function getProfile(){
    	return $this->profile;
    }
    
    public function getTitle(){
    	return $this->title;
    }
    
    public function getBody(){
    	return $this->body;
    }
    
    public function getCreated(){
    	return $this->created;
    }
    
    public function getUpdated(){
    	return $this->updated;
    }
    
    
    public function setProfile($var){
    	$this->profile = $var;
    }
    
    public function setTitle($var){
    	$this->title = $var;
    }
    
    public function setBody($var){
    	$this->body = $var;
    }
    
    public function setCreated($var){
    	$this->created = $var;
    }
    
    public function setUpdated($var){
    	$this->updated = $var;
    }
    
}
