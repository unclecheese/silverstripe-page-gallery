<?php

use UncleCheese\PageGallery\PageGalleryBuilder;

class PageGalleryTest extends SapphireTest
{
    protected static $fixture_file = 'fixtures/fixtures.yml';

    public function testItTestsNodeAndThrowsIfNodeIsNotInstalled()
    {
        $this->setExpectedException('\RuntimeException');
        list($builderStub, $processStub) = $this->createStubs(
            [],
            ['isSuccessful']
        );
        $processStub
            ->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(false));

        $builderStub
            ->expects($this->once())
            ->method('createProcess')
            ->with('node -v');

        $builderStub->build();
    }

    public function testItProcessesTheCorrectPageTypes()
    {
        $screenshotDir = dirname(__FILE__);
        Config::inst()->update(
            'UncleCheese\PageGallery\PageGalleryBuilder',
            'exclude',
            [
                'PageGalleryTestPage_ExclusionPage'
            ]
        );
        Config::inst()->update(
            'UncleCheese\PageGallery\PageGalleryBuilder',
            'instance_map',
            [
                'PageGalleryTestPage_IDPage' => 123,
                'PageGalleryTestPage_LinkPage' => 'test-link'
            ]
        );
        Config::inst()->update(
            'UncleCheese\PageGallery\PageGalleryBuilder',
            'project_screenshot_dir',
            $screenshotDir
        );

        list($builderStub, $processStub) = $this->createStubs(
            ['createScreenshotCommand', 'getAllClasses', 'writeln', 'deleteScreenshot'],
            ['isSuccessful']
        );

        // Assume the child process always works to isolate the test
        $processStub
            ->expects($this->any())
            ->method('isSuccessful')
            ->will($this->returnValue(true));

        // Only test the fixtures
        $builderStub
            ->expects($this->any())
            ->method('getAllClasses')
            ->will($this->returnValue([
                'PageGalleryTestPage_ExclusionPage',
                'PageGalleryTestPage_IDPage',
                'PageGalleryTestPage_LinkPage'
            ]));

        // Suppress output
        $builderStub
            ->expects($this->any())
            ->method('writeln')
            ->will($this->returnValue(null));

        // Check to see what screenshot commands were generated
        $builderStub
            ->expects($this->any())
            ->method('createScreenshotCommand')
            ->with($this->logicalAnd(
                $this->logicalOr(
                    $this->isInstanceOf('PageGalleryTestPage_IDPage'),
                    $this->isInstanceOf('PageGalleryTestPage_LinkPage')
                ),
                $this->logicalNot(
                    $this->isInstanceOf('PageGalleryTest_ExclusionPage')
                )
            ));

        // Check what deletions happened
        $builderStub
            ->expects($this->any())
            ->method('deleteScreenshot')
            ->with($this->logicalOr(
                'PageGalleryTestPage_ExclusionPage',
                'VirtualPage',
                'RedirectorPage'
            ));

        $builderStub->build();
    }

    protected function createStubs($builderMethods = [], $processMethods = [])
    {
        $builderStub = $this->getMockBuilder('UncleCheese\PageGallery\PageGalleryBuilder')
            ->setMethods(array_merge(['createProcess'], $builderMethods))
            ->getMock();
        $processStub = $this->getMockBuilder('Symfony\Component\Process\Process')
            ->setMethods($processMethods)
            ->disableOriginalConstructor()
            ->getMock();

        $builderStub
            ->expects($this->any())
            ->method('createProcess')
            ->will($this->returnValue($processStub));

        return [$builderStub, $processStub];
    }
}