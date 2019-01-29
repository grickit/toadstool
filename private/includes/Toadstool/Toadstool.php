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
        'archiveImages' => "{$basePath}/private/runtime/archive",
        'bigImages' => "{$basePath}/public/images/big",
        'previewImages' => "{$basePath}/public/images/preview",
        'watermark' => "{$basePath}/private/watermark.png",
        'index' => "{$basePath}/private/runtime/index.json",
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


    // Get the path of the image list cache
    public function getIndexPath()
    {
      return $this->_paths['index'];
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
      $hadNewPhoto = false;

      foreach($mainObjects['files'] as $currentMainObjectName => $currentMainObjectPath)
      {
        $processSingleUpload($currentMainObjectPath, $currentMainObjectName);
        $hadNewPhoto = true;
      }

      foreach($mainObjects['directories'] as $currentMainObjectName => $currentMainObjectPath)
      {
          $categoryObjects = $this->processDirectory($currentMainObjectPath);

          foreach($categoryObjects['files'] as $currentCategoryObjectName => $currentCategoryObjectPath)
          {
            $processSingleUpload($currentCategoryObjectPath, $currentCategoryObjectName, $currentMainObjectName);
            $hadNewPhoto = true;
          }
      }

      // Rebuild the index if we had any new photos
      if($hadNewPhoto === true && $this->buildIndex() === true)
        $this->saveIndex();
    }


    // Iterate through the preview folder and load all our photo objects
    protected function buildIndex()
    {
      $currentCategory = '';
      $previewImageObjects = $this->processDirectory($this->previewImagesPath);

      foreach($previewImageObjects['files'] as $currentImageName => $currentImagePath)
      {
        $currentPhoto = \Toadstool\Photo::createFromPreviewImagePath($this, $currentImagePath);
        $this->_photoObjects[$currentPhoto->name] = $currentPhoto;

        $this->_index['categories'][$currentPhoto->category][] = $currentPhoto->name;
        $this->_index['dates'][$currentPhoto->yearMonth][] = $currentPhoto->name;
        $this->_index['all'][] = $currentPhoto->name;

        /*
        if($currentPhoto->category !== $currentCategory)
        {
          $currentCategory = $currentPhoto->category;
          echo "<h1 id=\"{$currentCategory}\"><a href=\"#{$currentCategory}\">{$currentCategory}</a></h1>";
        }

        echo $currentPhoto->createDisplayBlock();
        */
      }

      // Sort inside the categories
      foreach($this->_index['categories'] as $index => $category)
        asort($this->_index['categories'][$index]);

      // Sort inside the year months
      foreach($this->_index['dates'] as $index => $yearmonth)
        asort($this->_index['dates'][$index]);

      // Sort the list of all images
      asort($this->_index['all']);

      return true;
    }


    // Save the image index to disk
    protected function saveIndex()
    {
      if(($json_index = json_encode($this->_index, JSON_PRETTY_PRINT)) === false)
        throw new \Exception('Failed to JSON encode image index.');
      
      if(!file_put_contents($this->indexPath, $json_index))
        throw new \Exception('Failed to save image index to disk.');

      return true;
    }


    // Load the image index from disk
    protected function loadIndex()
    {
      if(($json_index = file_get_contents($this->indexPath)) === false)
        throw new \Exception('Failed to load image index from disk.');
      
      if(($this->_index = json_decode($json_index, true)) === false)
        throw new Exception('Failed to JSON decode image index.');

      return true;
    }


    // Conditionally load or rebuild the index
    public function getIndex()
    {
      // We don't already have it loaded
      if(!count($this->_index))
      {
        // But it is ready to go on disk and not older than a day
        if(time() - filemtime($this->indexPath) < 86400)
        {
          $this->loadIndex();
        }
        // It doesn't exist on disk or is too old
        else
        {
          $this->buildIndex();
          $this->saveIndex();
        }
      }

      return $this->_index;
    }
  }
  