<?php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

if ( !defined('ABSPATH') ) {
    die();
}

function feodoraxis_menu_options() {
    Container::make( 'nav_menu_item', 'Дополнительно' )
        ->add_fields( array(
            Field::make( 'checkbox', 'feodoraxis-menu-is_catalog', 'Выводить каталог' ),
            /**
             * Тут я использую Font Awesome в качестве примера.
             * Добавил только некоторые иконки для примера.
             * Вы можете добавить все те, которые вам нужны из официальной документации
             */
            Field::make( 'select', 'feodoraxis-menu-icon', 'Иконка' )
                ->set_options( array(
                    'fa-music' => "Музыка",
                    'fa-search' => "Поиск",
                    'fa-envelope-o' => "Конверт",
                    'fa-heart' => "Сердце",
                    'fa-star' => "Звезда",
                    'fa-user' => "Пользователь",
                    'fa-film' => "Фильм",
                ) ),
        ));
}
add_action( 'carbon_fields_register_fields', 'feodoraxis_menu_options' );



function feodoraxis_catalog_categories() {
    Container::make( 'term_meta', 'Настройки категорий' )
        /**
         * Я предполагаю, что для ИМ используется WooCommerce
         **/
        ->where( 'term_taxonomy', '=', 'product_cat' )
        ->add_fields( array(
            Field::make( 'association', 'feodoraxis-products', 'Товары категории' )
                ->set_types( array(
                    array(
                        'type'      => 'post',
                        'post_type' => 'product',
                    )
                ) )
                ->set_help_text( 'Укажите товары, которые хотите выводить в меню каталога' ) //Укажем подсказку админу
                ->set_max( 2 ) //У нас вёрстка не допускает больше двух товаров
        ) );
}
add_action( 'carbon_fields_register_fields', 'feodoraxis_catalog_categories' );