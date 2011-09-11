<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @Gedmo\Loggable
 */
class User extends BaseUser implements Timestampable
{
	public function __construct()
    {
        parent::__construct();
        
        $this->profiles = new \Doctrine\Common\Collections\ArrayCollection();
        // your own logic
    }
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Wixet\WixetBundle\Entity\UserProfile", mappedBy="user")
     */
    protected $profiles;
	

    /**
     * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;

    
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
