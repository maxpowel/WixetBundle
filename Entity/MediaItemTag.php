<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_item_tag",uniqueConstraints={@ORM\UniqueConstraint(name="media_tag_profile", columns={"media_item_id", "profile_id"})})
 */
class MediaItemTag implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\MediaItem", inversedBy="comments")
     * @ORM\JoinColumn(name="media_item_id", referencedColumnName="id", nullable=false)
     */
     protected $media_item;
     
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
    
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", nullable=false)
     */
     protected $owner; 
     
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
    
    /** @ORM\Column(type="decimal") */
    private $posleft;
    
    /** @ORM\Column(type="decimal") */
    private $postop;
    
    
    
    public function getPosition(){
    	return array("left"=>$this->posleft, "top"=>$this->postop);
    }
    
    public function setPosition($position){
    	if(isset($position['left']))
    		$this->posleft = $position['left'];
    	
    	if(isset($position['top']))
    		$this->postop = $position['top'];
    }
    
    public function getId(){
    	return $this->id;
    }
    
    public function getMediaItem(){
    	return $this->media_item;	
    }
    
    public function getProfile(){
    	return $this->profile;
    }
    
    public function getOwner(){
    	return $this->owner;
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
    
    public function setOwner($item){
    	$this->owner = $item;
    }
    
    
}
