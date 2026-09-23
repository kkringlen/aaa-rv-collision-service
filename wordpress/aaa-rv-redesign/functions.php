<?php
/** AAA RV theme functions. Compatible with PHP 7.4 and later. */
if (!defined('ABSPATH')) { exit; }

function aaa_rv_content() {
    static $data = null;
    if ($data === null) {
        $data = json_decode(file_get_contents(get_template_directory() . '/content.json'), true);
        if (!is_array($data)) { $data = array(); }
    }
    return $data;
}

function aaa_rv_key() {
    if (is_front_page()) { return 'home'; }
    if (is_404()) { return '404'; }
    if (is_page()) {
        $slug = get_post_field('post_name', get_queried_object_id());
        $data = aaa_rv_content();
        if (isset($data[$slug])) { return $slug; }
    }
    return '';
}

function aaa_rv_resolve($html) {
    return str_replace(array('{{AAA_THEME}}', '{{AAA_HOME}}'),
        array(esc_url(get_template_directory_uri()), esc_url(trailingslashit(home_url('/')))), $html);
}

function aaa_rv_part($name) {
    if (!in_array($name, array('header','footer','policy'), true)) { return; }
    $html = aaa_rv_resolve(file_get_contents(get_template_directory() . '/parts/' . $name . '.html'));
    if ($name === 'header') {
        $key = aaa_rv_key();
        if (in_array($key, array('claim-help','gallery','about','contact'), true)) {
            $needle = '<a href="' . esc_url(home_url('/' . $key . '/')) . '">';
            $html = str_replace($needle, '<a aria-current="page" href="' . esc_url(home_url('/' . $key . '/')) . '">', $html);
        }
    }
    // These fragments are authored theme files, not visitor-supplied HTML.
    echo $html;
}

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('html5', array('search-form','gallery','caption','style','script'));
    add_theme_support('post-thumbnails');
});

add_action('wp_enqueue_scripts', function () {
    $uri = get_template_directory_uri();
    wp_enqueue_style('aaa-rv-fonts', $uri . '/assets/fonts.css', array(), '1.0.0');
    wp_enqueue_style('aaa-rv-design', $uri . '/styles.css', array('aaa-rv-fonts'), '1.0.0');
    wp_enqueue_script('aaa-rv-site', $uri . '/site.js', array(), '1.0.0', true);
});

add_shortcode('aaa_rv_work_request', function () {
    return '<iframe id="shopmonkey-work-request" title="AAA RV work order request form" src="https://app.shopmonkey.cloud/public/quote-request/60ee077ed870723088e7a9a2" width="100%" height="700" frameborder="0" loading="lazy"></iframe>';
});

function aaa_rv_seo_value($field, $fallback) {
    $data = aaa_rv_content(); $key = aaa_rv_key();
    return isset($data[$key][$field]) ? $data[$key][$field] : $fallback;
}
add_filter('pre_get_document_title', function ($title) { return aaa_rv_seo_value('seo_title', $title); });
add_filter('wpseo_title', function ($title) { return aaa_rv_seo_value('seo_title', $title); });
add_filter('wpseo_metadesc', function ($description) { return aaa_rv_seo_value('description', $description); });
add_filter('wpseo_opengraph_title', function ($title) { return aaa_rv_seo_value('seo_title', $title); });
add_filter('wpseo_opengraph_desc', function ($description) { return aaa_rv_seo_value('description', $description); });
add_filter('wpseo_twitter_title', function ($title) { return aaa_rv_seo_value('seo_title', $title); });
add_filter('wpseo_twitter_description', function ($description) { return aaa_rv_seo_value('description', $description); });

add_action('wp_head', function () {
    echo '<meta name="theme-color" content="#121313">' . "\n";
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url(get_template_directory_uri() . '/favicon.svg') . '">' . "\n";
    if (is_front_page()) {
        echo '<link rel="preload" as="image" href="' . esc_url(get_template_directory_uri() . '/assets/homepage-header.webp') . '" fetchpriority="high">' . "\n";
    }
    if (!defined('WPSEO_VERSION')) {
        $description = aaa_rv_seo_value('description', get_bloginfo('description'));
        echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    }
    if (is_front_page()) {
        $schema = array('@context'=>'https://schema.org','@type'=>'AutomotiveBusiness',
            '@id'=>home_url('/#business'),'name'=>'AAA RV Collision & Service Center',
            'url'=>home_url('/'),'telephone'=>'+1-405-634-1429',
            'address'=>array('@type'=>'PostalAddress','streetAddress'=>'10519 S. Sunnylane',
                'addressLocality'=>'Oklahoma City','addressRegion'=>'OK','postalCode'=>'73160','addressCountry'=>'US'),
            'openingHoursSpecification'=>array(array('@type'=>'OpeningHoursSpecification',
                'dayOfWeek'=>array('Monday','Tuesday','Wednesday','Thursday','Friday'),'opens'=>'08:00','closes'=>'17:00')));
        echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_HEX_TAG | JSON_HEX_AMP) . '</script>' . "\n";
    }
}, 5);

/** Import once on activation; retain original content, revisions and a restore record. */
function aaa_rv_activate() {
    if (get_option('aaa_rv_redesign_import_v1')) { return; }
    if (!current_user_can('switch_themes') || !current_user_can('unfiltered_html')) { return; }
    $data = aaa_rv_content();
    if (count($data) !== 16) {
        update_option('aaa_rv_redesign_import_error', 'The bundled page content could not be validated.', false);
        return;
    }
    if (!get_option('aaa_rv_redesign_previous_options')) {
        add_option('aaa_rv_redesign_previous_options', array(
            'show_on_front'=>get_option('show_on_front'), 'page_on_front'=>get_option('page_on_front'),
            'blog_public'=>get_option('blog_public'), 'created_at'=>current_time('mysql')), '', false);
    }
    $page_ids = array();
    foreach ($data as $key => $item) {
        if ((string) $key === '404') { continue; }
        $existing = $key === 'home' ? get_post((int)get_option('page_on_front')) : get_page_by_path($key, OBJECT, 'page');
        if ($existing && $existing->post_type !== 'page') { $existing = null; }
        if ($existing && !get_post_meta($existing->ID, '_aaa_rv_legacy_snapshot', true)) {
            add_post_meta($existing->ID, '_aaa_rv_legacy_snapshot', array(
                'post_content'=>$existing->post_content, 'post_title'=>$existing->post_title,
                'post_excerpt'=>$existing->post_excerpt, 'post_status'=>$existing->post_status,
                'page_template'=>get_post_meta($existing->ID, '_wp_page_template', true),
                'yoast_title'=>get_post_meta($existing->ID, '_yoast_wpseo_title', true),
                'yoast_description'=>get_post_meta($existing->ID, '_yoast_wpseo_metadesc', true),
                'created_at'=>current_time('mysql')), true);
            wp_save_post_revision($existing->ID);
        }
        $post = array('post_type'=>'page','post_status'=>'publish','post_title'=>$item['title'],
            'post_content'=>'<!-- wp:html -->' . aaa_rv_resolve($item['content']) . '<!-- /wp:html -->',
            'post_excerpt'=>$item['description'],'comment_status'=>'closed','ping_status'=>'closed');
        if ($existing) { $post['ID'] = $existing->ID; }
        else { $post['post_name'] = $key === 'home' ? 'home' : $key; }
        $id = wp_insert_post(wp_slash($post), true);
        if (is_wp_error($id)) {
            update_option('aaa_rv_redesign_import_error', $id->get_error_message(), false);
            return;
        }
        update_post_meta($id, '_aaa_rv_redesigned', '1');
        update_post_meta($id, '_wp_page_template', 'default');
        update_post_meta($id, '_yoast_wpseo_title', $item['seo_title']);
        update_post_meta($id, '_yoast_wpseo_metadesc', $item['description']);
        $page_ids[$key] = $id;
    }
    update_option('show_on_front', 'page');
    update_option('page_on_front', $page_ids['home']);
    update_option('aaa_rv_redesign_page_ids', $page_ids, false);
    update_option('aaa_rv_redesign_import_v1', current_time('mysql'), false);
    delete_option('aaa_rv_redesign_import_error');
    flush_rewrite_rules(false);
}
add_action('after_switch_theme', 'aaa_rv_activate');
add_action('admin_init', 'aaa_rv_activate');

add_action('admin_notices', function () {
    $error = get_option('aaa_rv_redesign_import_error');
    if ($error && current_user_can('switch_themes')) {
        echo '<div class="notice notice-error"><p>AAA RV page setup needs attention: ' . esc_html($error) . '</p></div>';
    }
});

function aaa_rv_page_body() {
    $data = aaa_rv_content(); $key = aaa_rv_key();
    if (isset($data[$key])) {
        if ($key !== '404' && get_post_meta(get_queried_object_id(), '_aaa_rv_redesigned', true) === '1') {
            while (have_posts()) { the_post(); the_content(); }
        } else {
            // Live Preview can display the design before any WordPress content is changed.
            echo do_shortcode(aaa_rv_resolve($data[$key]['content']));
        }
        return;
    }
    echo '<section class="section"><div class="container narrow">';
    while (have_posts()) {
        the_post();
        echo '<h1>' . esc_html(get_the_title()) . '</h1>';
        the_content();
    }
    echo '</div></section>';
}
