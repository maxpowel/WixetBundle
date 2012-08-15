<?php

namespace Wixet\WixetBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Timestampable;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\Table(name="event")
 */
class Event
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
     protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\ObjectType")
     * @ORM\JoinColumn(name="object_type_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $objectType;
     
     /**
     * @ORM\ManyToOne(targetEntity="Wixet\WixetBundle\Entity\UserProfile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
     protected $profile;
    
	/**
     * @ORM\Column(type="integer")
     */
     protected $objectId; 
     
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
     
     public function getId(){
     	return $this->id;
     }
     
     public function getObjectType(){
     	return $this->objectType;
     }
     
     public function getProfile(){
     	return $this->profile;
     }
     
     public function setObjectType($val){
     	$this->objectType = $val;
     }
     
     public function setObjectId($val){
     	$this->objectId = $val;
     }
     
     public function setProfile($val){
     	$this->profile = $val;
      }
     
     
      /* Managed in config.yml */
    public function postPersist(\Doctrine\ORM\Event\LifecycleEventArgs $args)
      {
      	
      	$entity = $args->getEntity();
      	$entityManager = $args->getEntityManager();
      	$className = get_class($entity);
      	
		$saveEvent = false;
      	if($className != "Wixet\WixetBundle\Entity\ObjectType" && $className != "Wixet\WixetBundle\Entity\Event"){
      	
      		
	      	$event = new \Wixet\WixetBundle\Entity\Event();
	      	
	      	
	      	if($entity instanceof \Wixet\WixetBundle\Entity\PrivateMessage){
	      		/* Private message */
	      		$saveEvent = true;
	      		$event->setProfile($entity->getProfile());
	      		$entityManager->persist($event);
	      	}elseif($entity instanceof \Wixet\WixetBundle\Entity\Newness){
	      		/* Newness */
	      		$saveEvent = true;
	      		$profile = $entity->getProfile();
	      		$author = $entity->getAuthor();
	      		if($profile->getId() != $author->getId()){
	      			$event->setProfile($profile);
	      			$entityManager->persist($event);
	      		}
	      	}
	      	
	      	if($saveEvent){
	      		$objectType = $entityManager->getRepository('Wixet\WixetBundle\Entity\ObjectType')->findOneBy(array('name' => $className));
	      		if($objectType == null){
	      			$objectType = new \Wixet\WixetBundle\Entity\ObjectType();
	      			$objectType->setName($className);
	      			$entityManager->persist($objectType);
	      			$entityManager->flush();
	      		}
	      		
	      		
	      		$event->setObjectType($objectType);
	      		$event->setObjectId($entity->getId());
	      		$entityManager->flush();
	      	}
      	}
      }
     
    
}
