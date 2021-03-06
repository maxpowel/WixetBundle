<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_update")
 */
class ProfileUpdate implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
     /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\ProfileUpdateComment", mappedBy="profile_update", fetch="EXTRA_LAZY")
     */
     protected $comments;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="newness")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
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
    
    public function getBody(){
    	return $this->body;
    }
    
    public function getComments(){
    	return $this->comments;
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
