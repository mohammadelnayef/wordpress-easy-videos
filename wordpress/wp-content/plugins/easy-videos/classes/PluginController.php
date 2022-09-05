<?php
namespace Plugin\EasyVideos;

use Plugin\EasyVideos\Video;
require_once dirname(__FILE__).'/Video.php';

class PluginController{

    // Initializes the plugin, changes the admin Add New button url, enqueues the js script and calls the video init function.
    public function init(){  
        add_filter( 'admin_url', [$this,'easyVideosChangeAdminButton'], 10, 2 );
        add_action('admin_enqueue_scripts',[$this,'insertEasyVideosJS']);
        $this->initVideo();
    }

    public function easyVideosChangeAdminButton( $url, $path ){
        if( $path === 'post-new.php?post_type=easy-videos' ) {
        $url = get_site_url(). '/wp-json/easyVideo/v1/insert-video';
        }
        return $url;
    }

    public function insertEasyVideosJS() {
        wp_enqueue_script( 'easy-vidoes-js', plugins_url( '/js/script.js', dirname(__FILE__, 1)));
    }  

    // Creates a video object, registers the custom post type, meta fields and the REST api endpoint.
    private function initVideo(){
        $videoObject = new Video();
        $videoObject->register();
        $videoObject->initRestApiEndpoint();
    }
}