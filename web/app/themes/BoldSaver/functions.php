<?php
/**
 * BoldSaver WordPress Theme
 */

if (!class_exists('Timber')) {
    add_action('admin_notices', function () {
        echo '<div class="error"><p>Timber not activated. Make sure you activate the plugin in <a href="' . esc_url(admin_url('plugins.php#timber')) . '">' . esc_url(admin_url('plugins.php')) . '</a></p></div>';
    });

    add_filter('template_include', function ($template) {
        return get_stylesheet_directory() . '/static/no-timber.html';
    });

    return;
}

/**
 * Sets the directories (inside your theme) to find .twig files
 */
Timber::$dirname = array('components');

/**
 * By default, Timber does NOT autoescape values. Want to enable Twig's autoescape?
 * No prob! Just set this value to true
 */
Timber::$autoescape = false;

/**
 * We're going to configure our theme inside of a subclass of Timber\Site
 * You can move this to its own file and include here via php's include("MySite.php")
 */
class StarterSite extends Timber\Site
{
    /** Add timber support. */
    public function __construct()
    {
        add_action('after_setup_theme', array($this, 'theme_supports'));
        add_action('after_setup_theme', array($this, 'legacy_functions'));
        add_filter('timber_context', array($this, 'add_to_context'));
        add_filter('upload_mimes', array($this, 'cc_mime_types'));
        add_action('init', array($this, 'register_my_menu'));
        add_action('acf/init', array($this, 'my_acf_init'));
        add_image_size('ratio', 300, 300, true);
        add_image_size('featured_xs', 350, 175, true);
        add_image_size('featured_sm', 450, 225, true);
        add_image_size('featured_md', 768, 384, true);
        add_image_size('featured_lg', 1300, 640, true);
        add_image_size('featured_xl', 1920, 1080, true);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_filter('the_content', 'wpautop');
        remove_filter('the_excerpt', 'wpautop');
        add_filter('post_thumbnail_html', array($this, 'remove_image_size_attributes'));
        add_filter('image_send_to_editor', array($this, 'remove_image_size_attributes'));
        apply_filters('rocket_cache_reject_wp_rest_api', false);
        parent::__construct();
    }

    public function register_my_menu()
    {
        register_nav_menus(array(
            'primary' => 'Primary Menu',
            'footer_one' => 'First Footer Menu',
        ));
    }

    /** This is where you add some context
     *
     * @param string $context context['this'] Being the Twig's {{ this }}.
     */
    public function add_to_context($context)
    {
        $context['menu_primary'] = new TimberMenu('primary');
        $context['menu_footer_one'] = new TimberMenu('footer_one');

        $context['site'] = $this;
        return $context;
    }

    public function theme_supports()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page();
        }

        add_theme_support('automatic-feed-links');
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');
        add_theme_support(
            'html5', array(
                'gallery',
            )
        );
        add_theme_support(
            'post-formats', array(
                'aside',
                'image',
                'video',
                'link',
                'gallery',
            )
        );
        add_theme_support('menus');

        add_theme_support('align-wide');
        add_theme_support('disable-custom-colors');
        add_theme_support('disable-custom-font-sizes');
    }

    public function cc_mime_types($mimes)
    {
        $mimes['webp'] = 'image/webp';
        $mimes['svg'] = 'image/svg+xml';
        return $mimes;
    }

    public function remove_image_size_attributes($html)
    {
        return preg_replace('/(width|height)="\d*"/', '', $html);
    }

    public function legacy_functions()
    {
        include_once 'includes/rest-menus.php';
        include_once 'includes/rest-options.php';
        include_once 'includes/rest-pages.php';
        include_once 'includes/rest-posts.php';
    }
}

new StarterSite();
