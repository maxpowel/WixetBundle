<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="favourite")
 */
class Favourite implements Timestampable
{
	
	public function __construct()
	{
		$this->profiles = new \Doctrine\Common\Collections\ArrayCollection();
	}
	
	
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="favourites")
     */
     protected $profiles;
    
    /**
     * @ORM\Column(type="string")
     */
     protected $name; 
      
	
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
    
    public function getProfiles(){
    	return $this->profiles;
    }
    
    public function getName(){
    	return $this->name;
    }
    
    public function getCreated(){
    	return $this->created;
    }
    
    public function getUpdated(){
    	return $this->updated;
    }
    
    
    public function setName($var){
    	$this->name = $var;
    }

    
    public function setCreated($var){
    	$this->created = $var;
    }
    
    public function setUpdated($var){
    	$this->updated = $var;
    }
    
}
