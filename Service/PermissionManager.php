<?php

namespace Wixet\WixetBundle\Service;

use Wixet\WixetBundle\Entity\GroupPermission;
use Wixet\WixetBundle\Entity\ProfilePermission;
use Wixet\WixetBundle\Entity\ObjectType;

class PermissionManager
{
    	
    private $groupClass;
    private $profileClass;
    private $albumClass;
    private $doctrine;
    private $useCache;
    private $doctrineCache;
    private $dbal;
    
        public function __construct($em,$config,$dbal)
	{
                $this->dbal = $dbal;
		$this->doctrine = $em;
		$this->groupClass = $config['classes']['group'];
		$this->profileClass = $config['classes']['profile'];
                $this->albumClass = $config['classes']['album'];
		
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
        
        
	public function addPermission($object,$identity,$readGranted,$writeGranted,$readDenied,$writeDenied, $album){
            //TODO URGENTE hacer que sean grupos de PERFILES no de USUARIOS
            
		//identity can be group or profile, album is a special object
                //get real class when doctrine uses proxy
                $identityClass = $this->doctrine->getClassMetadata(get_class($identity))->name;
                $albumClass = $this->doctrine->getClassMetadata(get_class($object))->name;
		
	
                    //Objeto normal, albumes incluidos
                    if($this->groupClass == $identityClass){
			//Adding permission to a group
			$permission = new GroupPermission();
			$permission->setGroup($identity);
                    }else if ($this->profileClass == $identityClass){
                            //Adding permission to a an profile
                            $permission = new ProfilePermission();
                            $permission->setProfile($identity);
                    }else throw new \Exception("Identity must be an instace of ".$this->groupClass." or ".$this->profileClass);
                
                    $permission->setReadGranted($readGranted);
                    $permission->setReadDenied($readDenied);
                    $permission->setWriteGranted($writeGranted);
                    $permission->setWriteDenied($writeDenied);

                    if($album == null)
                        $permission->setAlbum($object->getProfile()->getMainAlbum());
                    else
                        $permission->setAlbum($album);

                    //if($this->useCache != null){
                            //De momento no se cachea por la complejidad
                            //Remove all cache permissions of this object
                            //$this->removeObjectPermissionCache($object);
                    //}


                    $permission->setRealItemId($object->getId());

                    $objectType = $this->doctrine->getRepository('Wixet\WixetBundle\Entity\ObjectType')->findBy(array('name' => get_class($object)));
                    if($objectType == null){
                            $objectType = new ObjectType();
                            $objectType->setName(get_class($object));
                            $this->doctrine->persist($objectType);
                    }else $objectType = $objectType[0];

                    $permission->setObjectType($objectType);
                    
                    $permission->setObjectCreationTime($object->getCreated());


                    $this->doctrine->persist($permission);
                    $this->doctrine->flush();
                    
                    //Ahora los final permission
                    //$rsm = new \Doctrine\ORM\Query\ResultSetMapping;
                    //$rsm->addEntityResult('Wixet\WixetBundle\Entity\FinalPermission', 'u');//Utilizo esta por usar una cualquiera ya que es necesario especificar uno
                    //$rsm->addFieldResult('u', 'item_id', 'itemId');
        
                    if($this->albumClass == $albumClass){
                        //Album object
                        

                    }else{
                        //Normal object
                        if($this->profileClass == $identityClass){
                            //Add permission to one profile over an item
                            $sql = "insert into final_permission (profile_id, real_item_id, object_type_id, album_id, read_granted, read_denied, write_granted, write_denied, object_creation_time) ".
                                   "values ".
                                   "(".$identity->getId().",".$object->getId().",".$objectType->getId().",".$album->getId().",".($permission->getReadGranted()?1:0).",".($permission->getReadDenied()?1:0).",".($permission->getWriteGranted()?1:0).",".($permission->getWriteDenied()?1:0).",STR_TO_DATE('".$object->getCreated()->format("Y-m-d")."','%Y-%m-%d'))";
                            $this->dbal->query($sql);
                        }else{
                            //Add permission a group over an item (if no profile, then profile. Checked before)
                            	$sql="insert into final_permission (profile_id,  group_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time)
                                                            select p.id, group_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time
                                                            from group_permission gp
                                                            join profilegroup_userprofile r on (gp.group_id = r.profilegroup_id)
                                                            join user_profile p on (r.userprofile_id = p.id)  
                                                            where group_id = ".$identity->getId();
                            $this->dbal->query($sql);
                        }
                    }
                    
                //}
                
                //return $permission;
	}
        
        public function isWritable($object,$user){
		//The owner always have permission
		$perm = $this->getPermissions($object,$user);
		return $perm['writeGranted'] > 0 && $perm['writeDenied'] == 0;
	}
	
	public function isReadable($object,$user){
		$perm = $this->getPermissions($object,$user);
		return $perm['readGranted'] > 0 && $perm['readDenied'] == 0;
	}
        
	public function removePermission($permission){}
	public function getPermissions($object,$user){
            //TODO cachear los permisos
		$finalPermission = array();
		if($object->getUser()->getId() == $user->getId()){
			//$user is the owner, always granted
			$finalPermission['writeGranted'] = 1;
			$finalPermission['readGranted'] = 1;
			$finalPermission['readDenied'] = 0;
			$finalPermission['writeDenied'] = 0;
		}else{
                    
                }
            
        }
}
