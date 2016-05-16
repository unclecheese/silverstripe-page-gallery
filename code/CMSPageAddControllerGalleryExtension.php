<?php

namespace UncleCheese\PageGallery;

use FieldList;
use Director;
use Controller;
use ImageOptionsetField;
use Extension;
use Requirements;
use Config;

/**
 * Class CMSPageAddControllerGalleryExtension
 */
class CMSPageAddControllerGalleryExtension extends Extension
{

    /**
     * @param FieldList $fields
     */
    public function updatePageOptions(FieldList $fields)
    {
        Requirements::add_i18n_javascript('silverstripe-page-gallery/lang');

        $defaultImage = Config::inst()->get('PageGalleryUI','default_image');
        $title = $fields->dataFieldByName('PageType')->Title();
        $options = [];
        foreach ($this->owner->PageTypes() as $type) {
            $className = $type->getField('ClassName');
            $imagePath = Controller::join_links(
                PageGalleryBuilder::config()->project_screenshot_dir,
                $className . '.png'
            );

            $options[$type->getField('ClassName')] = [
                'title' => $type->getField('AddAction'),
                'image' => Director::fileExists($imagePath) ? $imagePath : $defaultImage
            ];

        }
        $fields->replaceField(
            'PageType',
            ImageOptionsetField::create('PageType', $title)
                ->setSource($options)
                ->setImageWidth(Config::inst()->get('PageGalleryUI', 'image_width'))
                ->setImageHeight(Config::inst()->get('PageGalleryUI', 'image_height'))
        );
    }


}