<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_container_has_items")
 */
class itemContainerHasItems implements Timestampable
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ItemContainer")
     * @ORM\JoinColumn(name="item_container_id", referencedColumnName="id", nullable=false)
     */
     protected $itemContainer;
     
     /**
     * @ORM\Column(type="integer")
     */
     protected $object_id;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false)
     */
     protected $objectType;
     
     
     public function setItemContainer($itemContainer){
     	$this->itemContainer = $itemContainer;
     }
     
     
     public function getItemContainer(){
     	return $this->itemContainer;
     }
     
     public function setObjectId($o){
     	$this->object_id = $o;
     }
     
     public function setObjectType($objectType){
     	$this->objectType = $objectType;
     }
     
     
    
}
