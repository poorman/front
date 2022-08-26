<?php
/**
 * Front Walker Offcanvas Modal Menu
 *
 * @package front
 */

/* Check if Class Exists. */
if ( ! class_exists( 'Front_Walker_Offcanvas_Modal_Menu' ) ) {
    /**
     * Front_Walker_Offcanvas_Modal_Menu class.
     *
     * @extends Walker_Nav_Menu
     */
    class Front_Walker_Offcanvas_Modal_Menu extends Walker_Nav_Menu {

        private $parent_menu_id = '';

        /**
         * Starts the list before the elements are added.
         *
         * @since WP 3.0.0
         *
         * @see Walker_Nav_Menu::start_lvl()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = str_repeat( $t, $depth );

            $children_id = '';
            if ( ! empty( $this->parent_menu_id ) ) {
                $children_id = 'id="modal-offcanvas-menu-' . $this->parent_menu_id . '"';
            }

            // Default class to add to the file.
            $classes[] = 'u-fullscreen__nav-list collapse';
            /**
             * Filters the CSS class(es) applied to a menu list element.
             *
             * @since WP 4.8.0
             *
             * @param array    $classes The CSS classes that are applied to the menu `<ul>` element.
             * @param stdClass $args    An object of `wp_nav_menu()` arguments.
             * @param int      $depth   Depth of menu item. Used for padding.
             */
            $class_names = join( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
            /**
             * The `.dropdown-menu` container needs to have a labelledby
             * attribute which points to it's trigger link.
             *
             * Form a string for the labelledby attribute from the the latest
             * link with an id that was added to the $output.
             */
            $labelledby = '';
            // find all links with an id in the output.
            preg_match_all( '/(<a.*?id=\"|\')(.*?)\"|\'.*?>/im', $output, $matches );
            // with pointer at end of array check if we got an ID match.
            if ( end( $matches[2] ) ) {
                // build a string to use as aria-labelledby.
                $labelledby = 'aria-labelledby="' . end( $matches[2] ) . '"';
            }
            $output .= "{$n}{$indent}<ul $children_id $class_names $labelledby role=\"menu\">{$n}";
        }

        /**
         * Ends the list of after the elements are added.
         *
         * @since 3.0.0
         *
         * @see Walker::end_lvl()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent  = str_repeat( $t, $depth );
            $output .= "$indent</ul>{$n}";
        }

        /**
         * Starts the element output.
         *
         * @since WP 3.0.0
         * @since WP 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
         *
         * @see Walker_Nav_Menu::start_el()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param WP_Post  $item   Menu item data object.
         * @param int      $depth  Depth of menu item. Used for padding.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         * @param int      $id     Current item ID.
         */
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
            
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

            $this->parent_menu_id = $item->ID;

            $classes = empty( $item->classes ) ? array() : (array) $item->classes;

            // Initialize some holder variables to store specially handled item
            // wrappers and icons.
            $linkmod_classes = array();
            $icon_classes    = array();
            $btn_classes     = array();

            /**
             * Get an updated $classes array without linkmod or icon classes.
             *
             * NOTE: linkmod and icon class arrays are passed by reference and
             * are maybe modified before being used later in this function.
             */
            $classes = front_separate_linkmods_and_icons_from_classes( $classes, $linkmod_classes, $icon_classes, $btn_classes, $depth );

            // Join any icon classes plucked from $classes into a string.
            $icon_class_string = join( ' ', $icon_classes );

            /**
             * Filters the arguments for a single nav menu item.
             *
             *  WP 4.4.0
             *
             * @param stdClass $args  An object of wp_nav_menu() arguments.
             * @param WP_Post  $item  Menu item data object.
             * @param int      $depth Depth of menu item. Used for padding.
             */
            $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

            // Add .dropdown or .active classes where they are needed.
            if ( isset( $args->has_children ) && $args->has_children ) {
                $classes[] = 'u-has-submenu u-fullscreen__submenu';
            }

            if ( in_array( 'current-menu-item', $classes, true ) || in_array( 'current-menu-parent', $classes, true ) ) {
                $classes[] = 'active';
            }

            // Add some additional default classes to the item.
            $classes[] = 'menu-item-' . $item->ID;

            // Allow filtering the classes.
            $classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );

            // Form a string of classes in format: class="class_names".
            $class_names = join( ' ', $classes );
            $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

            /**
             * Filters the ID applied to a menu item's list item element.
             *
             * @since WP 3.0.1
             * @since WP 4.1.0 The `$depth` parameter was added.
             *
             * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
             * @param WP_Post  $item    The current menu item.
             * @param stdClass $args    An object of wp_nav_menu() arguments.
             * @param int      $depth   Depth of menu item. Used for padding.
             */
            $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
            $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

            $output .= $indent . '<li' . $id . $class_names . '>';

            // initialize array for holding the $atts for the link item.
            $atts = array();

            // Set title from item to the $atts array - if title is empty then
            // default to item title.
            if ( empty( $item->attr_title ) ) {
                $atts['title'] = ! empty( $item->title ) ? strip_tags( $item->title ) : '';
            } else {
                $atts['title'] = $item->attr_title;
            }

            $atts['target']             = ! empty( $item->target ) ? $item->target : '';
            $atts['rel']                = ! empty( $item->xfn ) ? $item->xfn : '';
            $atts['class']              = 'u-fullscreen__nav-link';
            $atts['href']               = ( ! $args->has_children && ! empty( $item->url ) ) ? $item->url : '#modal-offcanvas-menu-' . $item->ID;
            if ( isset( $args->has_children ) && $args->has_children ) {
                $atts['role']               = 'button';
                $atts['data-toggle']        = 'collapse';
                $atts['aria-expanded']      = 'false';
                $atts['aria-controls']      = 'modal-offcanvas-menu-' . $item->ID;
            }

            if ( ! empty( $btn_classes ) ) {
                $atts['class'] = join( ' ', $btn_classes );
            }

            // update atts of this item based on any custom linkmod classes.
            $atts = self::update_atts_for_linkmod_type( $atts, $linkmod_classes );
            // Allow filtering of the $atts array before using it.
            $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

            // Build a string of html containing all the atts for the item.
            $attributes = '';
            foreach ( $atts as $attr => $value ) {
                if ( ! empty( $value ) ) {
                    $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                    $attributes .= ' ' . $attr . '="' . $value . '"';
                }
            }

            /**
             * Set a typeflag to easily test if this is a linkmod or not.
             */
            $linkmod_type = self::get_linkmod_type( $linkmod_classes );

            /**
             * START appending the internal item contents to the output.
             */
            $item_output = isset( $args->before ) ? $args->before : '';
            /**
             * This is the start of the internal nav item. Depending on what
             * kind of linkmod we have we may need different wrapper elements.
             */
            if ( '' !== $linkmod_type ) {
                // is linkmod, output the required element opener.
                $item_output .= self::linkmod_element_open( $linkmod_type, $attributes );
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '<a' . $attributes . '>';
            }

            /**
             * Initiate empty icon var, then if we have a string containing any
             * icon classes form the icon markup with an <i> element. This is
             * output inside of the item before the $title (the link text).
             */
            $icon_html = '';
            if ( ! empty( $icon_class_string ) ) {
                // append an <i> with the icon classes to what is output before links.
                $icon_html = '<i class="' . esc_attr( $icon_class_string ) . '" aria-hidden="true"></i> ';
            }

            /** This filter is documented in wp-includes/post-template.php */
            $title = apply_filters( 'the_title', $item->title, $item->ID );

            /**
             * Filters a menu item's title.
             *
             * @since WP 4.4.0
             *
             * @param string   $title The menu item's title.
             * @param WP_Post  $item  The current menu item.
             * @param stdClass $args  An object of wp_nav_menu() arguments.
             * @param int      $depth Depth of menu item. Used for padding.
             */
            $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

            /**
             * If the .sr-only class was set apply to the nav items text only.
             */
            if ( in_array( 'sr-only', $linkmod_classes, true ) ) {
                $title         = self::wrap_for_screen_reader( $title );
                $keys_to_unset = array_keys( $linkmod_classes, 'sr-only' );
                foreach ( $keys_to_unset as $k ) {
                    unset( $linkmod_classes[ $k ] );
                }
            }

            // Put the item contents into $output.
            $item_output .= isset( $args->link_before ) ? $args->link_before . $icon_html . $title . $args->link_after : '';
            /**
             * This is the end of the internal nav item. We need to close the
             * correct element depending on the type of link or link mod.
             */
            if ( '' !== $linkmod_type ) {
                // is linkmod, output the required element opener.
                $item_output .= self::linkmod_element_close( $linkmod_type, $attributes );
            } else {
                // With no link mod type set this must be a standard <a> tag.
                $item_output .= '</a>';
            }

            $item_output .= isset( $args->after ) ? $args->after : '';

            /**
             * END appending the internal item contents to the output.
             */
            $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
        }

        /**
         * Ends the element output, if needed.
         *
         * @since 3.0.0
         *
         * @see Walker::end_el()
         *
         * @param string   $output Used to append additional content (passed by reference).
         * @param WP_Post  $item   Page data object. Not used.
         * @param int      $depth  Depth of page. Not Used.
         * @param stdClass $args   An object of wp_nav_menu() arguments.
         */
        public function end_el( &$output, $item, $depth = 0, $args = array() ) {
            if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
                $t = '';
                $n = '';
            } else {
                $t = "\t";
                $n = "\n";
            }
            $output .= "</li>{$n}";
        }

        /**
         * Traverse elements to create list from elements.
         *
         * Display one element if the element doesn't have any children otherwise,
         * display the element and its children. Will only traverse up to the max
         * depth and no ignore elements under that depth. It is possible to set the
         * max depth to include all depths, see walk() method.
         *
         * This method should not be called directly, use the walk() method instead.
         *
         * @since WP 2.5.0
         *
         * @see Walker::start_lvl()
         *
         * @param object $element           Data object.
         * @param array  $children_elements List of elements to continue traversing (passed by reference).
         * @param int    $max_depth         Max depth to traverse.
         * @param int    $depth             Depth of current element.
         * @param array  $args              An array of arguments.
         * @param string $output            Used to append additional content (passed by reference).
         */
        public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {
            if ( ! $element ) {
                return; }
            $id_field = $this->db_fields['id'];
            // Display this element.
            if ( is_object( $args[0] ) ) {
                $args[0]->has_children = ! empty( $children_elements[ $element->$id_field ] ); }
            parent::display_element( $element, $children_elements, $max_depth, $depth, $args, $output );
        }

        /**
         * Menu Fallback
         * =============
         * If this function is assigned to the wp_nav_menu's fallback_cb variable
         * and a menu has not been assigned to the theme location in the WordPress
         * menu manager the function with display nothing to a non-logged in user,
         * and will add a link to the WordPress menu manager if logged in as an admin.
         *
         * @param array $args passed from the wp_nav_menu function.
         */
        public static function fallback( $args ) {
            if ( current_user_can( 'edit_theme_options' ) ) {

                /* Get Arguments. */
                $container       = $args['container'];
                $container_id    = $args['container_id'];
                $container_class = $args['container_class'];
                $menu_class      = $args['menu_class'];
                $menu_id         = $args['menu_id'];

                // initialize var to store fallback html.
                $fallback_output = '';

                if ( $container ) {
                    $fallback_output .= '<' . esc_attr( $container );
                    if ( $container_id ) {
                        $fallback_output .= ' id="' . esc_attr( $container_id ) . '"';
                    }
                    if ( $container_class ) {
                        $fallback_output .= ' class="' . esc_attr( $container_class ) . '"';
                    }
                    $fallback_output .= '>';
                }
                $fallback_output .= '<ul';
                if ( $menu_id ) {
                    $fallback_output .= ' id="' . esc_attr( $menu_id ) . '"'; }
                if ( $menu_class ) {
                    $fallback_output .= ' class="' . esc_attr( $menu_class ) . '"'; }
                $fallback_output .= '>';
                $fallback_output .= '<li class="text-white"><a href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '" title="' . esc_attr__( 'Add a menu', 'front' ) . '">' . esc_html__( 'Add a menu', 'front' ) . '</a></li>';
                $fallback_output .= '</ul>';
                if ( $container ) {
                    $fallback_output .= '</' . esc_attr( $container ) . '>';
                }

                // if $args has 'echo' key and it's true echo, otherwise return.
                if ( array_key_exists( 'echo', $args ) && $args['echo'] ) {
                    echo wp_kses_post( $fallback_output ); // WPCS: XSS OK.
                } else {
                    return $fallback_output;
                }
            }
        }

        /**
         * Return a string containing a linkmod type and update $atts array
         * accordingly depending on the decided.
         *
         * @since 4.0.0
         *
         * @param array $linkmod_classes array of any link modifier classes.
         *
         * @return string                empty for default, a linkmod type string otherwise.
         */
        private function get_linkmod_type( $linkmod_classes = array() ) {
            $linkmod_type = '';
            // Loop through array of linkmod classes to handle their $atts.
            if ( ! empty( $linkmod_classes ) ) {
                foreach ( $linkmod_classes as $link_class ) {
                    if ( ! empty( $link_class ) ) {

                        // check for special class types and set a flag for them.
                        if ( 'dropdown-header' === $link_class ) {
                            $linkmod_type = 'dropdown-header';
                        } elseif ( 'dropdown-divider' === $link_class ) {
                            $linkmod_type = 'dropdown-divider';
                        } elseif ( 'dropdown-item-text' === $link_class ) {
                            $linkmod_type = 'dropdown-item-text';
                        }
                    }
                }
            }
            return $linkmod_type;
        }

        /**
         * Update the attributes of a nav item depending on the limkmod classes.
         *
         * @since 4.0.0
         *
         * @param array $atts            array of atts for the current link in nav item.
         * @param array $linkmod_classes an array of classes that modify link or nav item behaviors or displays.
         *
         * @return array                 maybe updated array of attributes for item.
         */
        private function update_atts_for_linkmod_type( $atts = array(), $linkmod_classes = array() ) {
            if ( ! empty( $linkmod_classes ) ) {
                foreach ( $linkmod_classes as $link_class ) {
                    if ( ! empty( $link_class ) ) {
                        // update $atts with a space and the extra classname...
                        // so long as it's not a sr-only class.
                        if ( 'sr-only' !== $link_class ) {
                            $atts['class'] .= ' ' . esc_attr( $link_class );
                        }
                        // check for special class types we need additional handling for.
                        if ( 'disabled' === $link_class ) {
                            // Convert link to '#' and unset open targets.
                            $atts['href'] = '#';
                            unset( $atts['target'] );
                        } elseif ( 'dropdown-header' === $link_class || 'dropdown-divider' === $link_class || 'dropdown-item-text' === $link_class ) {
                            // Store a type flag and unset href and target.
                            unset( $atts['href'] );
                            unset( $atts['target'] );
                        }
                    }
                }
            }
            return $atts;
        }

        /**
         * Wraps the passed text in a screen reader only class.
         *
         * @since 4.0.0
         *
         * @param string $text the string of text to be wrapped in a screen reader class.
         * @return string      the string wrapped in a span with the class.
         */
        private function wrap_for_screen_reader( $text = '' ) {
            if ( $text ) {
                $text = '<span class="sr-only">' . $text . '</span>';
            }
            return $text;
        }

        /**
         * Returns the correct opening element and attributes for a linkmod.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a sting containing a linkmod type flag.
         * @param string $attributes   a string of attributes to add to the element.
         *
         * @return string              a string with the openign tag for the element with attribibutes added.
         */
        private function linkmod_element_open( $linkmod_type, $attributes = '' ) {
            $output = '';
            if ( 'dropdown-item-text' === $linkmod_type ) {
                $output .= '<span class="dropdown-item-text">';
            } elseif ( 'dropdown-header' === $linkmod_type ) {
                // For a header use a span with the .h6 class instead of a real
                // header tag so that it doesn't confuse screen readers.
                $output .= '<span class="dropdown-header h6">';
            } elseif ( 'dropdown-divider' === $linkmod_type ) {
                // this is a divider.
                $output .= '<div class="dropdown-divider">';
            }
            return $output;
        }

        /**
         * Return the correct closing tag for the linkmod element.
         *
         * @since 4.0.0
         *
         * @param string $linkmod_type a string containing a special linkmod type.
         *
         * @return string              a string with the closing tag for this linkmod type.
         */
        private function linkmod_element_close( $linkmod_type ) {
            $output = '';
            if ( 'dropdown-header' === $linkmod_type || 'dropdown-item-text' === $linkmod_type ) {
                // For a header use a span with the .h6 class instead of a real
                // header tag so that it doesn't confuse screen readers.
                $output .= '</span>';
            } elseif ( 'dropdown-divider' === $linkmod_type ) {
                // this is a divider.
                $output .= '</div>';
            }
            return $output;
        }
    }
}