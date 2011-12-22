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
           
           $this->maxProfileWidth = $config['profile_max_size'][0];
           $this->maxProfileHeight = $config['profile_max_size'][1];


	}
	
        
        public function printMediaItem($mediaItem){
            readfile($this->fileDir."/".$mediaItem->getUser()->getId()."/".$mediaItem->getId());
        }
        
        public function printMediaItemThumbnail($mediaItem){
            readfile($this->fileDir."/".$mediaItem->getUser()->getId()."/thumbnail/".$mediaItem->getId());
        }
        
        public function printProfileThumbnail($mediaItem){
        	$filename = $this->fileDir."/".$mediaItem->getProfile()->getId()."/profile/".$mediaItem->getId();
        	if(file_exists($filename)) {
        		header('Content-Type: '.$mediaItem->getMimeType()->getName());
        		ob_clean();
        		flush();
        		readfile($filename);
        	}

        }
        
        public function destoryProfileThumbnail(\Wixet\WixetBundle\Entity\MediaItem $mediaItem){
            $ownerId = $mediaItem->getUser()->getId();
            unlink($this->fileDir."/".$ownerId."/profile/".$mediaItem->getId());
        }
        public function doProfileThumbnail(\Wixet\WixetBundle\Entity\MediaItem $mediaItem){
            //No usada de momento
            //TODO: hacerlo para videos e imagenes
            $fileType = $mediaItem->getMimeType()->getName();
            $ownerId = $mediaItem->getUser()->getId();
            if($mediaItem->getMimeType()->getName() == "image/png")
                $origen = imagecreatefrompng($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                $origen = imagecreatefromjpeg($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());
            
            list($width, $height) = getimagesize($this->fileDir."/".$ownerId."/original/".$mediaItem->getId());

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
                        throw new \Exception("Cannot create media item profile thumbnail directory");
            }
            //Guardar archivo
            if($mediaItem->getMimeType()->getName() == "image/png")
                imagepng($thumb,$this->fileDir."/".$ownerId."/profile/".$mediaItem->getId());
            elseif($mediaItem->getMimeType()->getName() == "image/jpeg")
                imagejpeg($thumb,$this->fileDir."/".$ownerId."/profile/".$mediaItem->getId());
            

            
        }
        
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
            
            
            //La de perfil          
            //TODO: Hacer que sólo se cree cuando se necesita
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
