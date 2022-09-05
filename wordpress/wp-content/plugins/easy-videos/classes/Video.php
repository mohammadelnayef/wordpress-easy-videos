<?php
namespace Plugin\EasyVideos;
require_once 'YoutubeAPI.php';

class Video{

    // Registers the Easy Video custom post type and the required meta fields.
    public function register(): void {
        add_action('init', [$this, 'registerCustomPostType']);
        add_action('add_meta_boxes', [$this, 'registerCustomFields']);
    }

    // Registers the REST api endpoint that will be accessed via the "Insert Video" button.
    public function initRestApiEndpoint() {
        add_action('rest_api_init', function(){
            register_rest_route('easyVideo/v1','insert-video', [
                'methods'=>'GET',
                'callback'=> array($this, 'saveVideo'),
                'permission_callback' => '__return_true'
            ]);
        });
    }

    public function registerCustomPostType(): void {
        $options = [
                'supports' => array('title'),
                'rewrite' => array('slug' => 'easy-videos'),
                'public' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-video-alt3',
                'labels' => array(
                        'name' => 'Easy Videos',
                        'add_new_item' => 'Add Video',
                        'edit_item' => 'Edit Video',
                        'all_items' => 'All Videos',
                        'singular_name' => 'Video'
                        )       
        ];

        register_post_type('easy-videos', $options);
    }

    public function registerCustomFields(): void {

        // Video description meta field
        add_meta_box(
            'videoDescriptionMetaField',
            'Video Description',
            [$this, "addVideoDescriptionField"],
            'easy-videos',
            'normal',
            'low'
        );

        // Video thumbnail url meta field
        add_meta_box(
            'videoThumbnailURLMetaField',
            'Thumbnail URL',
            [$this, "addVideoThumbnailURLField"],
            'easy-videos',
            'normal',
            'low'
        );

        // Video publish time meta field
        add_meta_box(
            'videoPublishTimeMetaField',
            'Publish Time',
            [$this, "addVideoPublishTimeField"],
            'easy-videos',
            'normal',
            'low'
        );

        // Video url meta field
        add_meta_box(
            'videoURLMetaField',
            'Video URL',
            [$this, "addVideoURLField"],
            'easy-videos',
            'normal',
            'low'
        );

         // Video youtube id meta field
         add_meta_box(
            'youtubeIDMetaField',
            'Youtube ID',
            [$this, "addYoutubeIDField"],
            'easy-videos',
            'normal',
            'low'
        );
 
    }

    public function addVideoDescriptionField(){ 
        global $post;
        wp_nonce_field(basename( __FILE__ ), 'video_description_nonce');
        ?>
        <input class="widefat" type="text" name="videoDescription" id="videoDescription" value="<?php echo get_post_meta($post->ID, 'videoDescriptionMetaField', true); ?>" size="30" disabled />
        <?php
    }

    public function addVideoThumbnailURLField(){
        global $post;
        wp_nonce_field(basename( __FILE__ ), 'video_thumbnail_nonce');
        ?>
        <input class="widefat" type="text" name="videoThumbnail" id="videoThumbnail" value="<?php echo get_post_meta($post->ID, 'videoThumbnailURLMetaField', true); ?>" size="30" disabled />
        <?php
    }

    public function addVideoPublishTimeField(){ 
        global $post;
        wp_nonce_field(basename( __FILE__ ), 'video_publish_nonce');
        ?>
        <input class="widefat" type="text" name="videoPublishTime" id="videoPublishTime" value="<?php echo get_post_meta($post->ID, 'videoPublishTimeMetaField', true); ?>" size="30" disabled />
        <?php
    }

    public function addVideoURLField(){ 
        global $post;
        wp_nonce_field(basename( __FILE__ ), 'video_publish_nonce');
        ?>
        <input class="widefat" type="text" name="videoURL" id="videoURL" value="<?php echo get_post_meta($post->ID, 'videoURLMetaField', true); ?>" size="30" disabled />
        <?php
    }

    public function addYoutubeIDField(){ 
        global $post;
        wp_nonce_field(basename( __FILE__ ), 'video_publish_nonce');
        ?>
        <input class="widefat" type="text" name="videoYoutubeID" id="videoYoutubeID" value="<?php echo get_post_meta($post->ID, 'youtubeIDMetaField', true); ?>" size="30"  disabled />
        <?php
    }

    // Checks if the video already exists in the the DB via the youtube video id.
    private function videoExistsInDB(string $videoId): bool {
        global $wpdb;
        $result = $wpdb->get_results( "select * from $wpdb->postmeta where meta_value = '". $videoId ."' " );
        if($result){
            return true;
        }
        else{
            return false;
        }
    }

    // Creates a YoutubeAPI instance and fetcheds the data from the api, if the video already exists it fetch again. 
    // With the retrieved data it will create a new post in the easy-videos category and it will redirect to the admin page.
    public function saveVideo(){
        $youtubeAPI = new YoutubeAPI();
        $data = $youtubeAPI->fetchVideosData();
        while($this->videoExistsInDB($data["videoID"])){
            $data = $youtubeAPI->fetchVideosData($youtubeAPI->getNextPageToken());
        }
        wp_insert_post(array (
            'post_type' => 'easy-videos',
            'post_title' => $data['videoTitle'],
            'post_content' => 'your_content',
            'post_status' => 'publish',
            'meta_input' => array(
                'videoDescriptionMetaField' => $data['videoDescription'],
                'videoThumbnailURLMetaField' => $data['videoThumbnailURL'],
                'videoPublishTimeMetaField' => $data['videoPublishDate'],
                'videoURLMetaField' => $data['videoURL'],
                'youtubeIDMetaField' => $data['videoID']
              ),
         ));
        wp_redirect( get_site_url(). '/wp-admin/edit.php?post_type=easy-videos');
        die;      
    }

}
