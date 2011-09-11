<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
///////////LO DE CUENTAS DE GRUPO//////////
/**
 * @ORM\Entity
 * @ORM\Table(name="group_profile_type")
 */
class GroupProfileType
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
    
}
