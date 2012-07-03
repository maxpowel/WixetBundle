<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_item_comment")
 */
class MediaItemComment implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\MediaItem", inversedBy="comments")
     * @ORM\JoinColumn(name="media_item_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $media_item;
     
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
    
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
    
    public function getMediaItem(){
    	return $this->media_item;	
    }
    
    public function getProfile(){
    	return $this->profile;
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
    

    public function setMediaItem($item){
    	$this->media_item = $item;
    }
    
    public function setProfile($item){
    	$this->profile = $item;
    }
    
    public function setBody($item){
    	$this->body = $item;
    }
    
    
}
