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

    public function getBasePath()
    {
      return $this->_paths['base'];
    }

    public function getIncludesPath()
    {
      return $this->_paths['includes'];
    }

    public function getUploadsPath()
    {
      return $this->_paths['uploads'];
    }
  
    public function getArchiveImagesPath()
    {
      return $this->_paths['archiveImages'];
    }

    public function getBigImagesPath()
    {
      return $this->_paths['bigImages'];
    }

    public function getPreviewImagesPath()
    {
      return $this->_paths['previewImages'];
    }

    public function getWatermarkPath()
    {
      return $this->_paths['watermark'];
    }

    public function getWatermarkObject()
    {
      if($this->_watermarkObject !== null)
        return $this->_watermarkObject;
      
      $this->_watermarkObject = new \Imagick($this->watermarkPath);
      return $this->_watermarkObject;
    }

    public function processUploads()
    {
      foreach(scandir($this->uploadsPath) as $index => $currentUploadPath)
      {
        // We're fine with this running forever, as long as it makes progress
        set_time_limit(2);

        // Skip pseudo directories
        if($currentUploadPath === '.' || $currentUploadPath === '..')
          continue;

        // Expand the path to the file
        $currentUploadPath = realpath("{$this->uploadsPath}/{$currentUploadPath}");

        // Test it for basic jpegness
        if(mime_content_type($currentUploadPath) !== 'image/jpeg')
        {
          echo "Skipping invalid file {$currentUploadPath}<br>\n";
          continue;
        }
      
        echo "Processing image {$currentUploadPath}<br>\n";
        $currentUpload = \Toadstool\Photo::createFromUploadImagePath($this, $currentUploadPath);
        $currentUpload->createPreviewImage();
        $currentUpload->archive();
      }
    }

    public function processImages()
    {
      $currentCategory = '';
      foreach(scandir($this->previewImagesPath, SCANDIR_SORT_DESCENDING) as $index => $currentImagePath)
      {
        // Skip pseudo directories
        if($currentImagePath === '.' || $currentImagePath === '..')
          continue;

        // Check that the file actually exists within our preview images folder
        if(($currentImagePath = realpath("{$this->previewImagesPath}/{$currentImagePath}")) === false)
          throw new \Exception('Preview image appears to not actually be accessible within the preview folder. This could be a permissions issue.');

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