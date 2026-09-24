// --- AUTOMATE LLMS.TXT & SITEMAP.RSS ON NEW POST PUBLISH ---
function automate_flypped_hindi_files($ID, $post) {
    // 1. Autosave ya revision par trigger na ho
    if (wp_is_post_autosave($ID) || wp_is_post_revision($ID)) {
        return;
    }

    // 2. Sirf 'post' type par chale
    if ( $post->post_type === 'post' ) {
        
        $urls = [
            'https://flyppedhindi.com/update_llms.php?key=flypped_secure_123',
            'https://flyppedhindi.com/sitemaprss.php?key=flypped_secure_123'
        ];

        // 3. Background update (blocking => false zaroori hai taaki dashboard hang na ho)
        foreach ($urls as $url) {
            wp_remote_get( $url, array(
                'timeout'   => 5,
                'blocking'  => false, 
                'sslverify' => false
            ));
        }
    }
}
// 'publish_post' best hook hai kyunki ye strictly tabhi chalta hai jab post live hoti hai
add_action('publish_post', 'automate_flypped_hindi_files', 10, 2);