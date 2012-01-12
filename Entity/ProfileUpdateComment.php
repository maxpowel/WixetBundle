<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_update_comment")
 */
class ProfileUpdateComment implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ProfileUpdate", inversedBy="profile_update")
     * @ORM\JoinColumn(name="profile_update_id", referencedColumnName="id", nullable=false)
     */
     protected $profile_update;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
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
    
    public function setProfile($profile){
    	$this->profile = $profile;
    }
    
    public function getAuthor(){
    	return $this->author;
    }
    
    public function setAuthor($author){
    	$this->author = $author;
    }
    
    public function setProfileUpdate($update){
    	$this->profile_update = $update;
    }
    
    public function getProfileUpdate(){
    	return $this->profile_update;
    }
    
    public function getBody(){
    	return $this->body;
    }
    
    public function setBody($body){
    	$this->body = $body;
    }
    
    public function getCreated(){
    	return $this->created;
    }
    
    public function getUpdated(){
    	return $this->updated;
    }
    
    
    
    
    
    
}
