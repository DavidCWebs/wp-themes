<?php
/**
 * David\'s Blog functions and definitions
 *
 * @package David\'s Blog
 */


add_action('after_setup_theme','carawebs_bw_size');
function carawebs_bw_size() {
	
	if ( function_exists( 'add_image_size' ) ) {
	//add_image_size('carawebs_half_frontpage', 460, 260, true);
	//add_image_size('carawebs-bw-image2', 150, 180, true);
	add_image_size('carawebs-bw-image', 140, 160, true);
	add_image_size('carawebs_headshot', 140, 160, true);
	add_image_size('carawebs_person', 300, 380, true);
	add_image_size('carawebs_frontpage_thumbnail', 600, 440, true); // Add thumbnails for frontpage to allow re-sizing
	}
}


add_filter('wp_generate_attachment_metadata','gholumns_grayscale_filter');

function gholumns_grayscale_filter($meta)
{
$file = $meta['sizes']['carawebs-bw-image']['file'];
$meta['sizes']['carawebs-bw-image']['file'] = do_grayscale_filter($file);

return $meta;
}


function do_grayscale_filter($file)
{
$dir = wp_upload_dir();
$image = wp_load_image(trailingslashit($dir['path']).$file);
//$image = wp_load_image(trailingslashit($dir['path']).($dir['subdir']).$file);
imagefilter($image, IMG_FILTER_GRAYSCALE);
return save_modified_image($image, $file, '-grayscale');
}

function save_modified_image($image, $filename, $suffix)
{
$dir = wp_upload_dir();
$dest = trailingslashit($dir['path']).$filename;
//$dest = trailingslashit($dir['path']).($dir['subdir']).$filename;

list($orig_w, $orig_h, $orig_type) = @getimagesize($dest);

$filename = str_ireplace(array('.jpg', '.jpeg', '.gif', '.png'), array($suffix.'.jpg', $suffix.'.jpeg', $suffix.'.gif', $suffix.'.png'), $filename);
$dest = trailingslashit($dir['path']).$filename;
//$dest = trailingslashit($dir['path']).($dir['subdir']).$filename;

switch ($orig_type)
{
    case IMAGETYPE_GIF:
        imagegif( $image, $dest );
        break;
    case IMAGETYPE_PNG:
        imagepng( $image, $dest );
        break;
    case IMAGETYPE_JPEG:
        imagejpeg( $image, $dest );
        break;
}

return $filename;
}

function carawebs_add_bw_image () {
	
	if(function_exists('has_post_thumbnail') && has_post_thumbnail()) { 
	echo '<a href="'.get_permalink().'" class="fade-image">';
	the_post_thumbnail('carawebs-bw-image', array('class'=>'fade-image-a'));
	the_post_thumbnail('carawebs_headshot', array('class'=>'fade-image-b'));
	echo '</a>';
	}

}


add_action ('hook_after_bw_thumb', 'carawebs_add_bw_image');

/*====================================================================*/


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

if ( ! function_exists( 'davids_blog_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function davids_blog_setup() {

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on David\'s Blog, use a find and replace
	 * to change 'davids-blog' to the name of your theme in all the template files
	 */
	load_theme_textdomain( 'davids-blog', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	//add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'davids-blog' ),
	) );

	// Enable support for Post Formats.
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	// Setup the WordPress core custom background feature.
	add_theme_support( 'custom-background', apply_filters( 'davids_blog_custom_background_args', array(
		'default-color' => 'ffffff',
		'default-image' => '',
	) ) );
}
endif; // davids_blog_setup
add_action( 'after_setup_theme', 'davids_blog_setup' );

/**
 * Register widgetized area and update sidebar with default widgets.
 */
function davids_blog_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'davids-blog' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h1 class="widget-title">',
		'after_title'   => '</h1>',
	) );
}
add_action( 'widgets_init', 'davids_blog_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function davids_blog_scripts() {
	wp_enqueue_style( 'davids-blog-style', get_stylesheet_uri() );

	wp_enqueue_script( 'davids-blog-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );

	wp_enqueue_script( 'davids-blog-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'davids_blog_scripts' );

/**
 * Implement the Custom Header feature.
 */
//require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';
