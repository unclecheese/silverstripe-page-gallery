<?php


/**
 * Class PageGalleryTask
 */
class PageGalleryTask extends BuildTask
{
    /**
     * @param $request
     */
    public function run($request)
    {
        $builder = Injector::inst()->create('UncleCheese\PageGallery\PageGalleryBuilder');
        $builder->build();
    }
}