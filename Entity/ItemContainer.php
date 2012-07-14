<?php

namespace Wixet\WixetBundle\Entity;

use Gedmo\Timestampable\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="item_container")
 */
class ItemContainer implements Timestampable
{
    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
     
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile", inversedBy="itemContainers")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
     
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    /**
     * @ORM\Column(type="boolean")
     */
     protected $public = false; 
    
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
    
    public function getId() {
        return $this->id;
    }
    
    public function getProfile() {
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

    public function getPublic() {
        return $this->public;
    }

    public function setPublic($public) {
        $this->public = $public;
    }

    public function getCreated() {
        return $this->created;
    }


    public function getUpdated() {
        return $this->updated;
    }
    
}
