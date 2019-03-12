<?php

namespace Hulotte\Services;

use Intervention\Image\ImageManager;
use Psr\Http\Message\UploadedFileInterface;

/**
 * Class UploadService
 *
 * @package Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class UploadService
{
    /**
     * @var string
     */
    protected $path;
    
    /**
     * @var array
     */
    protected $formats;
    
    /**
     * UploadService constructor
     * @param null|string $path
     */
    public function __construct(?string $path = null)
    {
        if ($path) {
            $this->path = $path;
        }
    }
    
    /**
     * Add a suffix with label 'copy'
     * @param string $targetPath
     * @return string
     */
    public function addCopySuffix(string $targetPath): string
    {
        if (file_exists($targetPath)) {
            return $this->addCopySuffix($this->getPathWithSuffix($targetPath, 'copy'));
        }
        
        return $targetPath;
    }
    
    /**
     * Delete a file if exists
     * @param null|string $file
     */
    public function delete(?string $file): void
    {
        if ($file) {
            $file = $this->path . DIRECTORY_SEPARATOR . $file;
            
            if (file_exists($file)) {
                unlink($file);
            }
            
            foreach ($this->formats as $format => $_) {
                $fileWithFormat = $this->getPathWithSuffix($file, $format);
                
                if (file_exists($fileWithFormat)) {
                    unlink($fileWithFormat);
                }
            }
        }
    }
    
    /**
     * Upload a file
     * @param UploadedFileInterface $file
     * @param null|string $oldFile
     * @return string
     */
    public function upload(UploadedFileInterface $file, ?string $oldFile = null): string
    {
        $this->delete($oldFile);
        $targetPath = $this->addCopySuffix($this->path . DIRECTORY_SEPARATOR . $file->getClientFilename());
        $dirname = pathinfo($targetPath, PATHINFO_DIRNAME);
        
        if (!file_exists($dirname)) {
            mkdir($dirname, 777, true);
        }
  
        $file->moveTo($targetPath);
        $this->generateFormats($targetPath);
        
        return pathinfo($targetPath)['basename'];
    }
    
    /**
     * @param string $targetPath
     */
    private function generateFormats($targetPath): void
    {
        if ($this->formats) {
            foreach ($this->formats as $format => $size) {
                $destination = $this->getPathWithSuffix($targetPath, $format);
                $manager = new ImageManager(['driver' => 'gd']);
                [$width, $height] = $size;
                
                $manager->make($targetPath)
                    ->fit($width, $height)
                    ->save($destination);
            }
        }
    }
    
    /**
     * Get the file path with the suffix
     * @example public/image/test_thumb.jpg
     * @param string $path
     * @param string $suffix
     * @return string
     */
    private function getPathWithSuffix(string $path, string $suffix): string
    {
        $info = pathinfo($path);
        
        return $info['dirname']
            . DIRECTORY_SEPARATOR
            . $info['filename']
            . '_' . $suffix . '.'
            . $info['extension'];
    }
}
