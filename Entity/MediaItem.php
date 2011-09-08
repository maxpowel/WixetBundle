<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="media_item")
 */
class MediaItem implements Timestampable
{
	
	public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="updates")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
    
    /**
     * @ORM\Column(type="string")
     */
     protected $description; 
     
     /**
     * @ORM\Column(type="integer")
     */
     protected $duration; 
     
     /**
     * @ORM\Column(type="integer")
     */
     protected $file_size; 
     
     /**
     * @ORM\Column(type="string")
     */
     protected $title; 
     
     /**
     * @ORM\Column(type="integer")
     */
     protected $views; 
     
     /**
     * @ORM\Column(type="boolean")
     */
     protected $disabled; 
     
     /**
     * @ORM\Column(type="boolean")
     */
     protected $public; 
     
     
     /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\MediaItemComment", mappedBy="blog")
     */
	 protected $comments;
	 
	 /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\MimeType")
     * @ORM\JoinColumn(name="mime_type_id", referencedColumnName="id", nullable=false)
     */
     protected $mime_type;
	
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
    
}
