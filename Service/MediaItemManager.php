<?php

namespace Wixet\WixetBundle\Service;

class MediaItemManager
{
        private $fileDir;
        private $maxWidth;
        private $maxHeight;
        
	public function __construct($config)
	{
           $config = $config['mediaItem'];
           $this->fileDir = $config['file_directory'];
           $this->maxWidth = $config['thumbnail_max_size'][0];
           $this->maxHeight = $config['thumbnail_max_size'][1];
           
           $this->maxPrevWidth = $config['preview_max_size'][0];
           $this->maxPrevHeight = $config['preview_max_size'][1];
           
           $this->maxPublicProfileWidth = $config['public_profile_max_size'][0];
           $this->maxPublicProfileHeight = $config['public_profile_max_size'][1];
           
           $this->maxProfile = $config['profile_max_size'];


	}
	
        
		private function printFile($filename, $mediaItem){
			if(file_exists($filename)) {
				$mime = $mediaItem->getMimeType();
				$name = trim($mediaItem->getTitle());
				if(strlen($name) > 0){
					$name .= ".".$mime->getExtension();
				}else{
					$name = $mediaItem->getId().".".$mime->getExtension();
				}
			
				header('Content-Type:'.$mime->getName());
				header('Content-Disposition: attachment; filename="'.$name.'"');
				ob_clean();
				flush();
				readfile($filename);
			}
		}
		public function printMediaItemOriginal($mediaItem){
			$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/original/".$mediaItem->getId();
			$this->printFile($filename, $mediaItem);
		}
	
        public function printMediaItem($mediaItem){
        	$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/".$mediaItem->getId();
        	$this->printFile($filename, $mediaItem);
        	
        }
        
        public function printMediaItemThumbnail($mediaItem){
        	
        	$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/thumbnail/".$mediaItem->getId();
        	$this->printFile($filename, $mediaItem);
        }
        
        public function printProfileThumbnail($mediaItem){
        	$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/profile/mini";
        	readfile($filename);
        	//$this->printFile($filename, $mediaItem);

        }
        
        public function printPublicProfileThumbnail($mediaItem){
        	$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/profile/public";
        	readfile($filename);
        	//$this->printFile($filename, $mediaItem);
        
        }
        
        public function printDefaultPublicProfileThumbnail(){
        	$filename = "../src/Wixet/UserInterfaceBundle/Resources/public/img/50x50.png";
        	readfile($filename);
        	//$this->printFile($filename, $mediaItem);
        
        }
        
        public function printDefaultProfileThumbnail(){
        	$filename = "../src/Wixet/UserInterfaceBundle/Resources/public/img/180x180.png";
        	readfile($filename);
        	//$this->printFile($filename, $mediaItem);
        
        }
        
        public function destroyProfileThumbnail(\Wixet\WixetBundle\Entity\UserProfile $profile,\Wixet\WixetBundle\Entity\MediaItem $mediaItem){
            @unlink($this->fileDir."/".$profile->getId()."/profile/mini");
            @unlink($this->fileDir."/".$profile->getId()."/profile/public");
        }
        
        public function doProfileThumbnail(\Wixet\WixetBundle\Entity\UserProfile $profile, \Wixet\WixetBundle\Entity\MediaItem $mediaItem){
	        //TODO: hacerlo para videos e imagenes
	        $fileType = $mediaItem->getMimeType()->getName();
	        $ownerId = $mediaItem->getProfile()->getId();
	        if($mediaItem->getMimeType()->getName() == "image/png")
	        $origen = imagecreatefrompng($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
	        elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
	        $origen = imagecreatefromjpeg($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
	        
	        list($width, $height) = getimagesize($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
	        
	        
	        //Crear thumb
	        $thumb = imagecreatetruecolor($this->maxProfile, $this->maxProfile);
	        
	        //Redimensionar
	        imagecopyresized($thumb, $origen, 0, 0, 0, 0, $this->maxProfile, $this->maxProfile, $width, $height);
	        
	        
	        if(!file_exists($this->fileDir."/".$profile->getId()."/profile")){
	        	if(!@mkdir($this->fileDir."/".$profile->getId()."/profile", 0775, true))
	        		throw new \Exception("Cannot create media item profile thumbnail directory");
	        }
	        	//Guardar archivo
	        if($mediaItem->getMimeType()->getName() == "image/png")
	        	imagepng($thumb,$this->fileDir."/".$profile->getId()."/profile/mini");
	        elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
	        	imagejpeg($thumb,$this->fileDir."/".$profile->getId()."/profile/mini");
        
	        //Crear lo de perfil (que es mas grande)
	        /**************************************/
	        if($width > $height){
	        	//$width rules
	        	//Calculamos el porcentaje reducido a lo ancho para reducir el mismo porcentaje a lo alto
	        	$diff = $width - $this->maxPublicProfileWidth;
	        	if($diff > 0){
	        		$percent = $diff * 100 / $width;
	        
	        		//Final width
	        		$new_width = $this->maxPublicProfileWidth;
	        
	        		//Final height
	        		$new_height =$height - $percent * $height / 100;
	        	}else{
	        		$new_width = $width;
	        		$new_height = $height;
	        	}
	        }else{
	        	//$height rules
	        	$diff = $height - $this->maxPublicProfileHeight;
	        	if($diff > 0){
	        		$percent = $diff * 100 / $height;
	        
	        
	        		$new_height = $this->maxPublicProfileHeight;
	        
	        
	        		$new_width = $width - $percent * $width / 100;
	        	}else{
	        		$new_width = $width;
	        		$new_height = $height;
	        	}
	        }
	        
	        //Crear thumb
	        $thumb = imagecreatetruecolor($new_width, $new_height);
	        
	        //Redimensionar
	        imagecopyresized($thumb, $origen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	        
	        
	        if(!file_exists($this->fileDir."/".$profile->getId()."/profile")){
	        	if(!@mkdir($this->fileDir."/".$profile->getId()."/profile", 0775, true))
	        	throw new \Exception("Cannot create media item profile thumbnail directory");
	        }
	        //Guardar archivo
	        if($mediaItem->getMimeType()->getName() == "image/png")
	        	imagepng($thumb,$this->fileDir."/".$profile->getId()."/profile/public");
	        elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
	        	imagejpeg($thumb,$this->fileDir."/".$profile->getId()."/profile/public");
	        
        
        
        }
        
        /*public function doProfileThumbnail(\Wixet\WixetBundle\Entity\UserProfile $profile, \Wixet\WixetBundle\Entity\MediaItem $mediaItem){
            //TODO: hacerlo para videos e imagenes
            $fileType = $mediaItem->getMimeType()->getName();
            $ownerId = $mediaItem->getProfile()->getId();
            if($mediaItem->getMimeType()->getName() == "image/png")
                $origen = imagecreatefrompng($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                $origen = imagecreatefromjpeg($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
            
            list($width, $height) = getimagesize($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());

            if($width > $height){
                //$width rules
                //Calculamos el porcentaje reducido a lo ancho para reducir el mismo porcentaje a lo alto
                $diff = $width - $this->maxProfile;
                if($diff > 0){
                    $percent = $diff * 100 / $width;

                    //Final width
                    $new_width = $this->maxProfile;

                    //Final height
                    $new_height =$height - $percent * $height / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }else{
                //$height rules
                $diff = $height - $this->maxProfile;
                if($diff > 0){
                    $percent = $diff * 100 / $height;

                    
                    $new_height = $this->maxProfile;

                    
                    $new_width = $width - $percent * $width / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }
            
            //Crear thumb
            $thumb = imagecreatetruecolor($new_width, $new_height);

            //Redimensionar
            imagecopyresized($thumb, $origen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                        
            if(!file_exists($this->fileDir."/".$profile->getId()."/profile")){
                if(!@mkdir($this->fileDir."/".$profile->getId()."/profile", 0775, true))
                        throw new \Exception("Cannot create media item profile thumbnail directory");
            }
            //Guardar archivo
            if($mediaItem->getMimeType()->getName() == "image/png")
                imagepng($thumb,$this->fileDir."/".$profile->getId()."/profile/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                imagejpeg($thumb,$this->fileDir."/".$profile->getId()."/profile/".$mediaItem->getId());
            

            
        }*/
        
	    public function saveFile($filePath, \Wixet\WixetBundle\Entity\MediaItem $mediaItem){
            //TODO: hacerlo para videos e imagenes
            $fileType = $mediaItem->getMimeType()->getName();
            $ownerId = $mediaItem->getProfile()->getId();
            if($mediaItem->getMimeType()->getName() == "image/png")
                $origen = imagecreatefrompng($filePath);
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                $origen = imagecreatefromjpeg($filePath);
            
            
            //El thumbnail normal
            list($width, $height) = getimagesize($filePath);

            if($width > $height){
                //$width rules
                //Calculamos el porcentaje reducido a lo ancho para reducir el mismo porcentaje a lo alto
                $diff = $width - $this->maxWidth;
                if($diff > 0){
                    $percent = $diff * 100 / $width;

                    //Final width
                    $new_width = $this->maxWidth;

                    //Final height
                    $new_height =$height - $percent * $height / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }else{
                //$height rules
                $diff = $height - $this->maxHeight;
                if($diff > 0){
                    $percent = $diff * 100 / $height;

                    
                    $new_height = $this->maxHeight;

                    
                    $new_width = $width - $percent * $width / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }
            
            //Crear thumb
            $thumb = imagecreatetruecolor($new_width, $new_height);

            //Redimensionar
            imagecopyresized($thumb, $origen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
                        
            if(!file_exists($this->fileDir."/".$ownerId."/thumbnail")){
                if(!@mkdir($this->fileDir."/".$ownerId."/thumbnail", 0775, true))
                        throw new \Exception("Cannot create media item thumbnail directory");
            }
            //Guardar archivo
            if($mediaItem->getMimeType()->getName() == "image/png")
                imagepng($thumb,$this->fileDir."/".$ownerId."/thumbnail/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                imagejpeg($thumb,$this->fileDir."/".$ownerId."/thumbnail/".$mediaItem->getId());
            
            
            imagedestroy($thumb);
            list($width, $height) = getimagesize($filePath);
/*
            if($width > $height){
                //$width rules
                //Calculamos el porcentaje reducido a lo ancho para reducir el mismo porcentaje a lo alto
                $diff = $width - $this->maxProfileWidth;
                if($diff > 0){
                    $percent = $diff * 100 / $width;

                    //Final width
                    $new_width = $this->maxProfileWidth;

                    //Final height
                    $new_height =$height - $percent * $height / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }else{
                //$height rules
                $diff = $height - $this->maxProfileHeight;
                if($diff > 0){
                    $percent = $diff * 100 / $height;

                    
                    $new_height = $this->maxProfileHeight;

                    
                    $new_width = $width - $percent * $width / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }
            
            //Crear thumb
            $thumb = imagecreatetruecolor($new_width, $new_height);

            //Redimensionar
            imagecopyresized($thumb, $origen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
            
                        
            if(!file_exists($this->fileDir."/".$ownerId."/profile")){
                if(!@mkdir($this->fileDir."/".$ownerId."/profile", 0775, true))
                        throw new \Exception("Cannot create media item profile directory");
            }
            //Guardar archivo
            if($mediaItem->getMimeType()->getName() == "image/png")
                imagepng($thumb,$this->fileDir."/".$ownerId."/profile/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                imagejpeg($thumb,$this->fileDir."/".$ownerId."/profile/".$mediaItem->getId());
            
            imagedestroy($thumb);
            */
            
            //La de perfil          
            //En principio se hace cuando se necesita
            //$this->doProfileThumbnail($mediaItem);
            
            
            //La preview de mediaitem
            
            list($width, $height) = getimagesize($filePath);

            if($width > $height){
                //$width rules
                //Calculamos el porcentaje reducido a lo ancho para reducir el mismo porcentaje a lo alto
                $diff = $width - $this->maxPrevWidth;
                if($diff > 0){
                    $percent = $diff * 100 / $width;

                    //Final width
                    $new_width = $this->maxPrevWidth;

                    //Final height
                    $new_height =$height - $percent * $height / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }else{
                //$height rules
                $diff = $height - $this->maxPrevHeight;
                if($diff > 0){
                    $percent = $diff * 100 / $height;

                    
                    $new_height = $this->maxPrevHeight;

                    
                    $new_width = $width - $percent * $width / 100;
                }else{
                    $new_width = $width;
                    $new_height = $height;
                }
            }
            
            //Crear thumb
            $thumb = imagecreatetruecolor($new_width, $new_height);

            //Redimensionar
            imagecopyresized($thumb, $origen, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

                        
            if(!file_exists($this->fileDir."/".$ownerId)){
                if(!@mkdir($this->fileDir."/".$ownerId, 0775, true))
                        throw new \Exception("Cannot create media item thumbnail directory");
            }
            //Guardar archivo
            if($mediaItem->getMimeType()->getName() == "image/png")
                imagepng($thumb,$this->fileDir."/".$ownerId."/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                imagejpeg($thumb,$this->fileDir."/".$ownerId."/".$mediaItem->getId());
            
            
            
            //el archivo original
            if(!file_exists($this->fileDir."/".$ownerId."/original")){
                if(!@mkdir($this->fileDir."/".$ownerId."/original", 0775, true))
                        throw new \Exception("Cannot create media item original file directory");
            }
            
            move_uploaded_file($filePath, $this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
            
            imagedestroy($thumb);
            imagedestroy($origen);

            
            
        }
        
	public function __toString()
	{
		return 'Wixet MediaItem Service';
	}
}
