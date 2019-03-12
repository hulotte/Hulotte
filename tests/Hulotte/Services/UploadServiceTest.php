<?php

namespace Tests\Hulotte\Services;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;
use Hulotte\Services\UploadService;

/**
 * Class UploadServiceTest
 *
 * @package Tests\Hulotte\Services
 * @author Sébastien CLEMENT <s.clement@lareclame31.fr>
 */
class UploadServiceTest extends TestCase
{
    private $uploadService;
    
    public function setUp()
    {
        $this->uploadService = new UploadService('uploadTests');
    }
    
    public function tearDown()
    {
        if (file_exists('uploadTests/demo.jpg')) {
            unlink('uploadTests/demo.jpg');
            rmdir('uploadTests');
        }
    }
    
    public function testUpload()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)
            ->getMock();
            
        $uploadedFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');
            
        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('uploadTests\demo.jpg'));
            
        $this->assertEquals('demo.jpg', $this->uploadService->upload($uploadedFile));
    }
    
    public function testUploadWithExistingFile()
    {
        $uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)
            ->getMock();

        $uploadedFile->expects($this->any())
            ->method('getClientFilename')
            ->willReturn('demo.jpg');

        touch('uploadTests/demo.jpg');
        $uploadedFile->expects($this->once())
            ->method('moveTo')
            ->with($this->equalTo('uploadTests\demo_copy.jpg'));

        $this->assertEquals('demo_copy.jpg', $this->uploadService->upload($uploadedFile));
    }
}
