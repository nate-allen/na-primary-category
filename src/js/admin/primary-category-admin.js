/**
 * Admin functionality
 */

"use strict";

let primary_category = {};

primary_category.admin = function ($) {
    // Private Variables
    let $window = $(window),
        $doc = $(document),
        $body = $('body'),
        $elements = {
            category_checklist: $('#categorychecklist'),
            hidden_field : $('.na-primary-category-id')
        },
        self;

    return {

        // Kick everything off
        init: function () {
            self = primary_category.admin;

            $body
                .on( 'change', '#categorychecklist input[type=checkbox]', self.setup_buttons )
                .on( 'click', '.na-make-primary-category', self.make_category_primary )
                .on( 'change', '.yoast-wpseo-primary-term', self.update_hidden_field );

            self.setup_primary_category();
            self.maybe_setup_buttons();
        },

        /**
         * Add 'Make Primary' buttons next to categories if Yoast SEO isn't installed
         *
         * Yoast SEO is the most popular plugin, and has its own "Make Primary" feature, so this
         * ensures that our plugin doesn't create unnecessary duplicate buttons
         */
        maybe_setup_buttons: function() {
            // If Yoast SEO is installed, we'll use their buttons
            if ( window.primary_category_admin.yoast_seo_installed ) {
                return;
            }

            // Don't show buttons if user doesn't have permission to set the primary category
            if ( ! window.primary_category_admin.user_has_permission ) {
                return;
            }

            // Don't show buttons if there's only 1 category selected
            if ( $elements.category_checklist.find('input[type=checkbox]:checked').length < 2 ) {
                return;
            }

            // Setup the buttons
            self.setup_buttons();
        },

        /**
         * Determines which categories need buttons added or removed
         */
        setup_buttons: function() {
            // If Yoast SEO is installed, we'll use their buttons
            if ( window.primary_category_admin.yoast_seo_installed ) {
                return;
            }

            // Loop over each category
            $elements.category_checklist.find('li').each(function(){
                let category = $(this).prop('id');

                // Don't add buttons to unchecked categories
                if ( ! $(this).find('input[type=checkbox]').prop('checked') ) {
                    // Remove button if there was one
                    self.remove_make_primary_button( category );

                    // Remove label if there was one
                    self.remove_primary_label( category );

                    // Skip to next category
                    return true;
                }

                // Add a "Primary" label if this is the primary category, otherwise add a "Make Primary" button
                if ( $(this).hasClass( 'na-is-primary-category' ) ) {
                    self.add_primary_label( category );
                } else {
                    self.add_make_primary_button( category );
                }
            });

            // Get the first checked category
            let category = $elements.category_checklist.find('input[type=checkbox]:checked:first').parents('li').prop('id');

            // Make sure at least one of the categories is the primary one
            if ( ! $elements.category_checklist.find('.na-is-primary-category').length ) {
                self.add_primary_label(category);
            }

            // If there's only 1 category, remove primary label
            if ( $elements.category_checklist.find('input[type=checkbox]:checked').length === 1 ) {
                self.remove_primary_label(category);
            }

            // If there are no categories, remove primary category from hidden field
            if ( ! $elements.category_checklist.find('input[type=checkbox]:checked').length ) {
                $elements.hidden_field.val('');
            }
        },

        /**
         * Set the primary category when the page loads
         */
        setup_primary_category: function() {
            // If Yoast SEO is installed, we'll use their primary category
            if ( window.primary_category_admin.yoast_seo_installed ) {
                let category_id = $('.yoast-wpseo-primary-term').val();

                $elements.hidden_field.val( category_id );

                return;
            }

            if ( $elements.hidden_field.val() ) {
                let category_id = $elements.hidden_field.val();

                self.add_primary_label( 'category-' + category_id );
            }
        },

        /**
         * Marks a category as the primary one
         */
        make_category_primary: function() {
            // Reset primary category (in case there was one)
            $elements.category_checklist.find('.na-is-primary-category').removeClass('na-is-primary-category');

            // Add class to category for styling
            $( this ).parents( 'li' ).addClass( 'na-is-primary-category' );

            self.setup_buttons();
        },

        /**
         * Adds a "Make Primary" button next to a category
         *
         * @param category string The category to add a button to
         */
        add_make_primary_button: function( category ) {
            let $category = $( '#' + category );
            let $button   = $('<button>', {type: 'button', class: 'na-make-primary-category'}).text(window.primary_category_admin.i18n.make_primary);

            // Don't add a button if there's already one
            if ( $category.find('.na-make-primary-category').length ) {
                return;
            }

            // Remove "Primary" label if there is one
            self.remove_primary_label( category );

            // Add a button
            $category.append( $button );
        },

        /**
         * Adds a "Primary" label next to the category that is currently the primary one
         *
         * @param category
         */
        add_primary_label: function( category ) {
            let $category   = $( '#' + category );
            let $label      = $('<span>', {class: 'na-primary'}).text(window.primary_category_admin.i18n.primary);

            // Skip if it already has a label
            if ( $category.find('.na-primary').length ) {
                return;
            }

            // Remove "Make Primary" button if there is one
            self.remove_make_primary_button( category );

            $category.addClass( 'na-is-primary-category' ).append( $label );

            // Set the primary category id to the hidden field
            if ( category ) {
                let category_id = category.match( /\d+/g );

                $elements.hidden_field.val( category_id );
            }
        },

        /**
         * Removes the "Make Primary" button next to a category
         *
         * @param category string The category to remove a button from
         */
        remove_make_primary_button: function( category ) {
            let $category = $( '#' + category );

            // Remove button
            $category.find( '.na-make-primary-category' ).remove();

            // If there's only 1 category checked, remove its button too
            if ( $elements.category_checklist.find('input[type=checkbox]:checked').length < 2) {
                $elements.category_checklist.find( '.na-make-primary-category' ).remove();
            }
        },

        /**
         * Removes the "Primary" label next to a category
         *
         * @param category string The category to remove the label from
         */
        remove_primary_label: function( category ) {
            let $category = $( '#' + category );

            // Remove label
            $category.find( '.na-primary' ).remove();

            // Remove class
            if ( $category.hasClass('na-is-primary-category') ) {
                $category.removeClass( 'na-is-primary-category' );

                self.maybe_set_new_primary_category();
            }
        },

        /**
         * Maybe set a new category as the primary one
         *
         * When a primary category is unchecked, we should set a new primary category unless
         * there are less than 2 categories selected
         */
        maybe_set_new_primary_category: function() {
            // Don't set a primary category if there aren't more than 1 category selected
            if ( $elements.category_checklist.find('input[type=checkbox]:checked').length < 2 ) {
                return;
            }

            // Get the first checked category
            let category = $elements.category_checklist.find('input[type=checkbox]:checked:first').parents('li').prop('id');

            self.add_primary_label(category);
        },

        /**
         * If Yoast SEO is installed, update our hidden field when their primary category changes
         */
        update_hidden_field: function() {
            let category_id = $('.yoast-wpseo-primary-term').val();

            $elements.hidden_field.val( category_id );
        }

    };
}(jQuery);

jQuery(function ($) {
    primary_category.admin.init();
});
