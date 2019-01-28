<?php
  namespace Toadstool;

  class Toadstool extends \Toadstool\Base
  {
    const IMAGEWIDTHBIG = 2000;
    const IMAGEWIDTHPREVIEW = 240;

    protected $_paths;
    protected $_watermarkObject;
    protected $_photoObjects = [];
    protected $_index = [];

    public function __construct($basePath)
    {
      // TODO: force validity of all of these
      // TODO: set some of these up if possible!
      $this->_paths = [
        'base' => $basePath,
        'includes' => "{$basePath}/private/includes",
        'uploads' => "{$basePath}/private/uploads",
        'archiveImages' => "{$basePath}/private/archive",
        'bigImages' => "{$basePath}/public/images/big",
        'previewImages' => "{$basePath}/public/images/preview",
        'watermark' => "{$basePath}/private/watermark.png",
      ];
    }


    // Get the base path of this application
    public function getBasePath()
    {
      return $this->_paths['base'];
    }


    // Get the path of the PHP includes folder
    public function getIncludesPath()
    {
      return $this->_paths['includes'];
    }


    // Get the path of the uploaded images folder (photos for processing)
    public function getUploadsPath()
    {
      return $this->_paths['uploads'];
    }
  

    // Get the path of the archived images folder (photos that have already been processed)
    public function getArchiveImagesPath()
    {
      return $this->_paths['archiveImages'];
    }


    // Get the path of the big images folder (nobody needs the actual full size images plus we watermark these)
    public function getBigImagesPath()
    {
      return $this->_paths['bigImages'];
    }


    // Get the path of the preview images folder (thubmnails make the gallery load faster)
    public function getPreviewImagesPath()
    {
      return $this->_paths['previewImages'];
    }


    // Get the path of the watermark image that will be applied to uploaded photos
    public function getWatermarkPath()
    {
      return $this->_paths['watermark'];
    }


    // Get or load an ImageMagick object for the watermark image
    public function getWatermarkObject()
    {
      if($this->_watermarkObject !== null)
        return $this->_watermarkObject;
      
      $this->_watermarkObject = new \Imagick($this->watermarkPath);
      return $this->_watermarkObject;
    }


    // Scandir a folder and return lists of the goodies within
    protected function processDirectory($path)
    {
      $results = [];

      foreach(scandir($path) as $index => $currentObjectName)
      {
        // Skip pseudo directories and hidden files (., .., and .gitignore)
        if(substr($currentObjectName, 0, 1) === '.')
          continue;

        // Check that the file actually exists within our current folder
        if(($currentObjectPath = realpath("{$path}/{$currentObjectName}")) === false)
          throw new \Exception('Object appears to not actually be accessible within processed folder. This could be a permissions issue or a security issue. Note that we never allow symlinks.');


        if(is_file($currentObjectPath))
        {
          // Test it for basic jpegness
          if(mime_content_type($currentObjectPath) !== 'image/jpeg')
            throw new \Exception('Uploaded file appears not to be a jpeg.');

          $results['files'][$currentObjectName] = $currentObjectPath;
        }
        
        elseif(is_dir($currentObjectPath))
        {
          $results['directories'][$currentObjectName] = $currentObjectPath;
        }
      
        else
        {
          throw new \Exception('Found object in uploads folder that was neither a file nor directory?');
        }
      }

      return $results;
    }


    // Iterate through the uploads folder and do the initial handling of all the ones we find
    public function processUploads()
    {
      // Function to turn a single jpeg file into a Photo object
      $processSingleUpload = function($fullPath, $name, $category = \Toadstool\Photo::UNCATEGORIZED)
      {
        // Each image only gets two seconds to finish processing, but does extend the max execution time by that much
        set_time_limit(2);

        // Load the uploaded image into a Photo object, create a thumbnail, and archive it
        $currentUpload = \Toadstool\Photo::createFromUploadImagePath($this, $fullPath, $category);
        $currentUpload->createPreviewImage();
        $currentUpload->archive();
      };

      // Locate all valid objects inside the main uploads directory
      $mainObjects = $this->processDirectory($this->uploadsPath);

      foreach($mainObjects['files'] as $currentMainObjectName => $currentMainObjectPath)
      {
        $processSingleUpload($currentMainObjectPath, $currentMainObjectName);
      }

      foreach($mainObjects['directories'] as $currentMainObjectName => $currentMainObjectPath)
      {
          $categoryObjects = $this->processDirectory($currentMainObjectPath);

          foreach($categoryObjects['files'] as $currentCategoryObjectName => $currentCategoryObjectPath)
          {
            $processSingleUpload($currentCategoryObjectPath, $currentCategoryObjectName, $currentMainObjectName);
          }
      }
    }


    // Iterate through the preview folder and load all our photo objects
    public function processImages()
    {
      $currentCategory = '';
      $previewImageObjects = $this->processDirectory($this->previewImagesPath);

      foreach($previewImageObjects['files'] as $currentImageName => $currentImagePath)
      {
        $currentPhoto = \Toadstool\Photo::createFromPreviewImagePath($this, $currentImagePath);
        $this->_photoObjects[$currentPhoto->name] = $currentPhoto;

        $this->_index['categories'][$currentPhoto->category][] = $currentPhoto->name;

        if($currentPhoto->category !== $currentCategory)
        {
          $currentCategory = $currentPhoto->category;
          echo "<h1 id=\"{$currentCategory}\"><a href=\"#{$currentCategory}\">{$currentCategory}</a></h1>";
        }

        echo $currentPhoto->createDisplayBlock();
      }
    }
  }
  