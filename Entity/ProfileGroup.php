<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;


/**
 * @ORM\Entity
 * @ORM\Table(name="profile_group")
 */
class ProfileGroup implements Timestampable
{    public function getProfile() {
        return $this->profile;
    }

    public function setProfile($profile) {
        $this->profile = $profile;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getId() {
        return $this->id;
    }

    public function getProfiles() {
        return $this->profiles;
    }

    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="profiles")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false)
     */
     protected $profile;
    
    /**
     * @ORM\Column(type="string")
     */
     protected $name;
     
     
     /**
      * @ORM\ManyToMany(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="profile_groups")
      */
    private $profiles;

     
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
