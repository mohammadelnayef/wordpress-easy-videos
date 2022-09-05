<?php
namespace Educational\Plugins;

class Product{

    public function register(): void {

        add_action('init', [$this, 'registerCustomPostType']);
        add_action('add_meta_boxes', [$this, 'registerCustomFields']);
        add_action( 'save_post', [$this, 'savePostActions'], 10, 2 );
    }

    public function registerCustomPostType(): void {

        $options = [
                'supports' => array('title'),
                'rewrite' => array('slug' => 'products'),
                'public' => true,
                'has_archive' => false,
                'menu_icon' => 'dashicons-cart',
                'labels' => array(
                        'name' => 'Products',
                        'add_new_item' => 'Add Product',
                        'edit_item' => 'Edit Product',
                        'all_items' => 'All Products',
                        'singular_name' => 'Product'
                        )       
        ];

        register_post_type('products', $options);
    }

    public function registerCustomFields(): void {

        add_meta_box(
            'productCustomFields',
            'Product Description',
            [$this, "ProductDescriptionField"],
            'products',
            'normal',
            'low'
        );
    }

    public function ProductDescriptionField(): void { 
       global $post;
       wp_nonce_field(basename( __FILE__ ), 'product_description_nonce');
       ?>
       <input class="widefat" type="text" name="productDescription" id="product" value="<?php echo get_post_meta($post->ID, 'product', true); ?>" size="30" />
       <?php
    }

    public function savePostActions($postId, $post) {

        /* Verify the nonce before proceeding. */
        if (!isset($_POST['product_description_nonce']) || !wp_verify_nonce($_POST['product_description_nonce'], basename(__FILE__))){
            return $postId;
        }
      
        /* Get the post type object. */
        $postType = get_post_type_object( $post->post_type );
      
        /* Check if the current user has permission to edit the post. */
        if (!current_user_can($postType->cap->edit_post, $postId)){
            return $postId;
        }
      
        /* Get the posted data and sanitize it for use as an HTML class. */
        $newMetaValue = (isset( $_POST['productDescription']) ? $_POST['productDescription'] : '' );
      
        /* Get the meta key. */
        $metaKey = 'product';
      
        /* Get the meta value of the custom field key. */
        $metaValue = get_post_meta( $postId, $metaKey, true );

        /* If a new meta value was added and there was no previous value, add it. */
        if ( $newMetaValue && '' == $metaValue ){
             add_post_meta( $postId, $metaKey, $newMetaValue, true );
        }
      
        /* If the new meta value does not match the old value, update it. */
        elseif ( $newMetaValue && $newMetaValue != $metaValue ){
            update_post_meta( $postId, $metaKey, $newMetaValue );
        }
      
        /* If there is no new meta value but an old value exists, delete it. */
        elseif ( '' == $newMetaValue && $metaValue ){
            delete_post_meta( $postId, $metaKey, $metaValue );
        }

    }

}
