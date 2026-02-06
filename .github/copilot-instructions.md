# Copilot Instructions: Hasimuener Journal Theme

## Project Overview
**Hasimuener Journal** is a WordPress child theme extending [GeneratePress](https://generatepress.com/). It's designed as a high-performance journal/blog theme for hasim-org.

- **Parent Theme**: GeneratePress (assumed active in WordPress installation)
- **Language**: PHP (hooks/actions), CSS
- **Structure**: Minimal child theme with `functions.php` and `style.css`

## Key Principles

### 1. Child Theme Architecture
- **functions.php**: Enqueue styles via WordPress `wp_enqueue_scripts` hook
- **style.css**: Header metadata required for WordPress theme registration; include only child-specific styles (GeneratePress CSS is inherited)
- Never modify parent theme files; extend via child theme only

### 2. WordPress Integration
- Use WordPress hooks (`add_action`, `add_filter`, `wp_enqueue_scripts`)
- Follow naming convention: `hp_` prefix for custom functions (e.g., `hp_journal_enqueue_styles()`)
- Child theme stylesheet is enqueued via `get_template_directory_uri()` pointing to parent

### 3. CSS & Styling
- GeneratePress styles are automatically inherited; only override/extend in child theme's `style.css`
- Current baseline: Light gray body (`#f0f2f5`) with dark text (`#333`)
- Maintain responsiveness from parent theme

## Common Workflows

### Adding Custom Styles
1. Add CSS to `style.css` below the header metadata
2. Use specific selectors to override or extend GeneratePress defaults
3. Test in WordPress with Theme Customizer

### Adding Custom Functionality
1. Add hooks/filters to `functions.php` after `hp_journal_enqueue_styles()` definition
2. Follow `hp_` function prefix convention
3. Enqueue any custom scripts via `wp_enqueue_scripts` or `admin_enqueue_scripts`

### Testing
- Activate theme in WordPress installation with GeneratePress as parent
- Use browser DevTools to verify CSS cascading
- Check WordPress admin → Appearance → Customizer for style conflicts

## Files & Their Roles
- [style.css](style.css) - Theme metadata + child-specific CSS
- [functions.php](functions.php) - Hook parent styles, custom functionality entry point
- [README.md](README.md) - Main documentation
