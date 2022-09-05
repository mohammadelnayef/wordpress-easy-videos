<?php
namespace Plugin\EasyVideos;

class Utility{

    // Checks if the request has the right to acces the file, if not the script will be terminated.
    public static function hasRightsToAccessFile(): void {
        if ( ! defined( 'ABSPATH' ) ) {
            exit;
        }
    }

}