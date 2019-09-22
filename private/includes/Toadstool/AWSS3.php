<?php
  namespace Toadstool;

  require_once __DIR__ . '/../aws-autoloader.php';

  class AWSS3 extends \Toadstool\Storage
  {

    // Create the credentials object (once only)
    public function loadCredentials()
    {
      if($this->_credentials === null)
      {
        $this->_credentials = new \Aws\Credentials\Credentials(
          $this->_parent->config['storage']['awss3']['key'],
          $this->_parent->config['storage']['awss3']['secret']
        );

        return true;
      }

      throw new \Exception('Storage interface already has credentials. Cannot load again.');
    }

    // Create the client object (once only)
    public function loadClient()
    {
      if($this->_client === null)
      {
        $this->_client = new \Aws\S3\S3Client([
          'endpoint' => $this->_parent->config['storage']['awss3']['endpoint'],
          'version' => $this->_parent->config['storage']['awss3']['version'],
          'region' => $this->_parent->config['storage']['awss3']['region'],
          'credentials' => $this->_credentials,
        ]);

        return true;
      }

      throw new \Exception('Storage interface already has client. Cannot load again.');    
    }

    public function write($name, $filepath)
    {
      return $this->_client->putObject([
        'Bucket' => $this->_parent->config['storage']['awss3']['bucket'],
        'Key' => "{$this->_parent->config['storage']['basepath']}/$name",
        'SourceFile' => $filepath,
        'Content-Type' => 'image/jpeg',
        'ACL' => 'public-read'
      ]);
    }

  }