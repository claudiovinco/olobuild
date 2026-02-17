<?php
/**
 * Create Homepage template with PostGrid elements.
 * Run via: wp eval-file /tmp/create-homepage.php --allow-root
 */

$db = new Mosaic_Database();

$content = [
  // ── Section 1: Hero ──
  [
    'id' => 'sec-hero-001',
    'type' => 'section',
    'settings' => ['style' => 'default', 'width' => 'default', 'padding' => 'large'],
    'style' => ['bg' => ['type' => 'solid', 'color' => '#1e293b']],
    'advanced' => [],
    'children' => [
      [
        'id' => 'row-hero-001',
        'type' => 'row',
        'settings' => ['layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true],
        'style' => [],
        'advanced' => [],
        'children' => [
          [
            'id' => 'col-hero-001',
            'type' => 'column',
            'settings' => ['width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => ''],
            'style' => [],
            'advanced' => [],
            'children' => [
              [
                'id' => 'el-headline-001',
                'type' => 'headline',
                'settings' => [
                  'heading' => 'Explore Our Locations',
                  'subtitle' => 'Browse rentals, museums, restaurants & monuments in Rome',
                  'tag' => 'h1',
                  'alignment' => 'center',
                  'decoration' => 'line',
                  'decoration_color' => '#3b82f6',
                  'heading_color' => '#ffffff',
                  'subtitle_color' => '#94a3b8',
                  'heading_size' => 'lg',
                ],
                'style' => [],
                'advanced' => [],
              ],
            ],
          ],
        ],
      ],
    ],
  ],

  // ── Section 2: PostGrid — Pills + Sort (3 col) ──
  [
    'id' => 'sec-grid1-001',
    'type' => 'section',
    'settings' => ['style' => 'default', 'width' => 'default', 'padding' => 'default'],
    'style' => [],
    'advanced' => [],
    'children' => [
      [
        'id' => 'row-grid1-001',
        'type' => 'row',
        'settings' => ['layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true],
        'style' => [],
        'advanced' => [],
        'children' => [
          [
            'id' => 'col-grid1-001',
            'type' => 'column',
            'settings' => ['width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => ''],
            'style' => [],
            'advanced' => [],
            'children' => [
              [
                'id' => 'el-headline-002',
                'type' => 'headline',
                'settings' => [
                  'heading' => 'All Locations',
                  'subtitle' => 'Filter by category (pills) + sort by price, date or title',
                  'tag' => 'h2',
                  'alignment' => 'left',
                  'decoration' => 'none',
                  'heading_color' => '',
                  'subtitle_color' => '#6b7280',
                  'heading_size' => 'md',
                ],
                'style' => [],
                'advanced' => [],
              ],
              [
                'id' => 'el-postgrid-001',
                'type' => 'postgrid',
                'settings' => [
                  'post_type'       => 'location',
                  'posts_per_page'  => '12',
                  'orderby'         => 'date',
                  'order'           => 'DESC',
                  'meta_key'        => '',
                  'taxonomy'        => 'location_category',
                  'show_filters'    => true,
                  'filter_style'    => 'pills',
                  'show_sort'       => true,
                  'sort_options'    => 'date|price|title',
                  'columns'         => '3',
                  'gap'             => 'medium',
                  'card_style'      => 'default',
                  'image_height'    => '200',
                  'show_image'      => true,
                  'show_category'   => true,
                  'show_excerpt'    => true,
                  'excerpt_length'  => '20',
                  'show_meta'       => true,
                  'show_price'      => true,
                  'price_field'     => 'rental_price_night',
                  'price_prefix'    => '€',
                  'price_suffix'    => '/night',
                  'link_text'       => 'View Details',
                  'link_style'      => 'button',
                ],
                'style' => [],
                'advanced' => [],
              ],
            ],
          ],
        ],
      ],
    ],
  ],

  // ── Section 3: PostGrid — Dropdown filter + hover cards (2 col) ──
  [
    'id' => 'sec-grid2-001',
    'type' => 'section',
    'settings' => ['style' => 'default', 'width' => 'default', 'padding' => 'default'],
    'style' => ['bg' => ['type' => 'solid', 'color' => '#f8fafc']],
    'advanced' => [],
    'children' => [
      [
        'id' => 'row-grid2-001',
        'type' => 'row',
        'settings' => ['layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true],
        'style' => [],
        'advanced' => [],
        'children' => [
          [
            'id' => 'col-grid2-001',
            'type' => 'column',
            'settings' => ['width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => ''],
            'style' => [],
            'advanced' => [],
            'children' => [
              [
                'id' => 'el-headline-003',
                'type' => 'headline',
                'settings' => [
                  'heading' => 'By Zone',
                  'subtitle' => 'Dropdown filter, card hover effect, 2 columns, text link style',
                  'tag' => 'h2',
                  'alignment' => 'left',
                  'decoration' => 'none',
                  'heading_color' => '',
                  'subtitle_color' => '#6b7280',
                  'heading_size' => 'md',
                ],
                'style' => [],
                'advanced' => [],
              ],
              [
                'id' => 'el-postgrid-002',
                'type' => 'postgrid',
                'settings' => [
                  'post_type'       => 'location',
                  'posts_per_page'  => '12',
                  'orderby'         => 'title',
                  'order'           => 'ASC',
                  'meta_key'        => '',
                  'taxonomy'        => 'location_zone',
                  'show_filters'    => true,
                  'filter_style'    => 'dropdown',
                  'show_sort'       => false,
                  'sort_options'    => 'date|title',
                  'columns'         => '2',
                  'gap'             => 'large',
                  'card_style'      => 'hover',
                  'image_height'    => '250',
                  'show_image'      => true,
                  'show_category'   => true,
                  'show_excerpt'    => true,
                  'excerpt_length'  => '30',
                  'show_meta'       => false,
                  'show_price'      => true,
                  'price_field'     => 'rental_price_night',
                  'price_prefix'    => '€',
                  'price_suffix'    => '/night',
                  'link_text'       => 'Explore',
                  'link_style'      => 'text',
                ],
                'style' => [],
                'advanced' => [],
              ],
            ],
          ],
        ],
      ],
    ],
  ],

  // ── Section 4: PostGrid — Card clickable, primary, 4 col, sorted by price ──
  [
    'id' => 'sec-grid3-001',
    'type' => 'section',
    'settings' => ['style' => 'default', 'width' => 'default', 'padding' => 'default'],
    'style' => [],
    'advanced' => [],
    'children' => [
      [
        'id' => 'row-grid3-001',
        'type' => 'row',
        'settings' => ['layout' => '100', 'gap' => '16', 'column_gap' => 'default', 'vertical_align' => 'stretch', 'stack_mobile' => true],
        'style' => [],
        'advanced' => [],
        'children' => [
          [
            'id' => 'col-grid3-001',
            'type' => 'column',
            'settings' => ['width_default' => '', 'width_small' => '', 'width_medium' => '1-1', 'width_large' => ''],
            'style' => [],
            'advanced' => [],
            'children' => [
              [
                'id' => 'el-headline-004',
                'type' => 'headline',
                'settings' => [
                  'heading' => 'Quick Browse',
                  'subtitle' => 'Entire card clickable, primary style, 4 columns, sorted by price (no filters)',
                  'tag' => 'h2',
                  'alignment' => 'left',
                  'decoration' => 'none',
                  'heading_color' => '',
                  'subtitle_color' => '#6b7280',
                  'heading_size' => 'md',
                ],
                'style' => [],
                'advanced' => [],
              ],
              [
                'id' => 'el-postgrid-003',
                'type' => 'postgrid',
                'settings' => [
                  'post_type'       => 'location',
                  'posts_per_page'  => '8',
                  'orderby'         => 'meta_value_num',
                  'order'           => 'ASC',
                  'meta_key'        => 'rental_price_night',
                  'taxonomy'        => 'location_category',
                  'show_filters'    => false,
                  'filter_style'    => 'pills',
                  'show_sort'       => false,
                  'sort_options'    => 'date|title',
                  'columns'         => '4',
                  'gap'             => 'small',
                  'card_style'      => 'primary',
                  'image_height'    => '160',
                  'show_image'      => true,
                  'show_category'   => false,
                  'show_excerpt'    => false,
                  'excerpt_length'  => '20',
                  'show_meta'       => false,
                  'show_price'      => true,
                  'price_field'     => 'rental_price_night',
                  'price_prefix'    => '€',
                  'price_suffix'    => '/notte',
                  'link_text'       => 'View',
                  'link_style'      => 'card',
                ],
                'style' => [],
                'advanced' => [],
              ],
            ],
          ],
        ],
      ],
    ],
  ],
];

$id = $db->create_template([
  'title'    => 'Homepage',
  'type'     => 'page',
  'content'  => $content,
  'settings' => [],
  'status'   => 'published',
]);

echo "Template created with ID: $id\n";
