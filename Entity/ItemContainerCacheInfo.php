<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_container_cache_info")
 */
class ItemContainerCacheInfo
{
     
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
     /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ItemContainer")
     * @ORM\JoinColumn(name="item_container_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $itemContainer;
     
     public function setProfile($var){
     	$this->profile = $var;
     	
     }
     
     public function setItemContainer($var){
     	$this->itemContainer = $var;
     
     }
     
     
   
}
