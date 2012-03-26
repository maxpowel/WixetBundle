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
        
        
	public function setPermission($object,$identity,$readGranted,$writeGranted,$readDenied,$writeDenied){  
		          
				//identity can be group or profile, album is a special object
                //get real class when doctrine uses proxy
                $identityClass = $this->doctrine->getClassMetadata(get_class($identity))->name;
                $objectClass = $this->doctrine->getClassMetadata(get_class($object))->name;
				$permission = null;
                $initPermission = false;
	
                    //Add the permission
                    if($this->groupClass == $identityClass){
						//Adding permission to a group
                        //Check if permission already exists
                        //Group permission on mediaItem
						$query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\GroupPermission p '
                                .' JOIN p.object_type t '
                                .'WHERE p.real_item_id = ?1 AND t.name = ?2');
									  

									  
                        $query->setParameter(1,$object->getId());
						$query->setParameter(2,$objectClass);
                        try{
                            $permission = $query->getSingleResult();
                        }catch(\Exception $e){
                            //Item does not have permissions
                            $permission = new GroupPermission();
                            $permission->setGroup($identity);
                            $initPermission = true;
                        }
                    }else if ($this->profileClass == $identityClass){
                            //Adding permission to a an profile
                            //Check if permission already exists
                            //Profile permission on mediaItem
                            $query = $this->doctrine->createQuery('SELECT p FROM Wixet\WixetBundle\Entity\ProfilePermission p '
                                    .' JOIN p.object_type t '
                                    .' WHERE p.real_item_id = ?1 AND t.name = ?2');
                            
                            $query->setParameter(1,$object->getId());
                            $query->setParameter(2,$objectClass);
                            
                            try{
                            	$permission = $query->getSingleResult();
                                }catch(\Exception $e){
                                    //Item does not have permissions
                                    $permission = new ProfilePermission();
                                    $permission->setProfile($identity);
                                    $initPermission = true;
                            }

                    }else throw new \Exception("Identity must be an instace of ".$this->groupClass." or ".$this->profileClass);
                
                    
                    $permission->setReadGranted($readGranted);
                    $permission->setReadDenied($readDenied);
                    $permission->setWriteGranted($writeGranted);
                    $permission->setWriteDenied($writeDenied);

                    if($initPermission){
                    	if($this->albumClass != $objectClass && $this->profileClass != $objectClass)
                        	$permission->setAlbum($object->getAlbum());
                        
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
                    }
                    
                    $this->doctrine->flush();
                    
                    $this->doFinalPermissions($objectClass, $identityClass, $identity, $object, $permission);
                    //Now the final permissions
                    
	}
        
        
	private function doFinalPermissions($objectClass,$identityClass, $identity, $object, $permission){
		
						$objectType = $permission->getObjectType();
						if($this->albumClass == $objectClass){
                        //Add a permission to every item in the Album
                        if($this->profileClass == $identityClass){
                            $album = $object;
                            //Remove old final permission
                            $sql = "delete from final_permission where profile_id = ".$identity->getId()." and album_id = ".$album->getId()." and group_id is null";
                            $this->dbal->query($sql);
                            //Only to one Profile
                            
                            $sql="insert into final_permission (profile_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time)
                                       select profile_id, ".$album->getId().", real_item_id, object_type_id, ".($permission->getReadGranted()?1:0).",".($permission->getReadDenied()?1:0).",".($permission->getWriteGranted()?1:0).",".($permission->getWriteDenied()?1:0).", object_creation_time
                                       from profile_permission gp
                                       where profile_id = ".$identity->getId()." and album_id = ".$album->getId();
                            $this->dbal->query($sql);
                            //Insert the permission for the album
                            /*$sql = "delete from final_permission where real_item_id = ".$album->getId()." and album_id is null";
                            $this->dbal->query($sql);
                            
                            $sql="insert into final_permission (profile_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time)
                                       select profile_id, null, real_item_id, object_type_id, ".($permission->getReadGranted()?1:0).",".($permission->getReadDenied()?1:0).",".($permission->getWriteGranted()?1:0).",".($permission->getWriteDenied()?1:0).", object_creation_time
                                       from profile_permission gp
                                       where profile_id = ".$identity->getId()." and album_id = ".$album->getId();
                            $this->dbal->query($sql);*/
                                        
                           	//Persmission as normal object
                           	//$this->setPermission($object,$identity,$permission->getReadGranted(),readGranted,$writeGranted,$readDenied,$writeDenied, false);
                           	//$this->setPermission($object,$identity,$permission->getReadGranted(),$permission->getWriteGranted(),$permission->getReadDenied(),$permission->getReadGranted(), false);
                           	//$object,$identity,$readGranted,$writeGranted,$readDenied,$writeDenied,$albumIsSpecial = true
                            //
                        }else{
                            
                          $album = $object;
                          //Remove old final permission
                            $sql = "delete from final_permission where group_id = ".$identity->getId()." and album_id = ".$album->getId();
                            $this->dbal->query($sql);
                            
                          //To every profile in the group
                          $sql="insert into final_permission (profile_id, group_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time)
                                       select p.id, group_id, ".$album->getId().", real_item_id, object_type_id, ".($permission->getReadGranted()?1:0).",".($permission->getReadDenied()?1:0).",".($permission->getWriteGranted()?1:0).",".($permission->getWriteDenied()?1:0).", object_creation_time
                                       from group_permission gp
                                       join profilegroup_userprofile r on (gp.group_id = r.profilegroup_id)
                                       join user_profile p on (r.userprofile_id = p.id)  
                                       where group_id = ".$identity->getId()." and album_id = ".$album->getId();
                          $this->dbal->query($sql);
                        }
					}
                    //}else{
                    	
                        //Normal object (also is added the permission for an album as normal object)
                        if($this->profileClass == $identityClass){
                            //Remove old final permission
                            $sql = "delete from final_permission where profile_id = ".$identity->getId()." and real_item_id = ".$object->getId()." and object_type_id = ".$objectType->getId()." and group_id is null and album_id =".$object->getAlbum()->getId();
                            $this->dbal->query($sql);
                            
                        	    
                           /* if($this->albumClass == $objectClass || $this->profileClass == $objectClass)
                            	$albumId = "null";                            	
                            else 
                            	$albumId = $object->getAlbum()->getId();*/
                            //Add permission to one profile over an item
                            $sql = "insert into final_permission (profile_id, real_item_id, object_type_id, album_id, read_granted, read_denied, write_granted, write_denied, object_creation_time) ".
                                   "values ".
                                   "(".$identity->getId().",".$object->getId().",".$objectType->getId().",".$albumId.",".($permission->getReadGranted()?1:0).",".($permission->getReadDenied()?1:0).",".($permission->getWriteGranted()?1:0).",".($permission->getWriteDenied()?1:0).",STR_TO_DATE('".$object->getCreated()->format("Y-m-d")."','%Y-%m-%d'))";
                            $this->dbal->query($sql);
                        }else{
                        	//Is group
                            //Remove old final permission
                            //$sql = "delete from final_permission where group_id = ".$identity->getId()." and real_item_id = ".$object->getId()." and object_type_id = ".$objectType->getId()." and album_id is null";
                        	$sql = "delete from final_permission where group_id = ".$identity->getId()." and real_item_id = ".$object->getId()." and object_type_id = ".$objectType->getId()." and album_id =".$object->getAlbum()->getId();;
                            $this->dbal->query($sql);
                            
                            /*if($this->albumClass == $objectClass || $this->profileClass == $objectClass)
                            	$albumId = "null";
                            else
                            	$albumId = $object->getAlbum()->getId();*/
                            //Add permission a group over an item
                            	$sql="insert into final_permission (profile_id,  group_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time)
                                                            select p.id, group_id, album_id, real_item_id, object_type_id, read_granted, read_denied, write_granted, write_denied, object_creation_time
                                                            from group_permission gp
                                                            join profilegroup_userprofile r on (gp.group_id = r.profilegroup_id)
                                                            join user_profile p on (r.userprofile_id = p.id)  
                                                            where group_id = ".$identity->getId();
                            $this->dbal->query($sql);
                        }
                    //}
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
        
	public function getPermissions($object,$user){
		$finalPermission = array();
		if($object->getUser()->getId() == $user->getId()){
			//$user is the owner, always granted
			$finalPermission['writeGranted'] = 1;
			$finalPermission['readGranted'] = 1;
			$finalPermission['readDenied'] = 0;
			$finalPermission['writeDenied'] = 0;
		}else{
                    
                    $sql="select sum(read_granted) as read_granted, sum(read_denied) as read_denied, sum(write_granted) as write_granted, sum(write_denied) as write_denied
                          FROM final_permission
                          where user_id = ".$user->getId()." AND ream_item_id = ".$object->getId()." AND object_type_id = ".$object->getObjectType()->getId();
                    
                    $stmt = $this->dbal->query($sql);
                    $row = $stmt->fetch();
                    
                    $finalPermission['writeGranted'] = $row['write_granted'];
		    $finalPermission['readGranted'] = $row['read_granted'];
		    $finalPermission['readDenied'] = $row['read_denied'];
		    $finalPermission['writeDenied'] = $row['write_denied'];
                }
                return $finalPermission;
            
        }
}
