<?php
/**
 * Plugin Name: LH Page Links To
 * Plugin URI: https://lhero.org/portfolio/lh-page-links-to/
 * Description: A simple way of linking externally via posts and pages.
 * Version: 1.02
 * Author: Peter Shaw
 * Author URI: https://shawfactor.com
 * Text Domain: lh-page-links-to
 * Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
* LH Page Links To plugin class
*/



if (!class_exists('LH_Page_links_to_plugin')) {


class LH_Page_links_to_plugin {
    
    private static $instance;

    static function return_plugin_namespace(){
    
        return 'lh_page_links_to';
    
    }
    
    static function return_plugin_text_domain(){

        return 'lh-buddypress-pushover-notifications';

    }
    
    static function plugin_name(){
    
        return __('LH Page Links To', self::return_plugin_text_domain());

    }
    
    static function curpageurl() {
	    $pageURL = 'http';

	    if ((isset($_SERVER["HTTPS"])) && ($_SERVER["HTTPS"] == "on")){
	        
		    $pageURL .= "s";
		    
        }

	    $pageURL .= "://";

	    if (($_SERVER["SERVER_PORT"] != "80") and ($_SERVER["SERVER_PORT"] != "443")){
	        
		    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];

	    } else {
	        
		    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

        }

	    return $pageURL;
    }
    
    static function get_applicable_post_types() {
    
        $post_types = array('post','page');
        
        $post_types = apply_filters( self::return_plugin_namespace().'_get_applicable_post_types', $post_types );
        
        if (!empty($post_types) && is_array($post_types)){
            
            return array_unique($post_types);
            
        } else {
            
            return false;
            
        }
        
    }

    static function is_applicable_post_type($posttype) {
    
        $posttypes = self::get_applicable_post_types();

        return in_array( $posttype , $posttypes );
    
    }

	/**
	 * Makes a relative URL into an absolute one.
	 *
	 * @param string $url The relative URL.
	 * @return string The absolute URL.
	 */
    static function rel2abs($rel, $base){
        /* return if already absolute URL */
        if (wp_parse_url($rel, PHP_URL_SCHEME) != '') return $rel;
    
        /* queries and anchors */
        if (!empty($rel[0]) && ($rel[0]=='#' || $rel[0]=='?')) return $base.$rel;
    
        /* parse base URL and convert to local variables:
           $scheme, $host, $path */
        extract(wp_parse_url($base));
    
        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);
    
        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';
    
        /* dirty absolute URL */
        $abs = "$host$path/$rel";
    
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
    
        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }


	/**
	 * Returns the link for the specified post.
	 *
	 * @param  WP_Post|int $post a post or post ID.
	 * @return mixed either a URL or false.
	 */
	public static function get_link( $post ) {
	    
		$post = get_post( $post );
		$post_id = empty( $post ) ? null : $post->ID;
		
		$url = get_post_meta($post_id, "_".self::return_plugin_namespace()."-link_url", true);
		
		if (!empty($url) && wp_http_validate_url($url)){
		    
		    return $url;
		    
		} else {
		    
		 return false;   
		    
		}
	}

	/**
	 * Gets the redirection URL.
	 *
	 * @return string|bool the redirection URL, or false.
	 */
	 
    static function get_redirect() {
        
		if ( !is_singular() || !get_the_ID() || !get_post_type() || !self::is_applicable_post_type(get_post_type())) {
		    
			return false;
			
		}
		
		$url = self::get_link(get_the_ID());

		if (!empty($url)){
		    
		    $link = self::rel2abs($url, home_url());

	        return $link;
		
		} else {
		    
		    return false;
		    
		}
		
	}
	
	static function setup_crons(){
	    
	    wp_clear_scheduled_hook(self::return_plugin_namespace().'_inital_run');
    
        if (! wp_next_scheduled( self::return_plugin_namespace().'_inital_run')) {
            
            wp_schedule_single_event(time() + wp_rand( 5, 50), self::return_plugin_namespace().'_inital_run');
            
        }

    }
    
    
    static function remove_crons(){
        
        wp_clear_scheduled_hook(self::return_plugin_namespace().'_inital_run');
    
    }


    

    public function add_meta_boxes($post_type, $post)  {
    
        if (self::is_applicable_post_type($post_type)) {
        
            add_meta_box(self::return_plugin_namespace()."-url-div", "Page links to", array($this,"render_links_to_box_content"), $post_type, "normal", "high", array());

        }
    
    }


    public function render_links_to_box_content( $post, $callback_args ){
    
        $link_url = self::get_link($post->ID);

        wp_nonce_field( self::return_plugin_namespace()."-post_edit-nonce", self::return_plugin_namespace()."-post_edit-nonce" );
        
        echo '<table class="form-table">'."\n";
        echo '<tr valign="top">'."\n";
        echo '<th scope="row"><label for="'.self::return_plugin_namespace().'-link_url">'.__('URL', self::return_plugin_text_domain() ).'</label></th>'."\n";
        echo '<td>'."\n";
        echo '<input type="url" name="'.self::return_plugin_namespace().'-link_url" id="'.self::return_plugin_namespace().'-link_url" value="'.$link_url.'"  />'."\n";
        echo '</td>'."\n";
        echo '</tr>'."\n";
        echo '</table>'."\n";
    
    }  


    public function update_post_details( $post_id, $post, $update ) {
    
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;
    

        if(!empty($_POST[self::return_plugin_namespace()."-post_edit-nonce"]) && wp_verify_nonce($_POST[self::return_plugin_namespace()."-post_edit-nonce"], self::return_plugin_namespace()."-post_edit-nonce") ){
    
            if (!empty($_POST[self::return_plugin_namespace()."-link_url"]) && wp_http_validate_url($_POST[self::return_plugin_namespace()."-link_url"])){
    
                update_post_meta($post_id, "_".self::return_plugin_namespace()."-link_url",esc_url_raw($_POST[self::return_plugin_namespace()."-link_url"]));
    
            } else {
    
                delete_post_meta($post_id, "_".self::return_plugin_namespace()."-link_url" );    
    
            }

        }

    }


    public function intercept_request(){
    
        if ( ($link = self::get_redirect()) && !post_password_required() ) {
			do_action( 'lh_page_links_to_redirect_url', get_queried_object_id(), $link );
			wp_redirect($link, 301, self::plugin_name());exit();
			
		}
    
    }


    public function remove_redirects_from_xml_sitemap( $query_args, $post_type ) {
        
        if ( !self::is_applicable_post_type($post_type) ) {
            
            return $query_args;
    
        }
        
		$args = array(
            'post_type' => $post_type,
            'meta_query' => array(
		        array(
			        'key' => '_'.self::return_plugin_namespace().'-link_url',
			        'compare' => 'EXISTS'
		        )
	        ),
        );
    
        $maybe_page_links = get_posts( $args );
        
        if (!empty($maybe_page_links[0])){
            
            if (empty( $query_args['post__not_in'] )){
			    
		        $query_args['post__not_in'] = array();
			    
		    }
          
            $query_args['post__not_in'] = array_unique(array_merge(wp_list_pluck( $maybe_page_links, 'ID' ), $query_args['post__not_in']));
   
        } 
    
		return $query_args;
    
    }

    public function remove_redirects_from_html_sitemap($exclude_ids){
    
        $args = array(
            'post_type' => self::get_applicable_post_types(),
            'meta_query' => array(
		        array(
			        'key' => '_'.self::return_plugin_namespace().'-link_url',
			        'compare' => 'EXISTS'
		        )
	        ),
        );
    
        $maybe_page_links = get_posts( $args );
    
        if (!empty($maybe_page_links[0])){
          
            return array_unique(array_merge(wp_list_pluck( $maybe_page_links, 'ID' ), $exclude_ids));

        } else {
       
            return $exclude_ids;
        
        }
    
    }
    
    public function add_post_column($columns){
    	
        $columns[self::return_plugin_namespace().'-link_url'] = __( 'Link Url', self::return_plugin_text_domain());;
        return $columns;
    
    }
    
    public function add_post_column_values( $column, $post_id ) {
        
        if ( self::return_plugin_namespace().'-link_url' == $column ) {
            
            if ($url = self::get_link($post_id)){
		
		        echo '<a href="'.$url.'"><span class="dashicons dashicons-external"></span></a>';
		        
            } else {
                
                _e('None', self::return_plugin_text_domain());
                
            }
		
        }
		
    }
    
    public function do_admin_init(){
        
        if ($post_types = self::get_applicable_post_types()){
        
            foreach ( $post_types  as $post_type ) {
    
                add_filter('manage_'.$post_type.'_posts_columns', array($this,'add_post_column'),5,1);
                add_action('manage_'.$post_type.'_posts_custom_column', array( $this, 'add_post_column_values' ), 10, 2 );
    
            }
            
        }
        
    }
    
    public function add_select( $post_type ){
        
        if (self::is_applicable_post_type($post_type)){
            
            $post_type_object = get_post_type_object( $post_type );
	        
	        echo '<label for="'.self::return_plugin_namespace().'-exists" class="screen-reader-text">'.__( 'Filter by Link Url', self::return_plugin_text_domain() ).'</label>'."\n";
	        echo '<select id="'.self::return_plugin_namespace().'-exists" name="'.self::return_plugin_namespace().'-exists">'."\n";
            echo '<option value="">'.$post_type_object->labels->all_items.'</option>'."\n";
            echo '<option value="with_link" ';
            
            if (isset($_GET[ self::return_plugin_namespace().'-exists' ] ) && ($_GET[ self::return_plugin_namespace().'-exists' ] == 'with')){
                echo 'selected="selected" ';
                
            }
            
            echo '>'.$post_type_object->labels->name.__(' with Links', self::return_plugin_text_domain()).'</option>'."\n";
            echo '<option value="without_link"';
            
            if (isset($_GET[ self::return_plugin_namespace().'-exists' ] ) && ($_GET[ self::return_plugin_namespace().'-exists' ] == 'without')){
                echo 'selected="selected" ';
                
            }
            
            echo '>'.$post_type_object->labels->name.__(' without Links', self::return_plugin_text_domain()).'</option>'."\n";
            echo '</select>'."\n";
            
        }
        
    }
    
    public function modify_query($query) {
    
        global $pagenow;
        
        if ( !empty($pagenow) && ($pagenow == 'edit.php') ){
            
            if (!empty($_GET[self::return_plugin_namespace().'-exists' ]) && (($_GET[self::return_plugin_namespace().'-exists' ] == 'with_link') or ($_GET[self::return_plugin_namespace().'-exists' ] == 'without_link'))){
                
                if ($_GET[self::return_plugin_namespace().'-exists' ] == 'with_link'){
                
                    $meta_query = array(
                        array(
                            'key'     => '_'.self::return_plugin_namespace().'-link_url',
                            'compare' => 'EXISTS',
                        ),
                    );
                    
                } else {
                    
                    $meta_query = array(
                        array(
                            'key'     => '_'.self::return_plugin_namespace().'-link_url',
                            'compare' => 'NOT EXISTS',
                        ),
                    );
                    
                }
        
                $query->set( 'meta_query', $meta_query );
                
            }
            
        }
        
        return $query;
        
    }
    
    public function hide_columns_by_default( $hidden, $screen ) {
    
        $hidden[] = self::return_plugin_namespace().'-link_url';
        return $hidden;
    
    }
    
    public function run_initial_processes(){
        
        
        
    }
    

    
    public function plugin_init(){
        
        $init_plugin =  apply_filters(self::return_plugin_namespace().'_init_plugin', true);
        
        if ($init_plugin){
            
            //load translations
            load_plugin_textdomain( self::return_plugin_text_domain(), false, basename( dirname( __FILE__ ) ) . '/languages' );
    
            //add the Links to metabox
            add_action('add_meta_boxes', array($this,'add_meta_boxes'),10,2);
    
            //handle posted values from the metabox
            add_action( 'save_post', array($this,'update_post_details'),10,3);
    
            //do the redirect where applicable
            add_action( 'template_redirect', array($this,'intercept_request'),8);
            
            //aremovce the redirects from the wordpress xml sitemap
            add_filter( 'wp_sitemaps_posts_query_args', array($this,'remove_redirects_from_xml_sitemap'), 10, 2 );
    
            //support for LH HTML Sitemaps
            add_filter('lh_html_sitemap_get_excluded_post_ids', array($this,'remove_redirects_from_html_sitemap'),10,1);
            
            //add columns showing link to
            add_action( 'admin_init', array($this,'do_admin_init'));
            
            //hide the external link column by default
            add_filter( 'default_hidden_columns', array($this,'hide_columns_by_default'), 10, 2 ); 
            
            //add link select
            add_action( 'restrict_manage_posts', array($this, 'add_select'), 10, 1 );
            
            //Modify the backend queries to enable query for posts with/without a link url
            add_action('pre_get_posts', array($this, 'modify_query'));
            
            //hook u some processes to the initail cron run
            add_action( self::return_plugin_namespace().'_inital_run', array($this,'run_initial_processes'),10,1);
            
        }
    
    }

    /**
     * Gets an instance of our plugin.
     *
     * using the singleton pattern
     */
    public static function get_instance(){
        
        if (null === self::$instance) {
            
            self::$instance = new self();
        
        }
 
        return self::$instance;
    
    }
    
    static function on_activate($network_wide) {
        
        if ( is_multisite() && $network_wide ) { 
    
            $args = array('number' => 500, 'fields' => 'ids');
            
            $sites = get_sites($args);
        
            foreach ($sites as $blog_id) {
                    
                switch_to_blog($blog_id);
                self::setup_crons();
                restore_current_blog();
                
            } 
    
        } else {
    
            self::setup_crons();
    
        }
    
    }

    static function on_deactivate($network_wide) {
        
    
        if ( is_multisite() && $network_wide ) { 
    
            $args = array('number' => 500, 'fields' => 'ids');
                
            $sites = get_sites($args);
        
            foreach ($sites as $blog_id) {
                    
                switch_to_blog($blog_id);
                self::remove_crons();
                restore_current_blog();
                
            } 
    
        } else {
    
            self::remove_crons();
    
        }
        
    }




    public function __construct() {

        //add hooks on plugins loaded   
        add_action( 'plugins_loaded', array($this,'plugin_init'));

    }


}

$lh_page_links_to_instance = LH_Page_links_to_plugin::get_instance();
register_activation_hook(__FILE__, array('LH_Page_links_to_plugin','on_activate') );
register_deactivation_hook( __FILE__, array( 'LH_Page_links_to_plugin', 'on_deactivate' ) );


}

?>