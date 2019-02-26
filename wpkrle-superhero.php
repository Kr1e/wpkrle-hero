<?php
/**
 * Plugin Name: Superheroes
 * Plugin URI:  https://github.com/Kr1e/wpkrle-hero.git
 * Description: Simple WordPress Plugin that creates Superhero post type for entering and classifying Superheroes
 * Version:     1.0.0
 * Author:      Ivan Krstic
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpkrle-superheroes
 * Domain Path: /languages
 */

//If called directly abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

//Creating custom post type
add_action('init', 'wpkrle_superheroes_custom_post_type');

function wpkrle_superheroes_custom_post_type()
{
    register_post_type(
    		'wpkrle_superhero',
           	array(
               'labels'      => array(
                   'name'          => __( 'Superheroes', 'wpkrle-superheroes' ),
                   'singular_name' => __( 'Superhero', 'wpkrle-superheroes' ),
                   'add_new_item'  => __( 'Add new superhero', 'wpkrle-superheroes' ),
                   'edit_item'	   => __( 'Edit superhero', 'wpkrle-superheroes' ),
                   'all_items'	   => __( 'All superheroes', 'wpkrle-superheroes' ),

               ),
               'public'      => true,
               'has_archive' => true,
               'supports'	 => array( 'title', 'editor', 'comments', 'thumbnail'),
               'taxonomies'  => array( '' ),
               'rewrite'     => array( 'slug' => 'superheroes' ), 
               'menu_icon'   => plugins_url( 'superhero.png', __FILE__ ),
               10
           )
    );
    register_taxonomy(
			'wpkrle-superhero_publisher',
			'wpkrle_superhero',
			array(
				'labels' => array(
				'name' => __( 'Publishers', 'wpkrle-superheroes' ),
				'singular_name' => __( 'Publisher', 'wpkrle-superheroes' ),
				'add_new_item' => __('Add New Publisher', 'wpkrle-superheroes'),
				'new_item_name' => __( 'New Publisher Name', 'wpkrle-superheroes' ),
				'edit_item'	   => __( 'Edit Publisher Name', 'wpkrle-superheroes' ),
			),
			'show_ui' => true,
			'show_tagcloud' => false,
			'hierarchical' => true
		)
	);
}


//Settings page for the plugin
add_action( 'admin_menu', 'wpkrle_superheroes_settings_page');

function wpkrle_superheroes_settings_page ()
{
	add_submenu_page(
		'edit.php?post_type=wpkrle_superhero',
		__( 'Settings', 'wpkrle-superheroes' ),
		__( 'Settings', 'wpkrle-superheroes' ),
		'manage_options',
		'superhero-settins',
		'wpkrle_superheroes_settings_page_html'
	);
}

function wpkrle_superheroes_settings_page_html()
{
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Settings. To be done ... ', 'wpkrle-superheroes' ); ?></p>
	</div>
	<?php
}


//Facts metabox and custom fields

add_action( 'admin_init', 'wpkrle_superhero_admin_init' );

function wpkrle_superhero_admin_init() {
	add_meta_box( 
		'wpkrle_superhero_facts_meta_box',
		__('Superhero Facts', 'wpkrle-superheroes' ),
		'wpkrle_superhero_display_facts_meta_box_html',
		'wpkrle_superhero', 
		'normal', 
		'high' 
	);
}

function wpkrle_superhero_display_facts_meta_box_html( $superhero_facts ) 
{
	// Retrieve facts based on superhero ID
	$superhero_full_name = esc_html( get_post_meta( $superhero_facts->ID, 'superhero_full_name', true ) );
	$superhero_creator = esc_html( get_post_meta( $superhero_facts->ID, 'superhero_creator', true ) );
	$superhero_first_appearance = esc_html( get_post_meta( $superhero_facts->ID, 'superhero_first_appearance', true ) );
	$superhero_rating = intval( get_post_meta( $superhero_facts->ID, 'superhero_rating', true ) );
?>

	<table>
		<tr>
			<td style="width: 100%"><?php _e( 'Superhero Full Name', 'wpkrle-superheroes' ) ?></td>
			<td><input type="text" size="80" name="superhero_full_name" value="<?php echo $superhero_full_name; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%"><?php _e( 'Superhero Creator', 'wpkrle-superheroes' ) ?></td>
			<td><input type="text" size="80" name="superhero_creator_name" value="<?php echo $superhero_creator; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 100%"><?php _e( 'First Appearance', 'wpkrle-superheroes' ) ?></td>
			<td><input type="text" size="80" name="superhero_first_appearance" value="<?php echo $superhero_first_appearance; ?>" /></td>
		</tr>
		<tr>
			<td style="width: 150px"><?php _e( 'Superhero Rating', 'wpkrle-superheroes' ) ?></td>
			<td>
			<select style="width: 100px" name="superhero_rating">
	
		<?php
			for ( $rating = 5; $rating >= 1; $rating -- ) { ?>
				<option value="<?php echo $rating; ?>"
				<?php echo selected( $rating, $superhero_rating ); ?>>
				<?php echo $rating . ' ' . __( 'stars', 'wpkrle-superheroes' ); ?>
		<?php } ?>
			</select>
		</td>
		</tr>
	</table>

<?php 
}

add_action( 'save_post', 'wpkrle_superhero_add_facts_fields', 10, 2 );

function wpkrle_superhero_add_facts_fields( $superhero_facts_id, $superhero_facts ) 
{	
	// Check post type for book reviews
	if ( 'wpkrle_superhero' == $superhero_facts->post_type ) {
		// Store data in post meta table if present in post data
		if ( isset( $_POST['superhero_full_name'] ) ) {
			update_post_meta( $superhero_facts_id, 'superhero_full_name', sanitize_text_field( $_POST['superhero_full_name'] ) );
		}
		if ( isset( $_POST['superhero_creator_name'] ) ) {
			update_post_meta( $superhero_facts_id, 'superhero_creator', sanitize_text_field( $_POST['superhero_creator_name'] ) );
		}
		if ( isset( $_POST['superhero_first_appearance'] ) && !empty( $_POST['superhero_first_appearance'] ) ) {
			update_post_meta( $superhero_facts_id, 'superhero_first_appearance', sanitize_text_field( $_POST['superhero_first_appearance'] ) );
		}
		if ( isset( $_POST['superhero_rating'] ) && !empty( $_POST['superhero_rating'] ) ) {
			update_post_meta( $superhero_facts_id, 'superhero_rating', intval( $_POST['superhero_rating'] ) );
		}
	}
}

//Front - adding filter to format content for displaying wpkrle_superhero post type
add_filter( 'template_include', 'wpkrle_superhero_include', 1 );

function wpkrle_superhero_include( $template_path ) {
	if ( 'wpkrle_superhero' == get_post_type() ) {
		if ( is_single() ) {
			// if the template exists in the theme use that file
			if ( $theme_file = locate_template( array( 'single-wpkrle_superhero.php' ) ) ) {
				$template_path = $theme_file;
			} else {
				add_filter( 'the_content', 'wpkrle_superhero_display_single_hero', 20 );
			}
		}
	}
	return $template_path;
}

function wpkrle_superhero_display_single_hero( $content ) {
	if ( !empty( get_the_ID() ) ) {
		// Display featured image thumbnail on the right
		$content = '<div style="float: right; margin: 10px">';
		$content .= get_the_post_thumbnail( get_the_ID(), 'thumbnail' );
		$content .= '</div>';
		$content .= '<div class="entry-content">';
		// Display Superhero Full Name
		$content .= '<strong>' . __('Full Name: ', 'wpkrle-superheroes' ) . '</strong>';
		$content .= esc_html( get_post_meta( get_the_ID(), 'superhero_full_name', true ) );
		$content .= '<br />';
		// Display Creator Name
		$content .= '<strong>' . __( 'Creator: ', 'wpkrle-superheroes' ) . '</strong>';
		$content .= esc_html( get_post_meta( get_the_ID(), 'superhero_creator', true ) );
		$content .= '<br />';
		// Display First Appearance
		$content .= '<strong>' . __( 'First Appearance: ', 'wpkrle-superheroes' ) . '</strong>';
		$content .= esc_html( get_post_meta( get_the_ID(), 'superhero_first_appearance', true ) );
		$content .= '<br />';
		// Display yellow stars based on rating -->
		$content .= '<strong>' . __( 'Rating: ', 'wpkrle-superheroes' ) . '</strong>';
		$nb_stars = intval( get_post_meta( get_the_ID(), 'superhero_rating', true ) );
		for ( $star_counter = 1; $star_counter <= 5; $star_counter++ ) {
			if ( $star_counter <= $nb_stars ) {
				$content .= '<img src="' . plugins_url( 'star-icon.png', __FILE__ ) . '"/>';
			} else {
				$content .= '<img src="' . plugins_url( 'star-icon-grey.png', __FILE__ ) . '" />';
			}
		}
		// Display superhero description
		$content .= '<br /><br />';
		$content .= get_the_content( get_the_ID() );
		$content .= '</div>';
		return $content;
	}
}


?>