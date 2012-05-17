<?php
namespace Wixet\WixetBundle\Util;

/* DEPRECATED: use extra lazy in doctrine */
class ItemCollection
{
    private $doctrine;
    private $dbal;
    private $profileId;
    private $itemContainerId;
    private $objectTypeId;
    private $size = null;
    
    public function __construct($em,$dbal,$profile, $itemContainer, $objectType = null){
        $this->doctrine = $em;
        $this->dbal = $dbal;
        $this->profileId = $profile->getId();
        
       	$this->itemContainerId = $itemContainer->getId();

        
        //Result can be filtered by objectType
        if($objectType == null)
            $this->objectTypeId = null;
        else{
            //Get the id of the objectType
            $objectType = $this->doctrine->getRepository('Wixet\WixetBundle\Entity\ObjectType')->findBy(array('name' => $objectType));
            if(count($objectType) == 0)
                $this->objectTypeId = null;
            else
                $this->objectTypeId = $objectType[0]->getId();
        }
    }
    
    public function getSize(){
        if($this->size == null){
            $sql = "";
            if($this->objectTypeId == null){
                $sql = "select count(*) as total from (SELECT DISTINCT fp.object_id as id
                        FROM final_permission fp
                        JOIN object_type ot on (fp.object_type_id = ot.id)
                        WHERE profile_id = ".$this->profileId." AND item_container_id = ".$this->itemContainerId."
                        group by fp.object_id,ot.name,fp.profile_id
                        having sum(read_granted)>0 and sum(read_denied) = 0) tb";
            }else{
                $sql = "select count(*) as total from (SELECT DISTINCT fp.object_id as id
                        FROM final_permission fp
                        JOIN object_type ot on (fp.object_type_id = ot.id)
                        WHERE profile_id = ".$this->profileId." AND item_container_id = ".$this->itemContainerId." AND object_type_id = ".$this->objectTypeId."
                        group by fp.object_id,ot.name,fp.profile_id
                        having sum(read_granted)>0 and sum(read_denied) = 0) tb";
            }
            $stmt = $this->dbal->query($sql);
            $res = $stmt->fetch();
            $this->size = $res['total'];
        }
        
        return $this->size;
    }
    
    public function get($start = 0,$limit = 10){
        $items = array();
        //$res = $this->getRaw($start,$limit);
        //if(count($res) ==)
        $result = $this->getRaw($start,$limit);
        
        while( ($item = $result->fetch()) ){
        	//echo $item['object_type'];
        	//echo $item['id'];
            $items[] = $this->doctrine->find($item['object_type'],$item['id']);
        }
        return $items;
    }
    
    // If you only want the ID of the element, use this method 
    public function getRaw($start = 0,$limit = 10){
        
        $sql = "";
        if($this->objectTypeId == null){
            $sql = "SELECT fp.object_id as id, ot.name as object_type
                    FROM final_permission fp
                    JOIN object_type ot on (fp.object_type_id = ot.id)
                    WHERE profile_id = ".$this->profileId." AND item_container_id = ".$this->itemContainerId."
                    group by fp.object_id,ot.name,fp.profile_id
                    having sum(read_granted)>0 and sum(read_denied) = 0
                    LIMIT ".$limit." OFFSET ".$start;
        }else{
            $sql = "SELECT fp.object_id as id, ot.name as object_type
                    FROM final_permission fp
                    JOIN object_type ot on (fp.object_type_id = ot.id)
                    WHERE profile_id = ".$this->profileId." AND item_container_id = ".$this->itemContainerId." AND object_type_id = ".$this->objectTypeId."
                    group by fp.object_id,ot.name,fp.profile_id
                    having sum(read_granted)>0 and sum(read_denied) = 0
                    LIMIT ".$limit." OFFSET ".$start;
        }
        $stmt = $this->dbal->query($sql);
        return $stmt;
        //return $stmt->fetch();
    }
}
?>
