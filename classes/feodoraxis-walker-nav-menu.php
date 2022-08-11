<?php
if ( !defined('ABSPATH') ) {
    die();
}

class Feodoraxis_Walker_Nav_Menu extends Walker_Nav_Menu {
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = str_repeat( $t, $depth );

        /**
         * Заменим .sub-menu на .dropdown-menu
         */
        $classes = array( 'dropdown-menu' );

        $class_names = implode( ' ', apply_filters( 'nav_menu_submenu_css_class', $classes, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $output .= "{$n}{$indent}<ul$class_names>{$n}";
    }

    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $menu_item = $data_object;

        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

        $classes   = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
        $classes[] = 'menu-item-' . $menu_item->ID;

        /**
         * Добавим Bootstrap-класс для пунктов меню первого уровня
         */
        if ( $depth === 0 ) {
            $classes[] = 'nav-item';
        }

        $args = apply_filters( 'nav_menu_item_args', $args, $menu_item, $depth );

        $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $menu_item->ID, $menu_item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names . '>';

        $atts           = array();
        $atts['title']  = ! empty( $menu_item->attr_title ) ? $menu_item->attr_title : '';
        $atts['target'] = ! empty( $menu_item->target ) ? $menu_item->target : '';
        if ( '_blank' === $menu_item->target && empty( $menu_item->xfn ) ) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $menu_item->xfn;
        }
        $atts['href']         = ! empty( $menu_item->url ) ? $menu_item->url : '';
        $atts['aria-current'] = $menu_item->current ? 'page' : '';

        /**
         * Добавим классы ссылкам первого уровня
         */
        if ( $depth === 0 ) {
            $atts['class'] = 'nav-link';

            /**
             * Если есть дочерние пункты меню, или выводится меню каталога - добавим еще атриубты
             */
            if ( in_array('menu-item-has-children', $menu_item->classes ) || carbon_get_nav_menu_item_meta( $menu_item->ID, 'feodoraxis-menu-is_catalog' ) ) {
                $atts['class'] .= 'dropdown-toggle';

                $atts['role'] = 'button';
                $atts['data-bs-toggle'] = 'dropdown';
                $atts['aria-expanded'] = 'false';
            }
        }

        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
                $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );

        $title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';

        /**
         * Выведем иконки
         */
        if ( $depth > 0 ) {
            $icon_class = carbon_get_nav_menu_item_meta( $menu_item->ID, 'feodoraxis-menu-icon');
            if ( !empty( $icon_class  ) ) {
                $item_output .= '<i class="fa ' . $icon_class . '"></i> ';
            }
        }

        $item_output .= $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';

        /**
         * Если это меню каталога, то выведем его при помощи дополнительного метода, который создадим прямо в этом классе
         */
        if ( carbon_get_nav_menu_item_meta( $menu_item->ID, 'feodoraxis-menu-is_catalog' ) ) {
            $item_output .= $this->get_catalog();
        }

        $item_output .= $args->after;

        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
    }

    /**
     * @return string
     *
     * Метод вывода каталога товаров.
     * Т.к. мы будем использовать функцию wc_get_template_part(), то нам потребуется кеширование данных на вывод
     */
    protected function get_catalog():string {

        global $post;

        /**
         * Сначала подготовим данные
         */
        $categories = get_terms( [
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
            'orderby' => 'parent',
            'exclude' => [15] //Не выводим Uncategorized
        ] );

        $items = $products_categories = [];

        /**
         * Подготовим массив для удобного вывода
         */
        foreach ( $categories as $category ) {
            if ( $category->parent === 0 ) {
                $items[ $category->term_id ] = [
                    'term_id'          => $category->term_id,
                    'name'             => $category->name,
                    'slug'             => $category->slug,
                    'term_group'       => $category->term_group,
                    'term_taxonomy_id' => $category->term_taxonomy_id,
                    'taxonomy'         => $category->taxonomy,
                    'description'      => $category->description,
                    'parent'           => $category->parent,
                    'count'            => $category->count,
                    'filter'           => $category->filter,
                ];

                /**
                 * Сразу получим товары для категорий
                 */
                $products = carbon_get_term_meta( $category->term_id, 'feodoraxis-products' );
                if ( !empty( $products ) ) {
                    foreach ( $products as $product ) {
                        $products_categories[ $category->term_id ][] = $product['id'];
                    }
                }
                unset($products);
            } else {
                $items[ $category->parent ]['child'][ $category->term_id ] = [
                    'term_id'          => $category->term_id,
                    'name'             => $category->name,
                    'slug'             => $category->slug,
                    'term_group'       => $category->term_group,
                    'term_taxonomy_id' => $category->term_taxonomy_id,
                    'taxonomy'         => $category->taxonomy,
                    'description'      => $category->description,
                    'parent'           => $category->parent,
                    'count'            => $category->count,
                    'filter'           => $category->filter,
                ];
            }
        }

        /**
         * Начало буферизации
         */
        ob_start();

        echo '<div class="dropdown-menu dropdown-catalog">
                    <div class="catalog_menu">
                        <div class="catalog_menu-flexbox">';

        /**
         * Выведем категории
         */
        echo '<div class="catalog_menu-nav">
                        <ul>';

        foreach ( $items as $item ) {
            echo '<li>';
            echo '<a href="' . get_term_link( $item['term_id'] ) . '" data-category="#category_' . $item['term_id'] . '">' . $item['name'] . '</a>';

            /**
             * В этом месте больше подошла-бы рекурсия, но мы упрощаем код для более простого понимания
             */
            if ( isset( $item['child'] ) ) {
                echo '<ul>';

                foreach ( $item['child'] as $child ) {
                    echo '<li><a href="' . get_term_link( $child['term_id'] ) . '">' . $child['name'] . '</a></li>';
                }

                echo '</ul>';
            }

            echo '</li>';
        }

        echo '</ul>
            </div>';

        /**
         * Выведем секции с превью-карточкам товаров
         */
        echo '<div class="catalog_menu-products">';

        if ( !empty( $products_categories ) ) {
            foreach ( $products_categories as $key => $products ) {
                echo '<div class="catalog_menu-category" id="category_' . $key . '">
                        <div class="catalog_menu-products-flexbox">';

                /**
                 * Т.к. в стандартном шаблоне WC превью картоки товара выводятся при помощи списка - их нужно завернуть в родительский тег ul
                 */
                echo '<ul>';

                foreach ( $products as $post ) {
                    setup_postdata( $post );

                    echo '<div class="catalog_menu-product">';
                    wc_get_template_part( 'content', 'product' );
                    echo '</div>';
                }

                wp_reset_postdata();

                echo '</ul>
                    </div>
                </div>';
            }
        }
        echo '</div>';

        /**
         * Закрываем каталог меню
         */
        echo '</div>
            </div>
        </div>';

        /**
         * Конец буферизации и вывод разметки
         */
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }
}