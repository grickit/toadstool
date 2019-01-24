<?php
  namespace Toadstool;

  class Toadstool extends \Toadstool\Base
  {
    const IMAGEWIDTHBIG = 2000;
    const IMAGEWIDTHPREVIEW = 240;

    protected $_paths;
    protected $_watermarkObject;

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


    // Iterate through the uploads folder and do the initial handling of all the ones we find
    public function processUploads()
    {
      // Locate all valid objects inside the main uploads directory
      $mainObjects = $this->processDirectory($this->uploadsPath);

      foreach($mainObjects as $index => $currentMainObjectName)
      {
        // Expand the full path
        $fullMainObjectPath = "{$this->uploadsPath}/{$currentMainObjectName}";

        // Files get processed
        if(is_file($fullMainObjectPath))
        {
          $this->processSingleUpload($fullMainObjectPath, $currentMainObjectName);
        }

        // We will scan directories for more files, but we explicitly only go one level deep under the main uploads folder
        elseif(is_dir($fullMainObjectPath))
        {
          // Locate all valid objects inside the category folder we just found
          $categoryObjects = $this->processDirectory("{$this->uploadsPath}/{$currentMainObjectName}");

          foreach($categoryObjects as $index => $currentCategoryObjectName)
          {
            // Expand the full path
            $fullCategoryObjectPath = "{$this->uploadsPath}/{$currentMainObjectName}/{$currentCategoryObjectName}";
            
            // Files get processed
            if(is_file($fullCategoryObjectPath))
            {
              $this->processSingleUpload($fullCategoryObjectPath, $currentCategoryObjectName, $currentMainObjectName);
            }

            // Anything else is problematic
            else
            {
              throw new \Exception('Found non-file in category folder.');
            }
          }
        }

        // Don't think this'll ever get hit, but lol
        else
        {
          throw new \Exception('Found object in uploads folder that was neither a file nor directory?');
        }
      }
    }


    // Scandir a folder and return a list of the goodies within
    protected function processDirectory($path)
    {
      $results = [];

      foreach(scandir($path) as $index => $currentObjectName)
      {
        // Skip pseudo directories and hidden files (., .., and .gitignore)
        if(substr($currentObjectName, 0, 1) === '.')
          continue;

        // Check that the file actually exists within our current folder
        if(($currentUploadPath = realpath("{$path}/{$currentObjectName}")) === false)
          throw new \Exception('Object appears to not actually be accessible within processed folder. This could be a permissions issue or a security issue. Note that we never allow symlinks.');

        $results[] = $currentObjectName;
      }

      return $results;
    }


    // Turn a single jpeg file into a Photo object
    protected function processSingleUpload($fullPath, $name, $category = \Toadstool\Photo::UNCATEGORIZED)
    {
      // Each image only gets two seconds to finish processing, but does extend the max execution time by that much
      set_time_limit(2);

      // Rebuild our path from known good values to make sure we're where we want to be
      if($category === \Toadstool\Photo::UNCATEGORIZED)
        $validPath = "{$this->uploadsPath}/{$name}";
      else
        $validPath = "{$this->uploadsPath}/{$category}/{$name}";
      
      // Check that the file actually exists within our uploaded images folder
      if(($fullPath = realpath($validPath)) === false)
        throw new \Exception('Uploaded image appears to not actually be accessible within the uploads folder. This could be a permissions issue.');

      // Test it for basic jpegness
      if(mime_content_type($fullPath) !== 'image/jpeg')
        throw new \Exception('Uploaded image appears not to be a jpeg.');

      // Load the uploaded image into a Photo object, create a thumbnail, and archive it
      $currentUpload = \Toadstool\Photo::createFromUploadImagePath($this, $fullPath, $category);
      $currentUpload->createPreviewImage();
      $currentUpload->archive();
    }


    // WIP code to build the gallery based on the preview images folder
    public function processImages()
    {
      $currentCategory = '';
      foreach(scandir($this->previewImagesPath, SCANDIR_SORT_DESCENDING) as $index => $currentImagePath)
      {
        // Skip pseudo directories and hidden files (., .., and .gitignore)
        if(substr($currentImagePath, 0, 1) === '.')
          continue;

        // Check that the file actually exists within our preview images folder
        if(($currentImagePath = realpath("{$this->previewImagesPath}/{$currentImagePath}")) === false)
          throw new \Exception('Preview image appears to not actually be accessible within the preview folder. This could be a permissions issue.');

        // Test it for basic jpegness
        if(mime_content_type($currentImagePath) !== 'image/jpeg')
          throw new \Exception('Preview image appears not to be a jpeg.');

        $currentImage = \Toadstool\Photo::createFromPreviewImagePath($this, $currentImagePath);

        if($currentImage->category !== $currentCategory)
        {
          $currentCategory = $currentImage->category;
          echo "<h1 id=\"{$currentCategory}\"><a href=\"#{$currentCategory}\">{$currentCategory}</a></h1>";
        }

        echo $currentImage->createDisplayBlock();
      }
    }
  }
  