<?php
  namespace Toadstool;

  class Photo extends \Toadstool\Base
  {
    protected $_parent;
    protected $_originPath;
    protected $_originObject;
    protected $_name;
    protected $_category;


    // Create a new Photo object if all we know is a filepath in the uploads folder
    public static function createFromUploadImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->originPath = $path;

      // Generate a new name for this image
      $microtime = microtime(true)*10000;
      $processing_timestamp = base_convert($microtime, 10, 36);
      $modified_timestamp = date('YmdHis', filemtime($path));
      $hash = md5($path);
      $photo->name = strtoupper("{$modified_timestamp}_{$processing_timestamp}_{$hash}");

      return $photo;
    }


    // Create a new Photo object if all we know is a filepath in the preview images folder
    public static function createFromPreviewImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      
      if(preg_match("#^{$parent->previewImagesPath}\/([0-9]{14}_[A-Z0-9]+_[A-Z0-9]+)_preview\.jpeg$#", $path, $matches))
      {
        $photo->originPath = "{$parent->archiveImagesPath}/{$matches[1]}_original";
        $photo->name = $matches[1];
      }
      else
        throw new \Exception("Tried to create Photo from invalid preview image path.");

      return $photo;
    }


    // Create a new Photo object if all we know is a filepath in the big images folder
    public static function createFromBigImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      
      if(preg_match("#^{$parent->bigImagesPath}\/([0-9]{14}_[A-Z0-9]+_[A-Z0-9]+)_big\.jpeg$#", $path, $matches))
      {
        $photo->originPath = "{$parent->archiveImagesPath}/{$matches[1]}_original";
        $photo->name = $matches[1];
      }
      else
        throw new \Exception("Tried to create Photo from invalid big image path.");

      return $photo;
    }


    // Set the parent object (once only)
    public function setParent($parent)
    {
      if($this->_parent === null)
      {
        $this->_parent = $parent;
        return true;
      }

      throw new \Exception('Photo already has parent. Cannot set again.');
    }


    // Set the filepath to the original full size image (once only)
    public function setOriginPath($path)
    {
      if($this->_originPath === null)
      {
        $this->_originPath = $path;
        return true;
      }

      throw new \Exception('Photo already has origin path. Cannot set again.');
    }


    // Set the name (once only)
    public function setName($name)
    {
      if($this->_name === null)
      {
        $this->_name = $name;
        return true;
      }

      throw new \Exception('Photo already has name. Cannot set again.');
    }


    // Get the path to the original full size image
    public function getOriginPath()
    {
      if($this->_originPath !== null)
        return $this->_originPath;
      
      throw new \Exception('Asked for Photo origin path but did not have one.');
    }


    // Get or load an ImageMagick object for the original full size image
    public function getOriginObject()
    {
      if($this->_originObject !== null)
        return $this->_originObject;
      
      $this->_originObject = new \Imagick($this->_originPath);
      return $this->_originObject;
    }


    // Get the name of the photo
    public function getName()
    {
      if($this->_name !== null)
        return $this->_name;
      
      throw new \Exception('Asked for Photo name but did not have one.');
    }


    // Get the path to the big version of the image
    public function getBigImagePath()
    {
      return "{$this->_parent->bigImagesPath}/{$this->name}_big.jpeg";
    }


    // Get the path to the preview version of the image
    public function getPreviewImagePath()
    {
      return "{$this->_parent->previewImagesPath}/{$this->name}_preview.jpeg";
    }


    // Get the path to the archived full size image
    public function getArchiveImagePath()
    {
      return "{$this->_parent->archiveImagesPath}/{$this->name}_original";
    }


    // Any tests we want to do to make sure an image is valid
    public static function testImagePath($path)
    {
      if(is_readable($path) !== true)
        return false;

      if(mime_content_type($path) !== 'image/jpeg')
        return false;

      return true;
    }


    // Test the origin image path
    public function testOriginImagePath()
    {
      return Photo::testImagePath($this->originPath);
    }


    // Test the big image path
    public function testBigImagePath()
    {
      return PHoto::testImagePath($this->bigImagePath);
    }


    // Test the preview image path
    public function testPreviewImagePath()
    {
      return Photo::testPreviewPath($this->previewImagePath);
    }    


    // Create and publish a big version of our image
    public function createBigImage()
    {
      // Create a copy of the image for editing
      $bigImage = $this->originObject->clone();

      // Shrink it down
      $bigImage->scaleImage(\Toadstool\Toadstool::IMAGEWIDTHBIG,0);

      // Remove EXIF data but keep ICC profile
      $profiles = $bigImage->getImageProfiles('icc', true);
      $bigImage->stripImage();
      if(!empty($profiles))
        $bigImage->profileImage('icc', $profiles['icc']);

      // Add watermark
      // TODO: check that this is a valid ImageMagick object
      if($this->_parent->watermarkObject !== null)
        $bigImage->compositeImage($this->_parent->watermarkObject, \Imagick::COMPOSITE_DEFAULT, \Toadstool\Toadstool::IMAGEWIDTHBIG-$this->_parent->watermarkObject->getImageWidth(), $bigImage->getImageHeight()-$this->_parent->watermarkObject->getImageHeight());

      // Compress slightly
      $bigImage->setImageCompression(\Imagick::COMPRESSION_JPEG);
      $bigImage->setImageCompressionQuality(97);

      // Save
      $bigImage->writeImage($this->bigImagePath);
      chmod($this->bigImagePath, 0644);
    }


    // Create and publish a preview version of our image
    public function createPreviewImage()
    {
      // Create a copy of the image for editing
      $previewImage = $this->originObject->clone();

      // Shrink it down
      $previewImage->scaleImage(\Toadstool\Toadstool::IMAGEWIDTHPREVIEW, 0);

      // Remove EXIF data but keep ICC profile
      $profiles = $previewImage->getImageProfiles('icc', true);
      $previewImage->stripImage();
      if(!empty($profiles))
        $previewImage->profileImage('icc', $profiles['icc']);

      // Compress heavily
      $previewImage->setImageCompression(\Imagick::COMPRESSION_JPEG);
      $previewImage->setImageCompressionQuality(90);

      // Save
      $previewImage->writeImage($this->previewImagePath);
      chmod($this->previewImagePath, 0644);
    }


    // Move the original upload file to the archive folder so that it isn't picked up on future runs through the uploads folder
    public function archive()
    {
      // TODO: more errors!
      rename($this->_originPath, $this->archiveImagePath);
      chmod($this->archiveImagePath, 0644);
    }


    // WIP code to separate photos by year
    public function getCategory()
    {
      if($this->_category !== null)
        return $this->_category;

      if(preg_match('/^([0-9]{8})[0-9]{6}_[A-Z0-9]+_[A-Z0-9]+$/', $this->name, $matches))
      {
        $dateTime = \DateTime::createFromFormat('Ymd', $matches[1]);
        $this->_category = $dateTime->format('F Y');
      }
      else
      {
        return '';
      }

      return $this->_category;
    }


    // WIP code to display a thumbnail on the website
    public function createDisplayBlock()
    {
      $output = "<a href=\"images/big/{$this->name}_big.jpeg\" target=\"_blank\" rel=\"noopener noreferrer\">";
      $output .= "<img src=\"images/preview/{$this->name}_preview.jpeg\">";
      $output .= '<a/>';

      return $output;
    }
  }