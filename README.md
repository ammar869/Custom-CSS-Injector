# Custom CSS Injector WordPress Plugin

A lightweight WordPress plugin that allows site administrators to add custom CSS styles directly from the WordPress admin dashboard without editing theme files.

## Features

- **Easy Admin Interface**: Add custom CSS through a dedicated page under Appearance → Custom CSS
- **Code Editor**: Enhanced textarea with syntax highlighting using WordPress's built-in CodeMirror
- **Security First**: Input sanitization and validation to prevent XSS and other security risks
- **WordPress Standards**: Built using WordPress Settings API and best practices
- **Lightweight**: Minimal footprint with no external dependencies
- **Clean Output**: CSS is properly formatted in the site's `<head>` section

## Installation

1. Download the `custom-css-injector.php` file
2. Upload it to your WordPress site's `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to **Appearance → Custom CSS** to start adding your styles

## Usage

1. Go to **Appearance → Custom CSS** in your WordPress admin
2. Enter your CSS code in the textarea
3. Click **Save CSS**
4. Your custom styles will automatically appear on your site's frontend

### Example CSS

```css
/* Change the site background color */
body {
    background-color: #f0f0f0;
}

/* Style the main heading */
h1 {
    color: #333;
    font-family: 'Arial', sans-serif;
}

/* Add custom button styling */
.custom-button {
    background-color: #007cba;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.custom-button:hover {
    background-color: #005a87;
}
```

## Security Features

The plugin includes several security measures:

- **Input Sanitization**: Removes potentially dangerous content
- **Script Removal**: Strips out JavaScript and other non-CSS code
- **Permission Checks**: Only users with `manage_options` capability can access
- **Nonce Protection**: Uses WordPress Settings API for secure form handling

## Technical Details

- **WordPress Version**: Requires WordPress 4.9+ (for CodeMirror support)
- **PHP Version**: Compatible with PHP 7.0+
- **Database**: Uses WordPress Options API for storage
- **Hooks Used**: `admin_menu`, `admin_init`, `wp_head`, `admin_enqueue_scripts`

## Extending the Plugin

The plugin is designed to be easily extensible. You can:

- Add custom validation rules in the `sanitize_css()` method
- Modify the admin interface in the `admin_page()` method
- Add additional CSS processing in the `output_custom_css()` method
- Hook into the plugin's functionality using WordPress actions and filters

## Best Practices

- Always test CSS changes on a staging site first
- Use browser developer tools to test styles before adding them
- Be specific with CSS selectors to avoid theme conflicts
- Use `!important` sparingly and only when necessary
- Keep CSS organized with comments for better maintenance

## Troubleshooting

**CSS not appearing on frontend:**
- Check that the plugin is activated
- Verify you have the correct permissions
- Clear any caching plugins
- Check browser developer tools for the `<style id="custom-css-injector-styles">` tag

**Admin page not accessible:**
- Ensure your user has `manage_options` capability (Administrator role)
- Check for plugin conflicts by deactivating other plugins temporarily

## License

This plugin is licensed under the GPL v2 or later.

## Support

For support and feature requests, please create an issue in the project repository.