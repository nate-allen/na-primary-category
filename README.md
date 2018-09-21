# Primary Category

### [Latest Stable Version](https://github.com/nate-allen/na-primary-category/releases/latest)

**Primary Category** is a plugin that allows publishers to designate a primary category for posts, and query for posts by their primary category.

## Querying by primary category

This plugin extends `WP_Query` by adding a new parameter, `primary_category`, that accepts the category ID to get posts for have that as their primary category.

### Example
```php
$args = array(
    'primary_category' => 4,
);

$query = new WP_Query( $args );
```

## Scalable and Fast

The **Primary Category** plugin registers a private taxonomy that keeps track of which category is currently the primary one. When you search by primary category, the plugin uses `tax_query` to find the posts that have that primary category set.

Why use a taxonomy and not post meta? Post meta is great for storing unique metadata about a post, but its not efficient for querying. Taxonomies are great for grouping posts and querying by them is faster.

## Compatible with Yoast SEO Plugin

With over 5+ million active installations, the Yoast SEO plugin is one of the most popular plugins in the WordPress plugin directory. Yoast SEO has its own "primary category" functionality that is uses for (among other things) determining which category to show in the breadcrumbs.

**Primary Category** detects if the Yoast SEO plugin is installed and will use the UI that plugin provides. This ensures there aren't multiple "make primary" buttons shown.

Note: Yoast SEO stores their primary category as post meta, and is not meant for querying (which is why you should use this plugin!).
 
## Security 
 
By default, only users who can edit the current post can set the primary category. This plugin provides a filter called `user_can_set_primary_category` that allows you to set your own security rules.
 
### Example
```php
function only_admin_can_edit_primary_category() {
    return current_user_can( 'manage_options' );
}
add_filter( 'user_can_set_primary_category', 'only_admin_can_edit_primary_category' );
```