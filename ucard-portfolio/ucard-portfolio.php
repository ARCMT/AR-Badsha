<?php
/*
Plugin Name: uCard Portfolio
Plugin URI:  http://www.pixelwars.org
Description: uCard portfolio functionality.
Version:     1.1.2
Author:      Pixelwars
Author URI:  http://www.pixelwars.org
License:     ThemeForest License
Text Domain: ucard-portfolio
Domain Path: /languages/
*/


/* ====================================================================================================================================================== */


	// don't load directly
	if (! defined('ABSPATH'))
	{
		die('-1');
	}


/* ====================================================================================================================================================== */


	function ucard_portfolio_load_plugin_textdomain()
	{
		load_plugin_textdomain('ucard-portfolio', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	
	add_action('init', 'ucard_portfolio_load_plugin_textdomain');


/* ============================================================================================================================================= */


	function pixelwars__create_post_type__portfolio()
	{
		register_post_type('portfolio' , array( 'label'         => esc_html__('Portfolio', 'ucard-portfolio'),
												'public' 		=> true,
												'menu_position' => 5,
												'supports'      => array('title', 'editor', 'thumbnail', 'excerpt', 'comments')));
	}
	
	add_action('init', 'pixelwars__create_post_type__portfolio');
	
	
	function pixelwars__portfolio_columns( $pf_columns )
	{
		$pf_columns = array('cb'                   => '<input type="checkbox">',
							'title'                => esc_html__('Title', 'ucard-portfolio'),
							'pf_featured_image'    => esc_html__('Featured Image', 'ucard-portfolio'),
							'portfolio-categories' => esc_html__('Portfolio Categories', 'ucard-portfolio'),
							'comments'             => '<span class="vers"><div title="Comments" class="comment-grey-bubble"></div></span>',
							'date'                 => esc_html__('Date', 'ucard-portfolio'));
		
		return $pf_columns;
	}
	
	add_filter('manage_edit-portfolio_columns', 'pixelwars__portfolio_columns');
	
	
	function pixelwars__custom_columns__portfolio($pf_column)
	{
		global $post, $post_ID;
		
		switch ($pf_column)
		{
			case 'pf_featured_image':
			
				if (has_post_thumbnail())
				{
					the_post_thumbnail('thumbnail');
				}
			
			break;
			
			case 'portfolio-categories':
			
				$taxonomy = 'portfolio-category';
				
				$terms_list = get_the_terms($post_ID, $taxonomy);
				
				if (! empty($terms_list))
				{
					$out = array();
					
					foreach ($terms_list as $term_list)
					{
						$out[] = '<a href="edit.php?post_type=portfolio&portfolio-category=' . esc_attr($term_list->slug) . '">' . esc_html($term_list->name) . ' </a>';
					}
					
					echo join(', ', $out);
				}
			
			break;
		}
	}
	
	add_action('manage_posts_custom_column',  'pixelwars__custom_columns__portfolio');
	
	
	function pixelwars__taxonomy__portfolio()
	{
		$labels_cat = array('name'              => esc_html__('Portfolio Categories', 'ucard-portfolio'),
							'singular_name'     => esc_html__('Portfolio Category', 'ucard-portfolio'),
							'search_items'      => esc_html__('Search', 'ucard-portfolio'),
							'all_items'         => esc_html__('All', 'ucard-portfolio'),
							'parent_item'       => esc_html__('Parent', 'ucard-portfolio'),
							'parent_item_colon' => esc_html__('Parent:', 'ucard-portfolio'),
							'edit_item'         => esc_html__('Edit', 'ucard-portfolio'),
							'update_item'       => esc_html__('Update', 'ucard-portfolio'),
							'add_new_item'      => esc_html__('Add New', 'ucard-portfolio'),
							'new_item_name'     => esc_html__('New Name', 'ucard-portfolio'),
							'menu_name'         => esc_html__('Portfolio Categories', 'ucard-portfolio'));
		
		register_taxonomy(  'portfolio-category',
							array('portfolio'),
							array(  'hierarchical' => true,
									'labels'       => $labels_cat,
									'show_ui'      => true,
									'public'       => true,
									'query_var'    => true,
									'rewrite'      => array('slug' => 'portfolio-category')));
	}
	
	add_action('init', 'pixelwars__taxonomy__portfolio');
	
	
	function pixelwars_taxonomy_filter_portfolio()
	{
		global $typenow;
		
		if ($typenow == 'portfolio')
		{
			$filters = array('portfolio-category');
			
			foreach ($filters as $tax_slug)
			{
				$tax_obj = get_taxonomy($tax_slug);
				$tax_name = $tax_obj->labels->name;
				$terms = get_terms($tax_slug);
				
				echo '<select name="' . esc_attr($tax_slug) . '" id="' . esc_attr($tax_slug) . '" class="postform">';
				
					echo '<option value="">' . esc_html__('All', 'ucard-portfolio') . ' ' . esc_html($tax_name) . '</option>';
					
					foreach ($terms as $term)
					{
						echo '<option value=' . $term->slug, @$_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . esc_html($term->name) .' (' . esc_html($term->count) . ')</option>';
					}
				
				echo '</select>';
			}
		}
	}
	
	add_action('restrict_manage_posts', 'pixelwars_taxonomy_filter_portfolio');
	
	
	function pixelwars_theme_custom_box_show_portfolio($post)
	{
		?>
			<?php
				wp_nonce_field('pixelwars_theme_custom_box_show_portfolio', 'pixelwars_theme_custom_box_nonce_portfolio');
			?>
			
			<p>
				<?php
					$pf_type = get_option($post->ID . 'pf_type', 'Standard');
				?>
				
				<label><input type="radio" name="pf_type" <?php if ($pf_type == 'Standard') { echo 'checked="checked"'; } ?> value="Standard"> <?php echo esc_html__('Standard', 'ucard-portfolio'); ?></label>
				
				<br>
				
				<label><input type="radio" name="pf_type" <?php if ($pf_type == 'Lightbox Gallery') { echo 'checked="checked"'; } ?> value="Lightbox Gallery"> <?php echo esc_html__('Lightbox Gallery', 'ucard-portfolio'); ?></label>
				
				<br>
				
				<label><input type="radio" name="pf_type" <?php if ($pf_type == 'Lightbox Audio') { echo 'checked="checked"'; } ?> value="Lightbox Audio"> <?php echo esc_html__('Lightbox Audio', 'ucard-portfolio'); ?></label>
				
				<br>
				
				<label><input type="radio" name="pf_type" <?php if ($pf_type == 'Lightbox Video') { echo 'checked="checked"'; } ?> value="Lightbox Video"> <?php echo esc_html__('Lightbox Video', 'ucard-portfolio'); ?></label>
				
				<br>
				
				<label><input type="radio" name="pf_type" <?php if ($pf_type == 'Direct URL') { echo 'checked="checked"'; } ?> value="Direct URL"> <?php echo esc_html__('Direct URL', 'ucard-portfolio'); ?></label>
			</p>
			
			<hr>
			
			<p>
				<?php
					$pf_direct_url = stripcslashes(get_option($post->ID . 'pf_direct_url'));
					$pf_link_new_tab = get_option($post->ID . 'pf_link_new_tab', true);
				?>
				
				<label for="pf_direct_url"><?php echo esc_html__('URL', 'ucard-portfolio'); ?></label>
				
				<input type="text" id="pf_direct_url" name="pf_direct_url" class="widefat code2" value="<?php echo esc_url($pf_direct_url); ?>">
				
				<label><input type="checkbox" name="pf_link_new_tab" <?php if ($pf_link_new_tab != false) { echo 'checked="checked"'; } ?>> <?php echo esc_html__('Open link in new tab', 'ucard-portfolio'); ?></label>
			</p>
		<?php
	}
	
	function pixelwars_theme_custom_box_add_portfolio()
	{
		add_meta_box('pixelwars_theme_custom_box_portfolio', esc_html__('Type', 'ucard-portfolio'), 'pixelwars_theme_custom_box_show_portfolio', 'portfolio', 'side', 'low');
	}
	
	add_action('add_meta_boxes', 'pixelwars_theme_custom_box_add_portfolio');
	
	
	function pixelwars_theme_custom_box_save_portfolio($post_id)
	{
		if (! isset($_POST['pixelwars_theme_custom_box_nonce_portfolio']))
		{
			return $post_id;
		}
		
		$nonce = $_POST['pixelwars_theme_custom_box_nonce_portfolio'];
		
		if (! wp_verify_nonce($nonce, 'pixelwars_theme_custom_box_show_portfolio'))
        {
			return $post_id;
		}
		
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        {
			return $post_id;
		}
		
		if ('page' == $_POST['post_type'])
		{
			if (! current_user_can('edit_page', $post_id))
			{
				return $post_id;
			}
		}
		else
		{
			if (! current_user_can('edit_post', $post_id))
			{
				return $post_id;
			}
		}
		
		update_option($post_id . 'pf_type', $_POST['pf_type']);
		update_option($post_id . 'pf_direct_url', $_POST['pf_direct_url']);
		update_option($post_id . 'pf_link_new_tab', $_POST['pf_link_new_tab']);
	}
	
	add_action('save_post', 'pixelwars_theme_custom_box_save_portfolio');

?>