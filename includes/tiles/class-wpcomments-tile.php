<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Olo_Wpcomments_Tile extends Olo_Tile_Base {

    protected $type     = 'wpcomments';
    protected $name     = 'Commenti';
    protected $icon     = 'dashicons-admin-comments';
    protected $category = 'dynamic';
    protected $defaults = [
        'show_title'          => true,
        'title_text'          => 'Commenti',
        'title_tag'           => 'h3',
        'show_avatar'         => true,
        'avatar_size'         => '48',
        'show_date'           => true,
        'show_reply_link'     => true,
        'show_form'           => true,
        'comments_per_page'   => '10',
        'order'               => 'desc',
        'title_color'         => '',
        'text_color'          => '',
        'author_color'        => '',
        'date_color'          => '',
        'link_color'          => '',
        'form_background'     => '',
        'border_color'        => '#e5e7eb',
        'avatar_border_radius' => '50',
    ];

    public function get_controls() {
        return [
            [ 'key' => 'show_title',      'type' => 'toggle',   'label' => 'Mostra titolo' ],
            [ 'key' => 'title_text',       'type' => 'text',     'label' => 'Testo titolo' ],
            [ 'key' => 'title_tag',        'type' => 'select',   'label' => 'Tag titolo' ],
            [ 'key' => 'show_avatar',      'type' => 'toggle',   'label' => 'Mostra avatar' ],
            [ 'key' => 'avatar_size',      'type' => 'range',    'label' => 'Dimensione avatar' ],
            [ 'key' => 'show_date',        'type' => 'toggle',   'label' => 'Mostra data' ],
            [ 'key' => 'show_reply_link',  'type' => 'toggle',   'label' => 'Link rispondi' ],
            [ 'key' => 'show_form',        'type' => 'toggle',   'label' => 'Mostra modulo' ],
            [ 'key' => 'comments_per_page','type' => 'range',    'label' => 'Commenti per pagina' ],
            [ 'key' => 'order',            'type' => 'select',   'label' => 'Ordine' ],
        ];
    }

    public function render( $settings ) {
        $s = wp_parse_args( $settings, $this->defaults );

        global $post;

        // Se non siamo su un post singolo o i commenti sono chiusi, non mostrare nulla
        if ( ! $post ) {
            return '';
        }

        if ( ! comments_open( $post->ID ) ) {
            if ( get_comments_number( $post->ID ) === 0 ) {
                return '';
            }
        }

        $uid = 'olo-wpc-' . wp_rand( 10000, 99999 );

        // Sanitize settings
        $allowed_tags = [ 'h2', 'h3', 'h4', 'h5' ];
        $title_tag    = in_array( $s['title_tag'], $allowed_tags, true ) ? $s['title_tag'] : 'h3';
        $title_text   = esc_html( $s['title_text'] ?: olo_t( 'Commenti' ) );
        $show_title   = ! empty( $s['show_title'] );
        $show_avatar  = ! empty( $s['show_avatar'] );
        $avatar_size  = max( 24, min( 96, absint( $s['avatar_size'] ) ) );
        $avatar_radius = max( 0, min( 50, Olo_Tile_Utils::radius_int( $s['avatar_border_radius'] ) ) );
        $show_date    = ! empty( $s['show_date'] );
        $show_reply   = ! empty( $s['show_reply_link'] );
        $show_form    = ! empty( $s['show_form'] );
        $per_page     = max( 1, min( 100, absint( $s['comments_per_page'] ) ) );
        $order        = $s['order'] === 'asc' ? 'asc' : 'desc';

        // Colors
        $title_color  = $this->safe_color_css( $s['title_color'] );
        $text_color   = $this->safe_color_css( $s['text_color'] );
        $author_color = $this->safe_color_css( $s['author_color'] );
        $date_color   = $this->safe_color_css( $s['date_color'] );
        $link_color   = $this->safe_color_css( $s['link_color'] );
        $form_bg      = $this->safe_color_css( $s['form_background'] );
        $border_color = $this->safe_color_css( $s['border_color'] ?: '#e5e7eb' );

        // Get comments
        $comments = get_comments( [
            'post_id' => $post->ID,
            'status'  => 'approve',
            'order'   => $order,
            'number'  => $per_page,
        ] );

        $count = get_comments_number( $post->ID );

        // Store tile settings for the callback
        $this->_render_ctx = compact(
            'show_avatar', 'avatar_size', 'avatar_radius', 'show_date',
            'show_reply', 'text_color', 'author_color', 'date_color',
            'link_color', 'border_color', 'uid'
        );

        ob_start();
        ?>
        <style>
            .<?php echo $uid; ?> .olo-comment {
                padding: 16px 0;
                border-bottom: 1px solid <?php echo $border_color; ?>;
            }
            .<?php echo $uid; ?> .olo-comment:last-child {
                border-bottom: none;
            }
            .<?php echo $uid; ?> .olo-comment-body {
                display: flex;
                gap: 12px;
            }
            .<?php echo $uid; ?> .olo-comment-avatar img {
                border-radius: <?php echo $avatar_radius; ?>%;
                display: block;
            }
            .<?php echo $uid; ?> .olo-comment-meta {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 4px;
            }
            .<?php echo $uid; ?> .olo-comment-author {
                font-weight: 600;
                font-size: 0.9em;
                <?php if ( $author_color ) : ?>color: <?php echo $author_color; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-comment-date {
                font-size: 0.8em;
                <?php if ( $date_color ) : ?>color: <?php echo $date_color; ?>;<?php else : ?>color: #888;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-comment-content {
                font-size: 0.9em;
                line-height: 1.6;
                <?php if ( $text_color ) : ?>color: <?php echo $text_color; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-comment-content p {
                margin: 0 0 8px 0;
            }
            .<?php echo $uid; ?> .olo-comment-content p:last-child {
                margin-bottom: 0;
            }
            .<?php echo $uid; ?> .olo-comment-reply a {
                font-size: 0.8em;
                text-decoration: none;
                <?php if ( $link_color ) : ?>color: <?php echo $link_color; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-comment-reply a:hover {
                text-decoration: underline;
            }
            .<?php echo $uid; ?> .children {
                margin-left: 40px;
                list-style: none;
                padding: 0;
            }
            .<?php echo $uid; ?> .olo-comments-list {
                list-style: none;
                padding: 0;
                margin: 0;
            }
            <?php if ( $show_form ) : ?>
            .<?php echo $uid; ?> .olo-comment-form-wrap {
                margin-top: 24px;
                padding: 16px;
                border-radius: 6px;
                <?php if ( $form_bg ) : ?>background: <?php echo $form_bg; ?>;<?php endif; ?>
                <?php if ( $border_color ) : ?>border: 1px solid <?php echo $border_color; ?>;<?php endif; ?>
            }
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form label {
                display: block;
                font-size: 0.85em;
                margin-bottom: 4px;
                font-weight: 500;
            }
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form input[type="text"],
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form input[type="email"],
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form input[type="url"],
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form textarea {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid <?php echo $border_color; ?>;
                border-radius: 4px;
                font-size: 0.9em;
                box-sizing: border-box;
                margin-bottom: 12px;
            }
            .<?php echo $uid; ?> .olo-comment-form-wrap .comment-form .form-submit input {
                padding: 8px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 0.9em;
                font-weight: 500;
                <?php if ( $link_color ) : ?>background: <?php echo $link_color; ?>; color: #fff;<?php else : ?>background: #1e87f0; color: #fff;<?php endif; ?>
            }
            <?php endif; ?>
        </style>

        <div class="olo-wpcomments <?php echo $uid; ?>">
        <?php
        // Title
        if ( $show_title ) {
            $title_style = $title_color ? ' style="color:' . $title_color . '"' : '';
            echo '<' . $title_tag . ' class="olo-comments-title"' . $title_style . '>';
            echo $title_text . ' (' . absint( $count ) . ')';
            echo '</' . $title_tag . '>';
        }

        // Comment list
        if ( ! empty( $comments ) ) {
            echo '<ol class="olo-comments-list">';
            wp_list_comments( [
                'callback'    => [ $this, 'comment_callback' ],
                'end-callback' => [ $this, 'comment_end_callback' ],
                'style'       => 'ol',
                'max_depth'   => 5,
                'per_page'    => $per_page,
                'reverse_top_level' => ( $order === 'desc' ),
            ], $comments );
            echo '</ol>';
        } else {
            echo '<p style="font-size:0.9em;opacity:0.7">' . esc_html( olo_t( 'Nessun commento ancora.' ) ) . '</p>';
        }

        // Comment form
        if ( $show_form ) {
            if ( comments_open( $post->ID ) ) {
                echo '<div class="olo-comment-form-wrap">';
                comment_form( [
                    'title_reply'         => olo_t( 'Lascia un commento' ),
                    'title_reply_to'      => olo_t( 'Rispondi a %s' ),
                    'cancel_reply_link'   => olo_t( 'Annulla risposta' ),
                    'label_submit'        => olo_t( 'Invia commento' ),
                    'comment_notes_before' => '',
                    'comment_notes_after'  => '',
                ], $post->ID );
                echo '</div>';
            }
        }
        ?>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Custom comment callback for wp_list_comments
     */
    public function comment_callback( $comment, $args, $depth ) {
        $ctx = $this->_render_ctx;

        $tag = ( $args['style'] === 'div' ) ? 'div' : 'li';
        ?>
        <<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'olo-comment' ); ?>>
            <div class="olo-comment-body">
                <?php if ( $ctx['show_avatar'] ) : ?>
                <div class="olo-comment-avatar">
                    <?php echo get_avatar( $comment, $ctx['avatar_size'] ); ?>
                </div>
                <?php endif; ?>
                <div class="olo-comment-content-wrap" style="flex:1;min-width:0">
                    <div class="olo-comment-meta">
                        <span class="olo-comment-author"><?php echo esc_html( get_comment_author( $comment ) ); ?></span>
                        <?php if ( $ctx['show_date'] ) : ?>
                        <span class="olo-comment-date">
                            <time datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>">
                                <?php echo esc_html( get_comment_date( '', $comment ) ); ?>
                            </time>
                        </span>
                        <?php endif; ?>
                    </div>

                    <div class="olo-comment-content">
                        <?php
                        if ( '0' === (string) $comment->comment_approved ) {
                            echo '<em>' . esc_html( olo_t( 'Il commento è in attesa di moderazione.' ) ) . '</em>';
                        } else {
                            comment_text( $comment );
                        }
                        ?>
                    </div>

                    <?php if ( $ctx['show_reply'] ) : ?>
                    <div class="olo-comment-reply">
                        <?php
                        comment_reply_link( array_merge( $args, [
                            'reply_text' => olo_t( 'Rispondi' ),
                            'depth'      => $depth,
                            'max_depth'  => $args['max_depth'],
                        ] ), $comment );
                        ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
    }

    /**
     * End callback for wp_list_comments
     */
    public function comment_end_callback( $comment, $args, $depth ) {
        $tag = ( $args['style'] === 'div' ) ? 'div' : 'li';
        echo '</' . $tag . '>';
    }
}
