<?php

namespace Wixet\WixetBundle\Entity;

//use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
//use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="city")
 */
class City// implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\City", mappedBy="capital")
     */
     protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\City")
     * @ORM\JoinColumn(name="capital_id", referencedColumnName="id")
     */
    private $capital;
    
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="id", nullable=false)
     */
    private $country;
    
	/**
     * @ORM\Column(type="string", unique=true)
     */
     protected $name; 
     
    
}
