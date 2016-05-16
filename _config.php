<?php

define('PAGE_GALLERY_DIR','silverstripe-page-gallery');
if(basename(__DIR__) !== PAGE_GALLERY_DIR) {
    throw new RuntimeException('The silverstripe-page-gallery module must be installed in a directory named "silverstripe-page-gallery"');
}