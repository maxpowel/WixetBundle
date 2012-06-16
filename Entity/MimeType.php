<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="mime_type")
 */
class MimeType
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    
    /**
     * @ORM\Column(type="string")
     */
     protected $name;

     /**
     * @ORM\Column(type="string")
     */
     protected $extension;
     
     public function getName(){
     	return $this->name;
     }
     
     public function getExtension(){
     	return $this->extension;
     }
     
     public function setExtension($ex){
     	$this->extension = $ex;
     }
     
     public function setName($name){
     	$this->name = $name;
     }
     
     public function getId(){
     	return $this->id;
     }
     
    
}
