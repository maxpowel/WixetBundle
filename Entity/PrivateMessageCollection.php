<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="private_message_collection")
 */
class PrivateMessageCollection implements Timestampable
{
	public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


	/**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="private_messages_collections")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
     
    /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\PrivateMessageCollection", mappedBy="private_message_collection")
     */
	protected $messages;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    
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
