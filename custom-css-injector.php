<?php
/**
 * Plugin Name: Custom CSS Injector
 * Plugin URI: http://127.0.0.1:5500/index.html
 * Description: Allows site administrators to add custom CSS styles directly from the WordPress admin dashboard without editing theme files.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: custom-css-injector
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main Custom CSS Injector Class
 */
class CustomCSSInjector {
    
    /**
     * Option name for storing custom CSS
     */
    const OPTION_NAME = 'custom_css_injector_styles';
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('wp_head', array($this, 'output_custom_css'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    /**
     * Add menu item under Appearance
     */
    public function add_admin_menu() {
        add_theme_page(
            __('Custom CSS', 'custom-css-injector'),
            __('Custom CSS', 'custom-css-injector'),
            'manage_options',
            'custom-css-injector',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Register settings using Settings API
     */
    public function register_settings() {
        register_setting(
            'custom_css_injector_settings',
            self::OPTION_NAME,
            array(
                'sanitize_callback' => array($this, 'sanitize_css'),
                'default' => ''
            )
        );
        
        add_settings_section(
            'custom_css_injector_section',
            __('Custom CSS Styles', 'custom-css-injector'),
            array($this, 'settings_section_callback'),
            'custom-css-injector'
        );
        
        add_settings_field(
            'custom_css_field',
            __('CSS Code', 'custom-css-injector'),
            array($this, 'css_field_callback'),
            'custom-css-injector',
            'custom_css_injector_section'
        );
    }
    
    /**
     * Sanitize CSS input
     */
    public function sanitize_css($input) {
        // Remove any potential script tags or dangerous content
        $input = wp_strip_all_tags($input);
        
        // Allow only CSS-safe characters and properties
        $input = preg_replace('/[<>"\']/', '', $input);
        
        // Remove any javascript: or expression() attempts
        $input = preg_replace('/javascript\s*:/i', '', $input);
        $input = preg_replace('/expression\s*\(/i', '', $input);
        $input = preg_replace('/vbscript\s*:/i', '', $input);
        $input = preg_replace('/onload\s*=/i', '', $input);
        
        // Remove any @import statements for security
        $input = preg_replace('/@import\s+/i', '', $input);
        
        return $input;
    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>' . __('Add your custom CSS styles below. The CSS will be automatically added to your site\'s head section.', 'custom-css-injector') . '</p>';
        echo '<p><strong>' . __('Warning:', 'custom-css-injector') . '</strong> ' . __('Only add CSS code. JavaScript and other scripts will be removed for security.', 'custom-css-injector') . '</p>';
    }
    
    /**
     * CSS field callback
     */
    public function css_field_callback() {
        $css = get_option(self::OPTION_NAME, '');
        echo '<textarea id="custom-css-textarea" name="' . self::OPTION_NAME . '" rows="20" cols="80" class="large-text code">' . esc_textarea($css) . '</textarea>';
        echo '<p class="description">' . __('Enter your custom CSS code here. Example: body { background-color: #f0f0f0; }', 'custom-css-injector') . '</p>';
    }
    
    /**
     * Admin page content
     */
    public function admin_page() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'custom-css-injector'));
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php
            // Show success message after saving
            if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {
                add_settings_error(
                    'custom_css_injector_messages',
                    'custom_css_injector_message',
                    __('Custom CSS saved successfully!', 'custom-css-injector'),
                    'updated'
                );
            }
            
            settings_errors('custom_css_injector_messages');
            ?>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('custom_css_injector_settings');
                do_settings_sections('custom-css-injector');
                submit_button(__('Save CSS', 'custom-css-injector'));
                ?>
            </form>
            
            <div class="custom-css-info">
                <h3><?php _e('Tips for Using Custom CSS:', 'custom-css-injector'); ?></h3>
                <ul>
                    <li><?php _e('Always test your CSS changes on a staging site first.', 'custom-css-injector'); ?></li>
                    <li><?php _e('Use browser developer tools to test CSS before adding it here.', 'custom-css-injector'); ?></li>
                    <li><?php _e('Be specific with your selectors to avoid conflicts with theme styles.', 'custom-css-injector'); ?></li>
                    <li><?php _e('Consider using !important sparingly and only when necessary.', 'custom-css-injector'); ?></li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Output custom CSS in the head section
     */
    public function output_custom_css() {
        $css = get_option(self::OPTION_NAME, '');
        
        if (!empty($css)) {
            echo "\n<!-- Custom CSS Injector -->\n";
            echo '<style type="text/css" id="custom-css-injector-styles">' . "\n";
            echo esc_html($css);
            echo "\n</style>\n";
            echo "<!-- /Custom CSS Injector -->\n";
        }
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        // Only load on our admin page
        if ($hook !== 'appearance_page_custom-css-injector') {
            return;
        }
        
        // Add CodeMirror for better CSS editing experience
        wp_enqueue_code_editor(array('type' => 'text/css'));
        
        // Add custom admin styles
        wp_add_inline_style('wp-admin', '
            .custom-css-info {
                background: #f9f9f9;
                border: 1px solid #ddd;
                border-radius: 4px;
                padding: 15px;
                margin-top: 20px;
            }
            .custom-css-info h3 {
                margin-top: 0;
            }
            .custom-css-info ul {
                margin-bottom: 0;
            }
            #custom-css-textarea {
                font-family: Consolas, Monaco, monospace;
                font-size: 13px;
                line-height: 1.4;
            }
        ');
        
        // Initialize CodeMirror
        wp_add_inline_script('wp-theme-plugin-editor', '
            jQuery(document).ready(function($) {
                if (typeof wp.codeEditor !== "undefined") {
                    var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
                    editorSettings.codemirror = _.extend(
                        {},
                        editorSettings.codemirror,
                        {
                            mode: "css",
                            lineNumbers: true,
                            lineWrapping: true,
                            styleActiveLine: true,
                            continueComments: true,
                            extraKeys: {
                                "Ctrl-Space": "autocomplete",
                                "Ctrl-/": "toggleComment",
                                "Cmd-/": "toggleComment"
                            }
                        }
                    );
                    
                    var editor = wp.codeEditor.initialize($("#custom-css-textarea"), editorSettings);
                }
            });
        ');
    }
}

// Initialize the plugin
new CustomCSSInjector();

/**
 * Activation hook - create default option
 */
register_activation_hook(__FILE__, function() {
    add_option(CustomCSSInjector::OPTION_NAME, '');
});

/**
 * Deactivation hook - clean up (optional)
 */
register_deactivation_hook(__FILE__, function() {
    // Optionally remove the option on deactivation
    // delete_option(CustomCSSInjector::OPTION_NAME);
});

/**
 * Uninstall hook - clean up completely
 */
register_uninstall_hook(__FILE__, function() {
    delete_option(CustomCSSInjector::OPTION_NAME);
});