<?php  /**
 * Portfolio Widget
 *
 * This file is used to register and display the Hatch - Portfolio widget.
 *
 * @package Hatch
 * @since Hatch 1.0
 */
if( !class_exists( 'Hatch_Portfolio_Widget' ) ) {
	class Hatch_Portfolio_Widget extends Hatch_Widget {

		/**
		* Widget variables
		*
		* @param  	varchar    		$widget_title    	Widget title
		* @param  	varchar    		$widget_id    		Widget slug for use as an ID/classname
		* @param  	varchar    		$post_type    		(optional) Post type for use in widget options
		* @param  	varchar    		$taxonomy    		(optional) Taxonomy slug for use as an ID/classname
		* @param  	array 			$checkboxes    	(optional) Array of checkbox names to be saved in this widget. Don't forget these please!
		*/
		private $widget_title = 'Portfolio';
		private $widget_id = 'portfolio';
		private $post_type = 'jetpack-portfolio';
		private $taxonomy = 'jetpack-portfolio-type';
		public $checkboxes = array(
				'show_titles',
				'show_excerpts',
				'show_category_filter',
				'show_call_to_action'
			); // @TODO: Try make this more dynamic, or leave a different note reminding users to change this if they add/remove checkboxes
		/**
		*  Widget construction
		*/
		function Hatch_Portfolio_Widget(){
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'obox-hatch-' . $this->widget_id .'-widget', 'description' => 'This widget is used to display your ' . $this->widget_title . '.' );

			/* Widget control settings. */
			$control_ops = array( 'width' => HATCH_WIDGET_WIDTH_SMALL, 'height' => NULL, 'id_base' => HATCH_THEME_SLUG . '-widget-' . $this->widget_id );

			/* Create the widget. */
			$this->WP_Widget( HATCH_THEME_SLUG . '-widget-' . $this->widget_id , '(' . HATCH_THEME_TITLE . ') ' . $this->widget_title . ' Widget', $widget_ops, $control_ops );

			/* Setup Widget Defaults */
			$this->defaults = array (
				'title' => NULL,
				'excerpt' => NULL,
				'text_style' => 'regular',
				'category' => 0,
				'show_titles' => 'on',
				'show_excerpts' => 'on',
				'show_category_filter' => 'on',
				'excerpt_length' => 200,
				'show_call_to_action' => 'on',
				'call_to_action' => __( 'View Project' , HATCH_THEME_SLUG ),
                'posts_per_page' => 6,
                'order' => NULL,
				'design' => array(
					'layout' => 'layout-boxed',
					'imageratios' => 'square',
					'textalign' => 'text-left',
					'liststyle' => 'list-grid',
					'columns' => '3',
					'columflush' => false,
					'background' => array(
						'position' => 'center',
						'repeat' => 'no-repeat'
					),
					'fonts' => array(
						'align' => 'text-left',
						'size' => 'large',
						'color' => NULL,
						'shadow' => NULL
					)
				)
			);
		}

		/**
		*  Widget front end display
		*/
		function widget( $args, $instance ) {

			// Turn $args array into variables.
			extract( $args );

			// $instance Defaults
			$instance_defaults = $this->defaults;

			// If we have information in this widget, then ignore the defaults
			if( !empty( $instance ) ) $instance_defaults = array();

			// Parse $instance
			$instance = wp_parse_args( $instance, $instance_defaults );

			// Turn $instance into an object named $widget, makes for neater code
			$widget = (object) $instance;

			// Set the span class for each column
			if( 'list-list' == $widget->design[ 'liststyle' ] ) {
                $col_count = 1;
				$span_class = 'span-12';
			} else {
				$col_count = str_ireplace('columns-', '', $widget->design[ 'columns']  );
				$span_class = 'span-' . ( 12/ $col_count );
			}

			// Set the background & font styling
			if( !empty( $widget->design[ 'background' ] ) ) $this->widget_styles( $widget_id , 'background', $widget->design[ 'background' ] );
			if( !empty( $widget->design['fonts'][ 'color' ] ) ) $this->widget_styles( $widget_id , 'color', array( 'selectors' => array( '.section-title h3.heading' , '.section-title p.excerpt' ) , 'color' => $widget->design['fonts'][ 'color' ] ) );

			// Set Image Sizes
			if( isset( $widget->design[ 'imageratios' ] ) ){
				if( $col_count > 3 ){
					$imageratios = $widget->design[ 'imageratios' ] . '-medium';
				} else {
					$imageratios = $widget->design[ 'imageratios' ] . '-large';
				}
			} else {
				$imageratios = 'large';
			}

			// Begin query arguments
			$query_args = array();
			$query_args[ 'post_type' ] = $this->post_type;
			$query_args[ 'posts_per_page' ] = $widget->posts_per_page;
			if( isset( $widget->order ) ) {
				$decode_order = json_decode( $widget->order );
				foreach( $decode_order as $key => $value ){
					$query_args[ $key ] = $value;
				}
			}

			// Do the special taxonomy array()
			if( isset( $widget->category ) && '' != $widget->category && 0 != $widget->category ){
				$args['tax_query'] = array(
					array(
						"taxonomy" => $this->taxonomy,
						"field" => "id",
						"terms" => $widget->category
					)
				);
			} elseif( !isset( $widget->hide_category_filter ) ) {
				$terms = get_terms( $this->taxonomy );
			} // if we haven't selected which category to show, let's load the $terms for use in the filter


			// Do the WP_Query
			$portfolio_query = new WP_Query( $query_args ); ?>

			<section class="widget row content-vertical-massive" id="<?php echo $widget_id; ?>">
				<?php if( '' != $widget->title || '' != $widget->excerpt ) { ?>
					<div class="container clearfix">
						<div class="section-title <?php if( isset( $widget->design['fonts'][ 'size' ] ) ) echo $widget->design['fonts'][ 'size' ]; ?> <?php if( isset( $widget->design['fonts'][ 'align' ] ) ) echo $widget->design['fonts'][ 'align' ]; ?> clearfix">
							<?php if( '' != $widget->title ) { ?>
								<h3 class="heading"><?php echo $widget->title; ?></h3>
							<?php } ?>
							<?php if( '' != $widget->excerpt ) { ?>
								<p class="excerpt"><?php echo $widget->excerpt; ?></p>
							<?php } ?>
						</div>
<<<<<<< HEAD
				<?php if( isset( $widget->show_category_filter ) && !is_wp_error( $terms ) ) { ?>
					<nav class="nav nav-pills push-top <?php if( isset( $widget->design['fonts'][ 'align' ] ) ) echo $widget->design['fonts'][ 'align' ]; ?>">
						<ul>
							<?php foreach( $terms as $term ) { ?>
								<li data-filter="<?php echo $term->slug; ?>"><a href="#"><?php echo $term->name; ?></a></li>
							<?php } // foreach $terms ?>
						</ul>
					</nav>
=======
					</div>
>>>>>>> 9491627124e3bba283a3502f9208c80dd599cc26
				<?php } ?>
				<?php if( isset( $widget->show_category_filter ) && !is_wp_error( $terms ) ) { ?>
					<div class="container clearfix">
						<ul class="nav nav-pills push-top">
							<?php foreach( $terms as $term ) { ?>
								<li data-filter="<?php echo $term->slug; ?>"><a href="#"><?php echo $term->name; ?></a></li>
							<?php } // foreach $terms ?>
						</ul>
					</div>
				<?php } ?>
				<div class="row <?php if( isset( $widget->design[ 'layout' ] ) && 'layout-boxed' == $widget->design[ 'layout' ] ) echo 'container'; ?> <?php  if( isset( $widget->design[ 'liststyle' ] ) ) echo $widget->design[ 'liststyle' ]; ?>">
					<?php if( $portfolio_query->have_posts() ) { ?>
						<?php while( $portfolio_query->have_posts() ) {
							$portfolio_query->the_post();
							global $post; ?>
							<?php if( 'list-list' == $widget->design[ 'liststyle' ] ) { ?>
								<?php get_template_part( 'content' , 'list' ); ?>
							<?php } else { ?>
								<?php $terms = wp_get_post_terms( $post->ID, $this->taxonomy );
								$term_list = array();
								if( !empty( $terms ) ) {
									foreach( $terms as $term ){
										$term_list[] = $term->slug;
									}
								} ?>
								<article class="column<?php if( isset( $widget->design[ 'columnflush' ] ) ) echo '-flush'; ?> <?php echo $span_class; ?> hatch-masonry-column thumbnail <?php if( isset( $widget->text_style ) && 'overlay' == $widget->text_style ) echo 'with-overlay'; ?> <?php echo implode( $term_list, " " ); ?>"  data-cols="<?php echo $col_count; ?>">
									<?php if( has_post_thumbnail() ) { ?>
										<div class="thumbnail-media">
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( $imageratios );  ?>
											</a>
										</div>
									<?php } // if post thumbnail ?>
									<?php if( isset( $widget->show_titles ) || isset( $widget->show_excerpts ) ) { ?>
										<div class="thumbnail-body">
											<div class="overlay">
												<?php if( isset( $widget->show_titles ) ) { ?>
													<header class="article-title">
														<h4 class="heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
													</header>
												<?php } ?>
												<?php if( isset( $widget->show_excerpts ) ) {
													if( isset( $widget->excerpt_length ) && '' == $widget->excerpt_length ) {
														echo '<div class="excerpt">';
															the_content();
														echo '</div>';
                                                    } else if( isset( $widget->excerpt_length ) && 0 != $widget->excerpt_length && strlen( get_the_excerpt() ) > $widget->excerpt_length ){
                                                        echo '<p class="excerpt">' . substr( get_the_excerpt() , 0 , $widget->excerpt_length ) . '&#8230;</p>';
                                                    } else if( '' != get_the_excerpt() ){
                                                        echo '<p class="excerpt">' . get_the_excerpt() . '</p>';
                                                    }
                                                }; ?>
                                                <?php if( isset( $widget->show_call_to_action ) && isset( $widget->call_to_action ) && '' != $widget->call_to_action ) { ?>
													<a href="<?php the_permalink(); ?>" class="button"><?php echo $widget->call_to_action; ?></a>
												<?php } // show call to action ?>
											</div>
										</div>
									<?php } // if show titles || show excerpt ?>
								</article>
							<?php }; // if list-list == liststyle ?>
						<?php }; // while have_posts ?>
					<?php }; // if have_posts ?>
				</div>
			</section>

			<script>
				jQuery(function($){
					setTimeout(function(){
						var masonry = $('#<?php echo $widget_id; ?>').find('.list-masonry').masonry({
							'itemSelector': '.hatch-masonry-column'
							<?php if( !isset( $widget->design[ 'columnflush' ] ) ) echo ', "gutter": 20'; ?>
						});
					}, 500 );

					$('#<?php echo $widget_id; ?>').find('.nav-pills li').on( 'click' , function(e){
						e.preventDefault();

						// "Hi Mom"
						$that = $(this);

						// Get term slug
						$filter = $that.data( 'filter' );

						// Toggle active
						$that.toggleClass( 'active' ).siblings().removeClass('active');

						jQuery.each( $('#<?php echo $widget_id; ?>').find( '.hatch-masonry-column' ) , function(){
							if( jQuery(this).hasClass( $filter ) || !$that.hasClass( 'active' ) ){
								jQuery(this).fadeIn();
							} else {
								jQuery(this).hide();
							}
						});

						$('#<?php echo $widget_id; ?>').find('.list-masonry').masonry({
							'itemSelector': '.hatch-masonry-column'
							<?php if( !isset( $widget->design[ 'columnflush' ] ) ) echo ', "gutter": 20'; ?>
						});

						return false;
					});
				});
			</script>
			<!-- Front-end HTML Here
			<?php print_r( $instance ); ?>
			 -->

			<?php // Reset WP_Query
				wp_reset_postdata();
			?>
		<?php }

		/**
		*  Widget update
		*/

		function update($new_instance, $old_instance) {
			if ( isset( $this->checkboxes ) ) {
				foreach( $this->checkboxes as $cb ) {
					if( isset( $old_instance[ $cb ] ) ) {
						$old_instance[ $cb ] = strip_tags( $new_instance[ $cb ] );
					}
				} // foreach checkboxes
			} // if checkboxes
			return $new_instance;
		}

		/**
		*  Widget form
		*
		* We use regulage HTML here, it makes reading the widget much easier than if we used just php to echo all the HTML out.
		*
		*/
		function form( $instance ){

			// Initiate Widget Inputs
			$widget_elements = new Hatch_Form_Elements();

			// $instance Defaults
			$instance_defaults = $this->defaults;

			// If we have information in this widget, then ignore the defaults
			if( !empty( $instance ) ) $instance_defaults = array();

			// Parse $instance
			$instance = wp_parse_args( $instance, $instance_defaults );
			extract( $instance, EXTR_SKIP ); ?>

			<!-- Form HTML Here -->
			<?php $design_controller = new Hatch_Design_Controller();
			$design_controller->bar(
				'side', // CSS Class Name
				array(
					'name' => $this->get_field_name( 'design' ),
					'id' => $this->get_field_id( 'design' ),
				), // Widget Object
				$instance, // Widget Values
				array(
					'layout',
					'custom',
					'columns',
					'liststyle',
					'imageratios',
					'background',
					'fonts'
				), // Standard Components
				array(
					'display' => array(
						'icon-css' => 'icon-display',
						'label' => 'Display',
						'elements' => array(
								'text_style' => array(
									'type' => 'select',
									'name' => $this->get_field_name( 'text_style' ) ,
									'id' => $this->get_field_id( 'text_style' ) ,
									'value' => ( isset( $text_style ) ) ? $text_style : NULL,
									'label' => __( 'Title &amp; Excerpt Position' , HATCH_THEME_SLUG ),
									'options' => array(
											'regular' => __( 'Regular' , HATCH_THEME_SLUG ),
											'overlay' => __( 'Overlay' , HATCH_THEME_SLUG )
									)
								),
								'show_category_filter' => array(
									'type' => 'checkbox',
									'name' => $this->get_field_name( 'show_category_filter' ) ,
									'id' => $this->get_field_id( 'show_category_filter' ) ,
									'value' => ( isset( $show_category_filter ) ) ? $show_category_filter : NULL,
									'label' => __( 'Show Project Filter' , HATCH_THEME_SLUG )
								),
								'show_titles' => array(
									'type' => 'checkbox',
									'name' => $this->get_field_name( 'show_titles' ) ,
									'id' => $this->get_field_id( 'show_titles' ) ,
									'value' => ( isset( $show_titles ) ) ? $show_titles : NULL,
									'label' => __( 'Show  Project Titles' , HATCH_THEME_SLUG )
								),
								'show_excerpts' => array(
									'type' => 'checkbox',
									'name' => $this->get_field_name( 'show_excerpts' ) ,
									'id' => $this->get_field_id( 'show_excerpts' ) ,
									'value' => ( isset( $show_excerpts ) ) ? $show_excerpts : NULL,
									'label' => __( 'Show Project Excerpts' , HATCH_THEME_SLUG )
								),
                                'excerpt_length' => array(
                                    'type' => 'number',
                                    'name' => $this->get_field_name( 'excerpt_length' ) ,
                                    'id' => $this->get_field_id( 'excerpt_length' ) ,
                                    'min' => 0,
                                    'max' => 10000,
                                    'value' => ( isset( $excerpt_length ) ) ? $excerpt_length : NULL,
                                    'label' => __( 'Excerpts Length' , HATCH_THEME_SLUG )
                                ),
								'show_call_to_action' => array(
									'type' => 'checkbox',
									'name' => $this->get_field_name( 'show_call_to_action' ) ,
									'id' => $this->get_field_id( 'show_call_to_action' ) ,
									'value' => ( isset( $show_call_to_action ) ) ? $show_call_to_action : NULL,
									'label' => __( 'Show "Read More" Buttons' , HATCH_THEME_SLUG )
								),
                                'call_to_action' => array(
                                    'type' => 'text',
                                    'name' => $this->get_field_name( 'call_to_action' ) ,
                                    'id' => $this->get_field_id( 'call_to_action' ) ,
                                    'value' => ( isset( $call_to_action ) ) ? $call_to_action : NULL,
                                    'label' => __( '"Read More" Text' , HATCH_THEME_SLUG )
                                ),
							)
					)
				)
			); ?>
			<!-- Form HTML Here -->

			<div class="hatch-container-large">

				<?php $widget_elements->header( array(
					'title' =>  __( 'Portfolio' , HATCH_THEME_SLUG ),
					'icon_class' =>'portfolio'
				) ); ?>

				<section class="hatch-accordion-section hatch-content">

					<div class="hatch-row hatch-push-bottom">
						<p class="hatch-form-item">
							<?php echo $widget_elements->input(
								array(
									'type' => 'text',
									'name' => $this->get_field_name( 'title' ) ,
									'id' => $this->get_field_id( 'title' ) ,
									'placeholder' => __( 'Enter title here', HATCH_THEME_SLUG ),
									'value' => ( isset( $title ) ) ? $title : NULL ,
									'class' => 'hatch-text hatch-large'
								)
							); ?>
						</p>

						<p class="hatch-form-item">
							<?php echo $widget_elements->input(
								array(
									'type' => 'textarea',
									'name' => $this->get_field_name( 'excerpt' ) ,
									'id' => $this->get_field_id( 'excerpt' ) ,
									'placeholder' => __( 'Short Excerpt', HATCH_THEME_SLUG ),
									'value' => ( isset( $excerpt ) ) ? $excerpt : NULL ,
									'class' => 'hatch-textarea hatch-large'
								)
							); ?>
						</p>
						<?php // Grab the terms as an array and loop 'em to generate the $options for the input
						$terms = get_terms( $this->taxonomy );
						if( !is_wp_error( $terms ) ) { ?>
							<p class="hatch-form-item">
								<label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php echo __( 'Category to Display' , HATCH_THEME_SLUG ); ?></label>
								<?php $category_options[ 0 ] ="All";
								foreach ( $terms as $t ) $category_options[ $t->term_id ] = $t->name;
								echo $widget_elements->input(
									array(
										'type' => 'select',
										'name' => $this->get_field_name( 'category' ) ,
										'id' => $this->get_field_id( 'category' ) ,
										'placeholder' => __( 'Select a Category' , HATCH_THEME_SLUG ),
										'value' => ( isset( $category ) ) ? $category : NULL ,
										'options' => $category_options
									)
								); ?>
							</p>
						<?php } // if !is_wp_error ?>
						<p class="hatch-form-item">
							<label for="<?php echo $this->get_field_id( 'posts_per_page' ); ?>"><?php echo __( 'Number of items to show' , HATCH_THEME_SLUG ); ?></label>
							<?php $select_options[ '-1' ] = __( 'Show All' , HATCH_THEME_SLUG );
							$select_options = $widget_elements->get_incremental_options( $select_options , 1 , 20 , 1);
							echo $widget_elements->input(
								array(
									'type' => 'number',
									'name' => $this->get_field_name( 'posts_per_page' ) ,
									'id' => $this->get_field_id( 'posts_per_page' ) ,
									'value' => ( isset( $posts_per_page ) ) ? $posts_per_page : NULL ,
									'min' => '-1',
									'max' => '100'
								)
							); ?>
						</p>

						<p class="hatch-form-item">
							<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php echo __( 'Sort by' , HATCH_THEME_SLUG ); ?></label>
							<?php echo $widget_elements->input(
								array(
									'type' => 'select',
									'name' => $this->get_field_name( 'order' ) ,
									'id' => $this->get_field_id( 'order' ) ,
									'value' => ( isset( $order ) ) ? $order : NULL ,
									'options' => $widget_elements->get_default_sort_options()
								)
							); ?>
						</p>
					</div>
				</section>

			</div>
		<?php } // Form
	} // Class

	// Add our function to the widgets_init hook.
	 register_widget("Hatch_Portfolio_Widget");
}