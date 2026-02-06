<?php
/**
 * Provide a admin area view for the plugin
 *
 * @link       https://mahbubhasanhira.com/
 * @since      1.0.0
 *
 * @package    Headless_Redirector
 * @subpackage Headless_Redirector/admin/partials
 */
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    
    <style>
        .hr-card {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-left-width: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            margin: 20px 0;
            padding: 1px 12px;
        }
        .hr-card.danger { border-left-color: #d63638; padding-bottom: 10px; }
        .hr-info-box { background: #f0f6fc; border-left: 4px solid #72aee6; padding: 10px; margin-bottom: 15px; }
        .hr-table input[type="url"] { width: 100%; }
        .source-path { font-family: monospace; background: #eee; padding: 2px 5px; border-radius: 3px; color: #0073aa; }
    </style>

    <form method="post" action="options.php">
        <?php
            // check for active tab
            $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=headless-redirector&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
            <a href="?page=headless-redirector&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Advanced Options</a>
        </h2>
        
        <?php
        if( $active_tab == 'general' ) {
            settings_fields( 'hr_general_settings' );
            do_settings_sections( 'hr_general_settings' );
            ?>
            <div class="hr-card">
                <p>Configure where your WordPress traffic should go. These settings act as the global default for your headless setup.</p>
            </div>

            <!-- redirect options table -->
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Routing Behavior</th>
                    <td>
                        <fieldset>
                            <?php $strategy = get_option( 'hr_redirect_strategy', 'redirect' ); ?>
                            <label style="display: block; margin-bottom: 8px;">
                                <input type="radio" name="hr_redirect_strategy" value="redirect" <?php checked( 'redirect', $strategy ); ?> />
                                <strong>Redirect to Target URL</strong> <span class="description">(Standard)</span>
                            </label>
                            <label style="display: block;">
                                <input type="radio" name="hr_redirect_strategy" value="block" <?php checked( 'block', $strategy ); ?> />
                                <strong>Block Access (Headless Mode)</strong> <span class="description">(Returns HTTP 403 Forbidden)</span>
                            </label>
                            <p class="description" style="margin-top: 8px;">
                                - <strong>Redirect</strong>: Sends visitors to your Target URL.<br>
                                - <strong>Block</strong>: Shows an error message to visitors (useful for API-only sites).<br>
                                <em>Note: Admin, Login, and API paths are always accessible in both modes.</em>
                            </p>
                        </fieldset>
                    </td>
                </tr>
            <!-- redirect options table -->
            <div id="hr-redirect-options-wrapper">
            <table class="form-table">
                 <tr valign="top">
                    <th scope="row">Target URL</th>
                    <td>
                        <input type="url" name="hr_target_url" value="<?php echo esc_attr( get_option('hr_target_url') ); ?>" class="regular-text" placeholder="https://your-targeted-site.com" />
                        <p class="description">Enter the full URL of your targeted site.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Redirect</th>
                    <td>
                        <fieldset>
                            <label for="hr_enabled">
                                <input type="checkbox" id='hr_enabled' name="hr_enabled" value="1" <?php checked( 1, get_option( 'hr_enabled' ), true ); ?> />
                                <strong>Activate Redirection</strong>
                            </label>
                            <p class="description">Turn this on to start diverting you traffic to the target site.</p>
                        </fieldset>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Exclusion List</th>
                    <td>
                        <textarea name="hr_excluded_paths" rows="5" class="large-text code"><?php echo esc_textarea( get_option('hr_excluded_paths') ); ?></textarea>
                        <p class="description">
                            Add paths or keywords to exclude from redirection (one per line).<br>
                            <em>Note: <code>wp-admin</code>, <code>wp-login</code>, and <code>wp-json</code> are automatically excluded.</em>
                        </p>
                    </td>
                </tr>
            </table>
            </div>
            <?php
            submit_button();
        } else {
            settings_fields( 'hr_advanced_settings' );
            ?>
            
            <div class="hr-card danger">
                <h3>⚠️ Full Site Redirect</h3>
                <p><strong>Warning:</strong> Enabling this will force a redirect for <strong>ALL</strong> requests irrespective of exclusions (Safety limits for Admin & Login still apply).</p>
                <fieldset>
                    <label for="hr_full_redirect_mode">
                        <input type="checkbox" name="hr_full_redirect_mode" id="hr_full_redirect_mode" value="1" <?php checked( 1, get_option( 'hr_full_redirect_mode' ), true ); ?> />
                        Enable Full Site Redirect Mode
                    </label>
                </fieldset>
            </div>

            <hr>

            <h3>📍 URL Mapping</h3>
            <?php $target_url = get_option( 'hr_target_url' ); ?>
            <div class="hr-info-box">
                <p>Map specific local posts/pages to specific remote URLs.
                <?php if ( ! empty( $target_url ) ) : ?>
                    By default, unmapped content redirects to: <code><?php echo esc_url( $target_url ); ?></code>
                <?php else: ?>
                    <strong style="color: #d63638;">Please set a Target URL in General Settings first.</strong>
                <?php endif; ?>
                </p>
            </div>
            
            <table class="widefat fixed striped hr-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">Source Path</th>
                        <th style="width: 25%;">Post Title</th>
                        <th style="width: 55%;">Redirect To (Full URL)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch all public post types
                    $post_types = get_post_types( array( 'public' => true ), 'names' );
                    $args = array(
                        'post_type' => $post_types,
                        'posts_per_page' => 50, // Limit for V1
                        'post_status' => 'publish',
                    );
                    $query = new WP_Query( $args );
                    $mappings = get_option( 'hr_url_mappings', array() );
                    
                    if ( $query->have_posts() ) {
                        while ( $query->have_posts() ) {
                            $query->the_post();
                            $id = get_the_ID();
                            $target = isset( $mappings[$id] ) ? $mappings[$id] : '';
                            $permalink = get_permalink();
                            $path = wp_parse_url( $permalink, PHP_URL_PATH );
                            ?>
                            <tr>
                                <td><code class="source-path"><?php echo esc_html( $path ); ?></code></td>
                                <td>
                                    <strong><a href="<?php echo get_edit_post_link( $id ); ?>"><?php the_title(); ?></a></strong>
                                    <br>
                                    <small><?php echo ucfirst( get_post_type() ); ?></small> . 
                                    <a href="<?php the_permalink(); ?>" target="_blank" style="text-decoration: none;">🔗</a>
                                </td>
                                <td>
                                    <input type="url" name="hr_url_mappings[<?php echo $id; ?>]" value="<?php echo esc_url( $target ); ?>" placeholder="https://..." />
                                </td>
                            </tr>
                            <?php
                        }
                        wp_reset_postdata();
                    } else {
                        echo '<tr><td colspan="3">No public posts found.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
            <?php
            submit_button();
        }
    ?>
    </form>
    
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        var $urlInput = $('input[name="hr_target_url"]');
        var $enableCheckbox = $('input[name="hr_enabled"]');
        var $fullRedirectCheckbox = $('input[name="hr_full_redirect_mode"]');
        var $strategyRadios = $('input[name="hr_redirect_strategy"]');
        var $wrapper = $('#hr-redirect-options-wrapper');

        function validateUrlRequired() {
            var url = $urlInput.val();
            // In Redirect mode, URL is required to enable. In Block mode, Enable is forced off anyway.
            if( !url ) {
                $enableCheckbox.prop('disabled', true);
            } else {
                $enableCheckbox.prop('disabled', false);
            }
        }

        function handleStrategyChange() {
             var strategy = $strategyRadios.filter(':checked').val();
             if ( strategy === 'block' ) {
                 $wrapper.slideUp();
                 // Deselect "Enable Redirect" as requested
                 if( $enableCheckbox.is(':checked') ) {
                     $enableCheckbox.prop('checked', false);
                 }
                 // Disable it to prevent confusion
                 // $enableCheckbox.prop('disabled', true); 
             } else {
                 $wrapper.slideDown();
                 validateUrlRequired();
             }
        }

        // Init
        if( $strategyRadios.length ) {
            $strategyRadios.on('change', handleStrategyChange);
            handleStrategyChange(); // Run on load
        }

        if( $urlInput.length ) {
            $urlInput.on('input change', validateUrlRequired);
        }
    });
    </script>
</div>
