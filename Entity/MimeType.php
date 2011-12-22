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
     
     public function getName(){
     	return $this->name;
     }
     
     public function getId(){
     	return $this->id;
     }
    
}
