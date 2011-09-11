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
    public function getCreated() {
        return $this->created;
    }

    public function getUpdated() {
        return $this->updated;
    }

        public function getId() {
        return $this->id;
    }
    
    public function getProfile() {
        return $this->profile;
    }

    public function setProfile($profile) {
        $this->profile = $profile;
    }

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDuration() {
        return $this->duration;
    }

    public function setDuration($duration) {
        $this->duration = $duration;
    }

    public function getFileSize() {
        return $this->file_size;
    }

    public function setFileSize($file_size) {
        $this->file_size = $file_size;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getViews() {
        return $this->views;
    }

    public function setViews($views) {
        $this->views = $views;
    }

    public function getDisabled() {
        return $this->disabled;
    }

    public function setDisabled($disabled) {
        $this->disabled = $disabled;
    }

    public function getPublic() {
        return $this->public;
    }

    public function setPublic($public) {
        $this->public = $public;
    }

    public function getComments() {
        return $this->comments;
    }

    public function setComments($comments) {
        $this->comments = $comments;
    }

    public function getMimeType() {
        return $this->mime_type;
    }

    public function setMimeType($mime_type) {
        $this->mime_type = $mime_type;
    }

    	
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
