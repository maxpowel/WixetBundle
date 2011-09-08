<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="private_message",indexes={@ORM\index(name="conversation", columns={"conversation_id"})})
 */
class PrivateMessage implements Timestampable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
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
     * @ORM\Column(type="string")
     */
     protected $subject; 
     
     
    /**
     * @ORM\Column(type="integer")
     */
     protected $conversation_id; 
     
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", inversedBy="messages")
     * @ORM\JoinColumn(name="private_message_collection_id", referencedColumnName="id", nullable=true)
     */
     private $private_message_collection;
    
    
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
