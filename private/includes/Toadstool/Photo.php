<?php
  namespace Toadstool;

  class Photo extends \Toadstool\Base
  {
    protected $_parent;
    protected $_originPath;
    protected $_originObject;
    protected $_name;
    protected $_category;
    protected $_date;

    const UNCATEGORIZED = 'Uncategorized';

    /*
    0:  php
    1:  overall name
    2:  year
    3:  month
    4:  day
    5:  hour
    6:  minute
    7:  second
    8:  category
    9:  processing timestamp
    10: hash
    11: size
    */
    const NAMEREGEX = '#/(([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})_([a-zA-Z0-9-]+)_([A-Z0-9]+)_([A-Z0-9]+))_(preview|big|original)\.jpeg$#';


    // Create a new Photo object if all we know is a filepath in the uploads folder
    public static function createFromUploadImagePath($parent, $path, $category = \Toadstool\Photo::UNCATEGORIZED)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->originPath = $path;
      $category = preg_replace('/[^a-zA-Z0-9 ]/', '', $category);
      $category = preg_replace('/[ ]/', '-', $category);
      $photo->category = $category;

      // Try to get the date the photo was taken
      if(($exifData = exif_read_data($path)) !== false && is_array($exifData) && isset($exifData['DateTimeOriginal']))
      {
        $timestamp = strtotime($exifData['DateTimeOriginal']);

      }
      // Otherwise just use the last modified date on the file
      else
      {
        $timestamp = filemtime($path);
      }

      $photo->date = date('Y-m-d', $timestamp);
      $timestamp = date('YmdHis', $timestamp);

      // Generate a new name for this image
      $microtime = microtime(true)*10000;
      $processing_timestamp = base_convert($microtime, 10, 36);
      $hash = md5($path);
      $photo->name = "{$timestamp}_{$photo->category}_".strtoupper("{$processing_timestamp}_{$hash}");

      return $photo;
    }


    // Create a new Photo object if all we know is a filepath in the archive images folder
    public static function createFromArchiveImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->parseFilename($path);
      $photo->originPath = $photo->archiveImagePath;
      return $photo;
    }


    // Create a new Photo object if all we know is a filepath in the preview images folder
    public static function createFromPreviewImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->parseFilename($path);
      $photo->originPath = $photo->archiveImagePath;
      return $photo;
    }


    // Create a new Photo object if all we know is a filepath in the big images folder
    public static function createFromBigImagePath($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->parseFilename($path);
      $photo->originPath = $photo->archiveImagePath;
      return $photo;
    }


    // Create a new Photo object if all we know is the name
    public static function createFromName($parent, $path)
    {
      $photo = new Photo();
      $photo->parent = $parent;
      $photo->parseFilename("/{$path}_original.jpeg");
      $photo->originPath = $photo->archiveImagePath;
      return $photo;
    }


    // Extract what information we can from a photo name (while validating it)
    public function parseFilename($filename)
    {
      if(preg_match(Photo::NAMEREGEX, $filename, $matches))
      {
        $this->name = $matches[1];
        $this->category = preg_replace('/[-]/', ' ', $matches[8]);
        $this->date = "{$matches[2]}-{$matches[3]}-{$matches[4]}";
      }
      else
        throw new \Exception("Tried parse incorrectly formatted filename.");
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


    // Set the category (once only)
    public function setCategory($category)
    {
      if($this->_category === null)
      {
        $this->_category = $category;
        return true;
      }

      throw new \Exception('Photo already has category. Cannot set again.');
    }


    // Set the date (once only)
    public function setDate($date)
    {
      if($this->_date === null)
      {
        $this->_date = $date;
        return true;
      }

      throw new \Exception('Photo already has date. Cannot set again.');
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


    // Get the category of the photo
    public function getCategory()
    {
      if($this->_category !== null)
        return $this->_category;
      
      throw new \Exception('Asked for Photo category but did not have one.');
    }


    // Get the date of the photo
    public function getDate()
    {
      if($this->_date !== null)
        return $this->_date;
      
      throw new \Exception('Asked for Photo date but did not have one.');
    }


    // Returns formatted date
    public function getFancyDate()
    {
      $datetime = \DateTime::createFromFormat('Y-m-d', $this->date);
      return $datetime->format('F jS Y');
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
      return "{$this->_parent->archiveImagesPath}/{$this->name}_original.jpeg";
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
      return Photo::testImagePath($this->previewImagePath);
    }


    // Returns whether or not large files have been previously successfully shipped to external storage
    public function testShipped()
    {
      return $this->_parent->storage->testFile("original/{$this->name}_original.jpeg");
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

      // Add watermark if we have a valid one
      if(get_class($this->_parent->watermarkObject) === 'Imagick')
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
    public function archiveUpload()
    {
      // TODO: more errors!
      rename($this->_originPath, $this->archiveImagePath);
      //chmod($this->archiveImagePath, 0644);
      $this->shipArchive();
    }


    // Ships large files off to storage to save space on disk
    public function shipArchive()
    {
      // TODO: this is only the happy path - more errors!

      // If this photo has already been shipped, we don't want to do it again
      if($this->testShipped() === true)
        return true;

      // We're about to offload the original image from the web server, so we better finish up anything we might need that for now
      if($this->testPreviewImagePath() !== true)
        $this->createPreviewImage();
      
      if($this->testBigImagePath() !== true)
        $this->createBigImage();
      
      // Ship and delete the original upload
      if($this->_parent->storage->writeFile("original/{$this->name}_original.jpeg", $this->archiveImagePath, false))
        unlink($this->archiveImagePath);

      // Ship and delete the big watermarked image
      if($this->_parent->storage->writeFile("big/{$this->name}_big.jpeg", $this->bigImagePath, true))
        unlink($this->bigImagePath);
    }
  }
  