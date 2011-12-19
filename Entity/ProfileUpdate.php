<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile_update")
 */
class ProfileUpdate implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="newness")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="author_id", referencedColumnName="id", nullable=false)
     */
    private $author;
    
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
    
    
}
