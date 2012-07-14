<?php

namespace Wixet\WixetBundle\Service;

use Wixet\WixetBundle\Entity\GroupPermission;
use Wixet\WixetBundle\Entity\ProfilePermission;
use Wixet\WixetBundle\Entity\ObjectType;

class PermissionManager
{
    	
	private $groupClass;
	private $profileClass;
	private $itemContainerClass;
	private $doctrine;
	private $useCache;
	private $doctrineCache;
	private $dbal;
	private $itemContainerTypeId;
	

	public function __construct($em,$config,$dbal)
	{
		$this->dbal = $dbal;
		$this->doctrine = $em;
		$this->groupClass = $config['classes']['group'];
		$this->profileClass = $config['classes']['profile'];
		$this->itemContainerClass = $config['classes']['itemContainer'];
		
		//$this->itemContainerTypeId = $this->em
		$ot = $em->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $this->itemContainerClass ));
		if($ot == null){
			$ot = new \Wixet\WixetBundle\Entity\ObjectType();
			$ot->setName($this->itemContainerClass);
			$em->persist($ot);
			$em->flush();
		}
		
		$this->itemContainerTypeId = $ot->getId();
	
		//TODO
		/*if(isset($config['cache'])){
		$this->useCache = true;
		//foreach($config['cache']['connection'] as $connection){
		//	$this->useCache->addServer($connection['host'], $connection['port']);
		//}
		$this->useCacheTime = $config['cache']['expirationTime'];
		$this->doctrineCache = new \Doctrine\Common\Cache\ApcCache();
		//$this->doctrineCache->setManageCacheIds(true);
		//
		$config = new \Doctrine\ORM\Configuration();
		$config->setQueryCacheImpl($this->doctrineCache);
		$config->setResultCacheImpl($this->doctrineCache);
		$config->setMetadataCacheImpl($this->doctrineCache);
			
		//$query->useResultCache(true);
		//$query->setResultCacheLifetime(3600);
			
	
		}else $this->useCache == null;*/
		}
		
		
	/* Insert a profile to group and rebuild the permissions for the user */
    public function addProfileToGroup($profile, $group){
    	$group->addProfile($profile);
    	$this->doctrine->flush();
    	$this->invalidateProfilePermission($profile);
    }
    
    /* Remove a profile to group and rebuild the permissions for the user */
    public function removeProfileFromGroup($profile, $group){
		$group->removeProfile($profile);
    	$this->doctrine->flush();
		
    	
    	$this->invalidateProfilePermission($profile);
    }
    
    /* Remove all permissions of a profile (when a profile removes an user from all groups for example) */
    public function unbindProfile($profileOwner, $profileVisitor){
    	$group->removeProfile($profile);
    	$this->doctrine->flush();
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ProfilePermission p WHERE p.owner = ?1 AND p.profile = ?1');
    	$query->setParameter(1,$profileOwner);
    	$query->setParameter(1,$profileVisitor);
    	$query->execute();
    
    	 
    	$this->invalidateProfilePermission($profile);
    }
    
    public function getItemContainer($item){
    	$className = $this->doctrine->getClassMetadata(get_class($item))->name;
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $className));
    		
    		
    	$query = $this->doctrine->createQuery('SELECT h FROM Wixet\WixetBundle\Entity\ItemContainerHasItems h JOIN h.itemContainer ic WHERE h.object_id = ?1 AND h.objectType= ?2');
    	$query->setParameter(1,$item->getId());
    	$query->setParameter(2,$ot);
    	$result = $query->getResult();
    	return $result==null?null:$result[0];
    
    		
    }
    
    /* Change the itemContainer of an item */
    public function setItemContainer($item, $itemContainer){
    	$ot = $this->getObjectType($item);
    	$oldItemContainer = $this->getItemContainer($item);
    	
    	$query = $this->doctrine->createQuery('SELECT h FROM Wixet\WixetBundle\Entity\ItemContainerHasItems h JOIN h.itemContainer ic WHERE h.object_id = ?1 AND h.objectType= ?2');
    	$query->setParameter(1,$item->getId());
    	$query->setParameter(2,$ot);
    	
    	
    	//$icont = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ItemContainerHasItems' )->findOneBy( array( 'objectType' => $ot->getName(), 'object_id' => $item->getId()));
    	/*if($icont == null){
    		//No item container assigned (first time). ItemContainer contests has a special entitty ItemContainerHasItems
    		$icont = new \Wixet\WixetBundle\Entity\ItemContainerHasItems();
    		$icont->setObjectType($ot);
    		$icont->setObjectId($item->getId());
    		$icont->setItemContainer($itemContainer);
    		$this->doctrine->persist($icont);
    		$this->doctrine->flush();
    	}else{
    		$icont->setItemContainer($itemContainer);
    		$this->doctrine->flush();
    		
    	}*/
    	
    	//echo "BUSCANDO POR ".$item->getId()." ".$ot->getName()."\n";
    	try{
    		$result = $query->getSingleResult();
    		$icont->setItemContainer($itemContainer);
    		$this->doctrine->flush();
    	}catch(\Doctrine\ORM\NoResultException $e){
    		$icont = new \Wixet\WixetBundle\Entity\ItemContainerHasItems();
    		$icont->setObjectType($ot);
    		$icont->setObjectId($item->getId());
    		$icont->setItemContainer($itemContainer);
    		$this->doctrine->persist($icont);
    		$this->doctrine->flush();
    	}
    	
    	
    	$this->invalidateItemPermission($item);
    	$this->invalidateItemContainerPermission($itemContainer);
    	$this->invalidateItemContainerPermission($oldItemContainer);
    	
    }
    
    /* Get object type or create it if not exitst */
    private function getObjectType($item){
    	$className = $this->doctrine->getClassMetadata(get_class($item))->name;
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $className));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName($className);
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    	return $ot;
    }
    
    /* Main function to set a permission */
    public function setPermission($entity, $object, $permission){
    	 
    	$entityClassName = $this->doctrine->getClassMetadata(get_class($entity))->name;
    	
    	if($entityClassName == $this->groupClass){
    		$this->setGroupPermission($entity, $object, $permission);
    	}elseif($entityClassName == $this->profileClass){
    		$this->setProfilePermission($entity, $object, $permission);
    	}else{
    		throw new \Exception("Only can get permissions profiles or groups");
    	}
    }
    
    /* Function to group permission */
    private function setGroupPermission($group, $object, $permission){
    	$objectClassName = $this->doctrine->getClassMetadata(get_class($object))->name;
    	if($objectClassName == $this->itemContainerClass){
    		$this->setPermissionGroupItemContainer($group, $object, $permission);
    	}else{
    		$this->setPermissionGroupItem($group, $object, $permission);
    	}
    }
    
    /* Function to profile permission */
    private function setProfilePermission($profile, $object, $permission){
    	$objectClassName = $this->doctrine->getClassMetadata(get_class($object))->name;
    	
    	if($objectClassName == $this->itemContainerClass){
    		$this->setPermissionProfileItemContainer($profile, $object, $permission);
    	}else{
    		$this->setPermissionProfileItem($profile, $object, $permission);
    	}
    }
    
    
    
    /* Create or update the permission and build final permission for profile/item */
    public function setPermissionProfileItem($profile, $item, $permission){
    	$this->createProfilePermission($profile, $item, $permission);
    	$this->invalidateProfileItemPermission($profile,$item);
		//$this->rebuildFinalPermissionsToProfileItem($profile, $item);
    }
    
    
    
    private function invalidateProfilePermission($profile){
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.profile = ?1');
    	$query->setParameter(1,$profile);
    	$query->execute();
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.profile = ?1');
    	$query->setParameter(1,$profile);
    	$query->execute();
    }
    
    
    private function invalidateItemPermission($item){
    	$ot = $this->getObjectType($item);
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.object_id = ?1 AND p.objectType = ?2');
    	$query->setParameter(1,$item->getId());
    	$query->setParameter(2,$ot);
    	$query->execute();
    	
    	$ic = $this->getItemContainer($item);
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.itemContainer = ?1');
    	$query->setParameter(1,$ic);
    	$query->execute();
    	
    }
    
    private function invalidateProfileItemPermission($profile,$item){
    	$ot = $this->getObjectType($item);
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.profile = ?1 AND p.object_id = ?2 AND p.objectType = ?3');
    	$query->setParameter(1,$profile);
    	$query->setParameter(2,$item->getId());
    	$query->setParameter(3,$ot);
    	$query->execute();
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.profile = ?1');
    	$query->setParameter(1,$profile);
    	$query->execute();
    	
    }
    
    private function invalidateProfileItemContainerPermission($profile,$itemContainer){
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.profile = ?1 AND p.itemContainer = ?2');
    	$query->setParameter(1,$profile);
    	$query->setParameter(2,$itemContainer);
    	$query->execute();
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.profile = ?1 AND p.itemContainer = ?2');
    	$query->setParameter(1,$profile);
    	$query->setParameter(2,$itemContainer);
    	$query->execute();
    }
    
    /* Rebuild final permissions to group/itemContainer */
    public function setPermissionGroupItemContainer($group, $itemContainer, $permission){
    	/* Create or update the entity */
    	$this->createGroupPermission($group, $itemContainer, $permission);
    	$this->invalidateItemContainerPermission($itemContainer);
    }
    
    private function invalidateItemContainerPermission($itemContainer){
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.itemContainer = ?1');
    	$query->setParameter(1,$itemContainer);
    	$query->execute();
    	
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.itemContainer = ?1');
    	$query->setParameter(1,$itemContainer);
    	$query->execute();
    }
    
    
    /* Rebuild final permissions to group/item */
    public function setPermissionGroupItem($group, $item, $permission){
    	/* Create or update the entity */
    	$this->createGroupPermission($group, $item, $permission);
    	$this->invalidateItemPermission($item);
    
    
    
    }
    
    
    
    /* Get if the permission should be removed */
    private function isNullPermission($permission){
    	return !$permission['readGranted'] && !$permission['readDenied'] && !$permission['writeGranted'] && !$permission['writeDenied']; 
    }
    
    /* Create or update the ProfilePermission doctrine entity */
    private function createProfilePermission($profile, $item, $permission){
    	//Check if permssion exists. Insert or update or remove
    	$className = $this->doctrine->getClassMetadata(get_class($item))->name;
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $className));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName($className);
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    
    	$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\ProfilePermission p WHERE p.object_id = ?1 AND p.objectType = ?2 AND p.profile = ?3');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->setParameter(3, $profile);
    	$profilePermission = null;
    	
    	try{
    		$profilePermission = $query->getSingleResult();
    		
    		if($this->isNullPermission($permission)){
    			$this->doctrine->remove($profilePermission);
    		}else{
	    		$profilePermission->setReadGranted($permission['readGranted']);
	    		$profilePermission->setReadDenied($permission['readDenied']);
	    		$profilePermission->setWriteGranted($permission['writeGranted']);
	    		$profilePermission->setWriteDenied($permission['writeDenied']);
    		}
    		$this->doctrine->flush();
    	}catch(\Doctrine\ORM\NoResultException $e){
    		//Permission does not exists for this item/user
    		if(!$this->isNullPermission($permission)){
    			$profilePermission = new \Wixet\WixetBundle\Entity\ProfilePermission();
	    		$profilePermission->setProfile($profile);
	    		$profilePermission->setOwner($item->getProfile());
	    		$profilePermission->setObjectType($ot);
	    		$profilePermission->setObjectId($item->getId());
	    
	    		$profilePermission->setReadGranted($permission['readGranted']);
	    		$profilePermission->setWriteGranted($permission['writeGranted']);
	    		$profilePermission->setReadDenied($permission['readDenied']);
	    		$profilePermission->setWriteDenied($permission['writeDenied']);
	    		 
	    		$profilePermission->setObjectCreationTime($item->getCreated());
	    		 
	    		$this->doctrine->persist($profilePermission);
	    		$this->doctrine->flush();
    		}
    		
    	}
    	//Remove permission if is null
    	if( !$permission['readGranted'] && !$permission['readDenied'] && !$permission['writeGranted'] && !$permission['writeDenied'] ){
    		//Null permission, remove it
    		$this->doctrine->remove($profilePermission);
    		$this->doctrine->flush();
    	} 
    	
    }
    
    /* Create or update the GroupPermission doctrine entity */
    private function createGroupPermission($group, $item, $permission){
    	//Check if permssion exists. Insert or update or remove
    	$className = $this->doctrine->getClassMetadata(get_class($item))->name;
    	 
    	$ot = $this->doctrine->getRepository( 'Wixet\WixetBundle\Entity\ObjectType' )->findOneBy( array( 'name' => $className));
    	if($ot == null){
    		$ot = new \Wixet\WixetBundle\Entity\ObjectType();
    		$ot->setName($className);
    		$this->doctrine->persist($ot);
    		$this->doctrine->flush();
    	}
    
    	$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\GroupPermission p WHERE p.object_id = ?1 AND p.objectType = ?2 AND p.group = ?3');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->setParameter(3, $group);
    	$groupPermission = null;
    	 
    	try{
    		$groupPermission = $query->getSingleResult();
    		$groupPermission->setReadGranted($permission['readGranted']);
    		$groupPermission->setReadDenied($permission['readDenied']);
    		$groupPermission->setWriteGranted($permission['writeGranted']);
    		$groupPermission->setWriteDenied($permission['writeDenied']);
    		$this->doctrine->flush();
    	}catch(\Doctrine\ORM\NoResultException $e){
    		//Permission does not exists for this item/user
    		$groupPermission = new \Wixet\WixetBundle\Entity\GroupPermission();
    		$groupPermission->setGroup($group);
    		$groupPermission->setOwner($item->getProfile());
    		$groupPermission->setObjectType($ot);
    		$groupPermission->setObjectId($item->getId());
    		 
    		$groupPermission->setReadGranted($permission['readGranted']);
    		$groupPermission->setReadDenied($permission['readDenied']);
    		$groupPermission->setWriteGranted($permission['writeGranted']);
    		$groupPermission->setWriteDenied($permission['writeDenied']);
    
    		 
    		$groupPermission->setObjectCreationTime($item->getCreated());
    		 
    		$this->doctrine->persist($groupPermission);
    		$this->doctrine->flush();
    	}
    	
    	//Remove permission if is null
    	if( !$permission['readGranted'] && !$permission['readDenied'] && !$permission['writeGranted'] && !$permission['writeDenied'] ){
    		//Null permission, remove it
    		$this->doctrine->remove($groupPermission);
    		$this->doctrine->flush();
    	}
    }
    
    /* Set permission to profile/itemContainer (which is inherited to the container contents) */
    public function setPermissionProfileItemContainer($profile, $itemContainer, $permission){
    	$this->createProfilePermission($profile, $itemContainer, $permission);
    	$this->invalidateProfileItemContainerPermission($profile, $itemContainer);
    	 
    
    
    	//$this->rebuildFinalPermissionsToProfileItemContainer($profile, $itemContainer);
    }
    
    
    
    /* Set permission to profile/itemContainer (which is inherited to the container contents) */
    public function unprotect($item){
    	$ot = $this->getObjectType($item);
    	//Profile permission
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ProfilePermission p WHERE p.object_id = ?1 AND p.objectType = ?2');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->execute();
    	//Group permission
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\GroupPermission p WHERE p.object_id = ?1 AND p.objectType = ?2');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->execute();
    	
    	//Remove all final permissions
    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\FinalPermission p WHERE p.object_id = ?1 AND p.objectType = ?2');
    	$query->setParameter(1, $item->getId());
    	$query->setParameter(2, $ot);
    	$query->execute();
    	
    	//Remove item container cache permissions
    	if($this->itemContainerClass == $ot->getName()){
	    	$query = $this->doctrine->createQuery('DELETE FROM Wixet\WixetBundle\Entity\ItemContainerCacheInfo p WHERE p.itemContainer = ?1');
	    	$query->setParameter(1,$item);
	    	$query->execute();
    	}
    
    }
  
    
    
}
