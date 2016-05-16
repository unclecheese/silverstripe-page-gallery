<?php

namespace UncleCheese\PageGallery;

use ClassInfo;
use SiteTree;
use Director;
use Object;
use RuntimeException;
use Injector;

/**
 * Class PageGalleryTask
 */
class PageGalleryBuilder extends Object
{

    /**
     * @param $request
     */
    public function build()
    {
        if (!$this->checkNode()) {
            throw new RuntimeException('You must have node installed in your PATH to run this task. More info: https://docs.npmjs.com/getting-started/installing-node');
        }

        chdir(BASE_PATH);

        foreach ($this->getPageTypes() as $class) {
            $instance = $this->getInstanceFor($class);
            if (!$instance) {
                $this->writeln("Skipping $class. No instances found");
                continue;
            }
            $process = $this->createProcess(
                $this->createScreenshotCommand($instance)
            );

            $process->start();

            while ($process->isRunning()) {}

            if (!$process->isSuccessful()) {
                $this->writeln("[FAIL] Could not create screenshot for $class.");
            } else {
                $this->writeln("Created screenshot for $class from {$instance->Link()}");
            }
        }

        // Delete any pages that are now excluded
        foreach ($this->config()->exclude as $className) {
            $this->deleteScreenshot($className);
        }
    }

    /**
     * @return array
     */
    protected function getPageTypes()
    {
        $classes = [];
        $allClasses = $this->getAllClasses();
        array_shift($allClasses);
        $config = $this->config();

        foreach ($allClasses as $c) {
            if (!in_array($c, $config->exclude)) {
                $classes[] = $c;
            }
        }

        return $classes;
    }


    /**
     * @return array
     */
    protected function getAllClasses()
    {
        return ClassInfo::subclassesFor('SiteTree');
    }


    /**
     * @return bool
     */
    protected function checkNode()
    {
        $process = $this->createProcess('node -v');
        $process->run();

        return $process->isSuccessful();
    }


    /**
     * @param $className
     * @return string
     */
    protected function pathToScreenshot($className)
    {
        return sprintf(
            '%s/%s.png',
            $this->config()->project_screenshot_dir,
            $className
        );
    }

    /**
     * @param $className
     */
    protected function deleteScreenshot($className)
    {
        $filePath = $this->pathToScreenshot($className);
        $process = $this->createProcess("rm $filePath");
        $process->run();

        if ($process->isSuccessful()) {
            $this->writeln('Deleted legacy screenshot ' . $filePath);
        }
    }

    /**
     * @param $class
     * @return SiteTree
     */
    protected function getInstanceFor($class)
    {
        $map = $this->config()->instance_map;
        if (isset($map[$class])) {
            $identifier = $map[$class];
            if (is_numeric($identifier)) {
                return $class::get()->byID($identifier);
            }
            return SiteTree::get_by_link($identifier);
        }

        return $class::get()
                ->filter('ClassName', $class)
                ->sort('LastEdited DESC')
                ->first();
    }


    /**
     * @param SiteTree $page
     */
    protected function createScreenshotCommand(SiteTree $page)
    {
        $config = $this->config();
        $cmd = sprintf(
            'node %s %s %s --screenWidth=%s --screenHeight=%s',
            PAGE_GALLERY_DIR . '/capture.js',
            $page->AbsoluteLink(),
            $this->pathToScreenshot($page->ClassName),
            $config->screen_width,
            $config->screen_height
        );

        return $cmd;
    }

    /**
     * @param $cmd
     * @return mixed
     */
    protected function createProcess($cmd)
    {
        return Injector::inst()->createWithArgs('PageGalleryProcess', [$cmd]);
    }

    /**
     * @param $text
     */
    protected function writeln($text)
    {
        if (Director::is_cli()) {
            fwrite(STDOUT, $text . PHP_EOL);
        } else {
            echo "$text<br>";
        }
    }
}