<?php
/**
 * WP_GraphViz Shortcodes class.
 *
 * @package   WP_GraphViz
 * @author    De B.A.A.T. <WP_GraphViz@de-baat.nl>
 * @license   GPL-2.0+
 * @link      http://www.de-baat.nl/WP_Graphviz
 * @copyright 2014 - 2023 De B.A.A.T.
 */

class WP_GraphViz_Shortcodes {

	var $option_page, $page_title, $menu_title, $capability, $menu_slug, $version, $count;

	function __construct() {
		if (!defined('WP_GRAPHVIZ_SHORTCODES_VERSION')) {
			define('WP_GRAPHVIZ_SHORTCODES_VERSION', '1.0.0');
		}

		// Set some variables
		$this->page_title = 'WP GraphViz';
		$this->menu_title = __('WP GraphViz Shortcodes', 'wp-graphviz');
		$this->capability = 'edit_theme_options';
		$this->menu_slug = 'wp-graphviz-shortcodes';
		$this->version = WP_GRAPHVIZ_SHORTCODES_VERSION;
		$this->count = 1;

		add_action('admin_init', array(&$this, 'admin_init'));

		// Define the wp_graphviz_shortcodes, including version independent of upper or lower case
		$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
		foreach ($wp_graphviz_shortcodes as $shortcode) {
			$shortcode_lc = strtolower($shortcode['label']);
			$shortcode_uc = strtoupper($shortcode['label']);
			add_shortcode($shortcode['label'], array($this, $shortcode['function']));
			add_shortcode($shortcode_lc, array($this, $shortcode['function']));
			add_shortcode($shortcode_uc, array($this, $shortcode['function']));
		}

		// Add a menu entry to the WP_GraphViz plugin menu
        add_filter('add_wp_graphviz_menu_items',array($this,'add_menu_items'),90);

		// Add the filter to prevent WP_GraphViz shortcodes to be texturized
		add_filter('no_texturize_shortcodes', array($this, 'wpg_no_texturize_shortcodes'));

	}

    /**
     * Add the shortcode menu for this page
     *
     * @param mixed[] $menuItems
     * @return mixed[]
     */
    function add_menu_items($menuItems) {
        return array_merge(
                    $menuItems,
                    array(
                        array(
                            'label'     => $this->menu_title,
                            'slug'      => $this->menu_slug,
                            'class'     => $this,
                            'function'  => 'render_options'
                        ),
                    )
                );
    }

    /**
     * Get all shortcodes defined for WP GraphViz
     *
     * @return $shortcodes[]
     */
    function get_wp_graphviz_shortcodes() {
        return array (
					array(
						'label'        => 'WP_GraphViz',
						'description'  => __('The basic shortcode to render a graph specified in the DOT language.', 'wp-graphviz'),
						'class'        => $this,
						'function'     => 'wp_graphviz_shortcode',
						'no_texturize' => true,
						'parameters'   => '<ul>' .
											'<li>' .
											'<strong>type</strong>="[digraph|graph]"' .
											'<br/>' .
											__('Determines the type of graph being either directed', 'wp-graphviz') . ' (<code>digraph</code>) ' . __('or undirected', 'wp-graphviz') . ' (<code>graph</code>). ' .
											__('Default value is:', 'wp-graphviz') . ' <code>digraph</code>. ' .
											'</li>' .
// TODO: Graph not supported (yet?)
//											'<li>' .
//											'<strong>graph</strong>="&lt;slug&gt;"' .
//											'<br/>' .
//											__('Determines the location of the box, similar to the alignment of media.', 'wp-graphviz') . ' ' .
//											__('Valid values are:', 'wp-graphviz') . ' <code>none</code>, <code>left</code>, <code>center</code>, <code>right</code>. ' .
//											__('Default value is:', 'wp-graphviz') . ' <code>left</code>. ' .
//											'</li>' .
// TODO: Lang not supported (yet?)
//											'<li>' .
//											__('<strong>lang</strong>="&lt;lang&gt;"', 'wp-graphviz') .
//											'<br/>' .
//											__('Specifies the particular GraphViz interpreter to use.', 'wp-graphviz') . ' ' .
//											__('Valid values are:', 'wp-graphviz') . ' <code>dot</code>, <code>neato</code>. ' .
//											__('Default value is:', 'wp-graphviz') . ' <code>dot</code>. ' .
//											'</li>' .
											'<li>' .
											'<strong>simple</strong>="[true|false]"' .
											'<br/>' .
											__('The simple option provides a very basic DOT wrapper around your code such that the following is possible:', 'wp-graphviz') . ' ' .
											'<br/>' .
											' <code>[wp_graphviz simple=true] a -> b -> c; [/wp_graphviz]</code>. ' .
											'<br/>' .
											__('Default value is:', 'wp-graphviz') . ' <code>false</code>. ' .
											'</li>' .
// TODO: Output not supported (yet?)
//											'<li>' .
//											'<strong>output</strong>="&lt;output&gt;"' .
//											'<br/>' .
//											__('Determines the type of the image used to show the graph.', 'wp-graphviz') . ' ' .
//											__('Valid values are:', 'wp-graphviz') . ' <code>svg</code>, <code>png</code>. ' .
//											__('Default value is:', 'wp-graphviz') . ' <code>svg</code>. ' .
//											'</li>' .
											'<li>' .
											'<strong>size</strong>="&lt;width&gt;,&lt;heigth&gt;"' .
											'<br/>' .
											__('This attribute controls the size of the drawing; if the drawing is too large, it is scaled uniformly as necessary to fit.', 'wp-graphviz') . ' ' .
											__('The value should be a comma separated pair of width and heigth, specified in inches.', 'wp-graphviz') . ' ' .
											__('For example, size="7.5,10" fits on an 8.5x11 page.', 'wp-graphviz') . ' ' .
											__('Default value is empty, using the natural size of the image.', 'wp-graphviz') . ' ' .
											'</li>' .
											'<li>' .
											'<strong>title</strong>=&lt;'. __('Title text', 'wp-graphviz') . '&gt;' .
											'<br/>' .
											__('A text string that is used as title for this graph.', 'wp-graphviz') . ' ' .
											__('Default value is an empty string indicating no title.', 'wp-graphviz') . ' ' .
											'</li>' .
											'<li>' .
											'<strong>showdot</strong>="[true|false]"' .
											'<br/>' .
											__('Determines whether to show the (DOT) graph specification generated for the graph shown.', 'wp-graphviz') . ' ' .
											__('This option can be used to debug the graph generation.', 'wp-graphviz') . ' ' .
											__('Default value is:', 'wp-graphviz') . ' <code>false</code>. ' .
											'</li>' .
										 '</ul>',
					)
				);
    }

	function add_admin_scripts($hook) {
		if ($hook == $this->option_page) {
			if (is_admin()) {
				wp_enqueue_style('wp-graphviz-shortcodes-admin', plugins_url('css/admin.css', dirname(__FILE__)), array(), $this->version);
				wp_enqueue_style('wp-graphviz-shortcodes-admin-dosis', 'http://fonts.googleapis.com/css?family=Dosis', array(), $this->version);
			}
		}
	}

	function render_options() {

		$render_options_output = '';
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php echo __('Welcome to WP GraphViz Shortcodes', 'wp-graphviz'); ?></h2>
			<?php
			if (isset($_REQUEST['settings-updated'])) {
				?>
				<div id="sip-return-message" class="updated"><?php echo __('Your Settings have been saved.', 'wp-graphviz'); ?></div>
				<?php
			}
			?>
			<p>
				<?php echo __('This page shows the shortcodes provided by the WP GraphViz plugin:', 'wp-graphviz'); ?>
			</p>
			<div id='wp_graphviz_table_wrapper'>
			<table id='wp_graphviz_shortcodes_table' class='wp-graphviz wp-list-table widefat fixed posts' cellspacing="0">

			<thead>
				<tr class="wp_graphviz_shortcodes_row">
					<th class="wp_graphviz_shortcodes_cell" width="20%"><code>[SHORTCODE]</code>&nbsp;</th>
					<th class="wp_graphviz_shortcodes_cell" width="25%"><?php echo __('Description', 'wp-graphviz'); ?>&nbsp;</th>
					<th class="wp_graphviz_shortcodes_cell"><?php echo __('Parameters', 'wp-graphviz'); ?>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$row_style = 'even';
				$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
				foreach ($wp_graphviz_shortcodes as $shortcode) {
					$row_style = ($row_style == 'odd') ? 'even' : 'odd';
					$render_shortcode_output = '';
					$render_shortcode_output .= '<tr class="wp_graphviz_shortcodes_row ' . $row_style . '">';
					$render_shortcode_output .= '<td class="wp_graphviz_shortcodes_cell"><code>[' . $shortcode['label'] . ']</code></td>';
					$render_shortcode_output .= '<td class="wp_graphviz_shortcodes_cell">' . $shortcode['description'] . '</td>';
					$render_shortcode_output .= '<td class="wp_graphviz_shortcodes_cell">' . $shortcode['parameters'] . '</td>';
					$render_shortcode_output .= '</tr>';
					echo $render_shortcode_output;
				}
				?>
			</tbody>
			</table>

		</div>
		<p>
			<?php echo sprintf (__('For usage information see <a href="%s">here</a>.', 'wp-graphviz'), WP_GRAPHVIZ_LINK); ?>
		</p>

	</div>
	<?php
	}

	function add_scripts() {
		// No scripts needed
	}

	function admin_init() {
		// No admin_init needed
	}

	/**
	 * Adds our shortcodes that should not be texturized.
	 **/
	function wpg_no_texturize_shortcodes($shortcodes) {

		$wp_graphviz_shortcodes = $this->get_wp_graphviz_shortcodes();
		foreach ($wp_graphviz_shortcodes as $shortcode) {
			if ($shortcode['no_texturize']) {
				$shortcodes[] = $shortcode['label'];
				$shortcodes[] = strtolower($shortcode['label']);
				$shortcodes[] = strtoupper($shortcode['label']);
			}
		}
		return $shortcodes;
	}


	/**
	 * Generates an image specified in the DOT language. The short code takes the following arguments:
	 *  - image_caption: the caption of the image generated
	 *
	 * @param  $attr
	 * @return string
	 */
	function wp_graphviz_shortcode($attr, $content) {

		$wpg_shortcode_output = '';
		$wpg_graph_doc = '';
		$wpg_graph_spec = '';
		global $WP_GraphViz_Object;

		// Get the shortcode_attributes
		$wpg_atts = shortcode_atts(array(
			'id'      => 'wp_graphviz_'.($this->count++),
			'type'    => 'digraph',
			'graph'   => 'left',
			'lang'    => 'dot',
			'simple'  => false,
			'output'  => 'svg',
			'imap'    => false,
			'href'    => false,
			'size'    => '',
			'title'   => '',
			'showdot' => false,
		), $attr);


		$WP_GraphViz_Object->debugMP('pr','WP GraphViz wp_graphviz_dot_shortcode attributes',$attr,__FILE__,__LINE__);
		$WP_GraphViz_Object->debugMP('pr','WP GraphViz wp_graphviz_dot_shortcode attributes',$wpg_atts,__FILE__,__LINE__);
		$WP_GraphViz_Object->debugMP('msg','WP GraphViz wp_graphviz_dot_shortcode dot parameter',esc_html($content),__FILE__,__LINE__);

		// Set some variables
		$wpg_div_id = 'wpg_div_' . $wpg_atts['id'];
		$wpg_id = 'wpg_' . $wpg_atts['id'];

		// Get the dot specification of the graph
		$wpg_graph_dot = preg_replace(array('#<br\s*/?>#i', '#</?p>#i'), ' ', $content);

		$wpg_graph_dot = str_replace(
			array('&lt;', '&gt;', '&quot;', '&#8220;', '&#8221;', '&#8243;', '&#039;', '&#8125;', '&#8127;', '&#8217;', '&#038;', '&amp;', "\n", "\r", "\xa0", '&#8211;'),
			array('<',    '>',    '"',      '"',       '"',       '"',       "'",      "'",       "'",       "'",       '&',      '&',     '',   '',   '-',    ''),
			$wpg_graph_dot
		);

		// Check the type option, only allows graph and digraph
		if (strtolower($wpg_atts['type']) !== 'graph') {
			$wpg_atts['type'] = 'digraph';
		} else {
			// Replace digraph with graph
			$wpg_atts['type'] = 'graph';
			$wpg_graph_dot = preg_replace('/digraph/', 'graph', $wpg_graph_dot, 1);
		}
		// Build the dot specification of the graph when requested by the simple option
		if (wpg_string_to_bool($wpg_atts['simple'])) {
			$wpg_graph_dot = $wpg_atts['type'] . ' ' . $wpg_atts['id'] . ' { ' . $wpg_graph_dot . ' }';
		} 
		// Add the size option when provided and not already defined
		$wpg_graph_size = '';
		if (($wpg_atts['size'] !== '') &&(strpos($wpg_graph_dot, 'size') === false)) {
			$wpg_graph_size = ' size="' . $wpg_atts['size'] . '"; ';
			$wpg_graph_dot = preg_replace('/{/', '{' . $wpg_graph_size, $wpg_graph_dot, 1);
		}
		$WP_GraphViz_Object->debugMP('msg','WP GraphViz ++++++++++++++++++++++++++++++++> wpg_graph_dot 04 = ',$wpg_graph_dot);

		// TODO: Output not supported (yet?)
		// Build the format parameter from the parameters provided
		$wpg_format = '';
		if (strtolower($wpg_atts['output']) == 'png') {
			$wpg_format .= ' format: "png-image-element", ';
		} else {
			$wpg_format .= ' svg, ';
		}
		// TODO: Lang not supported (yet?)
		if (strtolower($wpg_atts['lang']) !== 'dot') {
			$wpg_format .= ' engine: "' . strtolower($wpg_atts['lang']) . '", ';
		}
		$wpg_format = '"svg"';
		//$wpg_format = '{ format: "png-image-element" }';
		//$wpg_format = '"json"';
		//$wpg_format = '{format:"SVG"}';
		$WP_GraphViz_Object->debugMP('msg','WP GraphViz wpg_format = ',$wpg_format);

		// Build the script to generate the graph
		$wpg_graph_spec .= '<script type="text/vnd.graphviz" id="' . $wpg_id . '">';
			$wpg_graph_spec .= $wpg_graph_dot;
		$wpg_graph_spec .= '</script>';

		// Build the script to generate the graph and replace the placeholder div with the graph itself
		$wpg_graph_doc .= '<script>';
			$wpg_graph_doc .= $wpg_div_id . '.innerHTML = createViz("' . $wpg_id . '", ' . $wpg_format . ');';
			$WP_GraphViz_Object->debugMP('msg','WP GraphViz wpg_graph_doc = ', esc_html($wpg_graph_doc));

			// Check value to showdot
			if (wpg_string_to_bool($wpg_atts['showdot'])) {
				$wpg_graph_doc .= $wpg_div_id . '.innerHTML += "<h4>' . __('DOT specification for graph', 'wp-graphviz') . ' ' . $wpg_atts['id'] . '.</h4>";';
				$wpg_graph_doc .= $wpg_div_id . '.innerHTML += "<blockquote>' . esc_html($wpg_graph_spec) . '</blockquote>";';
			}
		$wpg_graph_doc .= '</script>';

		// Build the placeholder and scripts to display the graph
		if ($wpg_atts['title']) {
			$wpg_shortcode_output .= '<h2>' . $wpg_atts['title'] . '</h2>';
		}
		$wpg_shortcode_output .= '<div id=' . $wpg_div_id . '>' . $wpg_div_id . '</div>';
		$wpg_shortcode_output .= $wpg_graph_spec;
		$wpg_shortcode_output .= $wpg_graph_doc;

		//$wpg_shortcode_output = 'TESTING wp_graphviz_dot_shortcode<br/>';

		$WP_GraphViz_Object->debugMP('msg','WP GraphViz wp_graphviz_shortcode shortcode_output', esc_html($wpg_shortcode_output),__FILE__,__LINE__);

		return $wpg_shortcode_output;
	}

}

add_action('init', 'init_wp_graphviz_shortcodes');
function init_wp_graphviz_shortcodes() {
	global $WP_GraphViz_Shortcodes;
	$WP_GraphViz_Shortcodes = new WP_GraphViz_Shortcodes();
}
