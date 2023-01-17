<?php 
/**
 * Idea Space functions and definitions.
 *
 */

function print2($text, $die = 0) {
  print '<pre>';
  print_r($text);
  print '</pre>';
  if($die) exit;
}

# Enqueue Style
add_action( 'wp_enqueue_scripts', 'ss_enqueue_styles', 20);
function ss_enqueue_styles() {
    //echo 'i m here - '.get_stylesheet_directory_uri();
    wp_enqueue_style( 'ideaspace-main-style', get_stylesheet_directory_uri() . '/style.css','','1.1', true);
}

# Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'ss_enqueue_scripts', 10 );
function ss_enqueue_scripts() {
  global $post;

  $base = get_stylesheet_directory_uri();
  $home = get_site_url();
  
  //echo 'i m here -'.$base.'/'.get_stylesheet_uri();
  //wp_enqueue_style( 'ss-style', get_stylesheet_uri() );
  //wp_enqueue_script( 'ss-main', $base.'/js/ss-main.js', array( 'jquery' ), 1, true );
}

#User registration form
add_shortcode('SS_REGISTRATION_FORM', 'ss_user_registration_form');
function ss_user_registration_form($atts){
	
	$str = '<form action="'.get_option('home').'/custom-user-registration-form/" method="post">
		<div>First Name <input type="text" name="first_name" id="first_name" /></div>
		<div>Last Name <input type="text" name="last_name" id="last_name" /></div>
		<div>Email <input type="email" name="email" id="email" /></div>
		<input type="submit" name="submit" value="SignUp" />
	';
	$str .= '</form>';
	
	return $str;
	
}


# Homepage Slider
add_shortcode( 'SS_HOMEPAGE_ALL_SLIDES', 'ss_homepage_all_slides' );
function ss_homepage_all_slides($atts) {
  global $post;
  
  $limit = ($atts) ? $atts['limit']:5;
  $args = array(
		'numberposts'      => $limit,
		'category'         => 0,
		'orderby'          => 'date',
		'order'            => 'ASC',
		'post_type'        => 'slide',
		'suppress_filters' => true,
	);
  $slides = get_posts($args);
  //print2($slides);
  if($slides) {
      $cnt = 1;
      $count_li = 1;
      $total_count = count($slides);
      foreach($slides as $slide_val) {
          $active_class = ($count_li == 1) ? 'active':'';
          if($count_li == 1) $slide_li .= '<ol class="carousel-indicators">';
          $slide_li .= '<li data-target="#carouselExampleIndicators" data-slide-to="'.$count_li.'" class="'.$active_class.'"></li>';
          if($count_li == $total_count) $slide_li .= '</ol>';
          $count_li++;
      }
      $str = '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">';
      foreach($slides as $slide) {
          //print2($slide->post_title);
          $active_class = ($cnt == 1) ? 'active':'';
          $slider_img_url =  get_field('scf_slider_image', $slide->ID);
          if($cnt == 1) {
              $str .= $slide_li;
              $str .= '<div class="carousel-inner">';
          }
          $str .= '<div class="carousel-item '.$active_class.'">
                      <img src="'.$slider_img_url.'" alt="..." width="100%" />
                      <div class="carousel-caption d-none d-md-block">
                        <h5>'.$slide->post_title.'</h5>
                        <p>'.$slide->post_content.'</p>
                      </div>
                    </div>';
          if($cnt == $total_count) {
              $str .= '</div>';
          }
        $cnt++;
      }
      $str .= '<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>';
  }

  return $str;
}

# Taxonomy Category
add_shortcode( 'SS_SLIDER_ALL_TAXONOMY', 'ss_slider_all_taxonomy' );
function ss_slider_all_taxonomy($atts) {
    global $post;
    
    $limit = ($atts) ? $atts['limit']:5;
    $category_type = ($atts) ? $atts['category_type']:'';
    
    $taxonomy = 'slider_category'; // this is the name of the taxonomy
    $get_terms_default_attributes = array (
    		'taxonomy' => $taxonomy,
    		'orderby' => 'name',
    		'order' => 'ASC',
    		'hide_empty' => false,
    		'number' => $limit,
    		'fields' => 'all',
    		'slug' => $category_type,
    		'update_term_meta_cache' => true,
    		'meta_key' => array(),
    );
    $terms = get_terms($get_terms_default_attributes);
    //print2($terms);
    if($terms) {
        $str = '<ol>';
        foreach($terms as $term) {
            $str .= '<li>'.$term->name.'</li>';
        }
        $str .= '</ol>';
    }
    return $str;
}

# Taxonomy Category Slider
add_shortcode( 'SS_TAXONOMY_SLIDER', 'ss_taxonomy_slider' );
function ss_taxonomy_slider($atts) {
    global $post;
    
    $limit = ($atts) ? $atts['limit']:5;
    $category_type = ($atts) ? $atts['category_type']:'';
    
    $taxonomy = 'slider_category'; // this is the name of the taxonomy
    $get_terms_default_attributes = array (
    		'taxonomy' => $taxonomy,
    		'orderby' => 'name',
    		'order' => 'ASC',
    		'hide_empty' => true,
    		'number' => $limit,
    		'fields' => 'all',
    		'slug' => $category_type,
    		'update_term_meta_cache' => true,
    		'meta_key' => array(),
    );
    $terms = get_terms($get_terms_default_attributes);
    //print2($terms);
    $args = array(
    'post_type' => 'slide',
    'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => wp_list_pluck($terms,'slug')
                )
            )
    );
    
    $wp_query = new WP_Query( $args );
    if ( $wp_query->have_posts() ) {
        $cnt = 1;
        $count_li = 1;
        $total_count = $wp_query->post_count;
    	while ( $wp_query->have_posts() ) {
    		$wp_query->the_post();
    		$active_class = ($count_li == 1) ? 'active':'';
              if($count_li == 1) $slide_li .= '<ol class="carousel-indicators">';
              $slide_li .= '<li data-target="#carouselExampleIndicators" data-slide-to="'.$count_li.'" class="'.$active_class.'"></li>';
              if($count_li == $total_count) $slide_li .= '</ol>';
              $count_li++;
    	}
    	wp_reset_postdata();
    	
    	$str = '<div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">';
        while ( $wp_query->have_posts() ) {
            $wp_query->the_post();
              $active_class = ($cnt == 1) ? 'active':'';
              $slider_img_url =  get_field('scf_slider_image', $wp_query->post->ID);
              if($cnt == 1) {
                  $str .= $slide_li;
                  $str .= '<div class="carousel-inner">';
              }
              $str .= '<div class="carousel-item '.$active_class.'">
                          <img src="'.$slider_img_url.'" alt="..." width="100%" />
                          <div class="carousel-caption d-none d-md-block">
                            <h5>'.get_the_title().'</h5>
                            <p>Taxonomy Category Type - '.$category_type.'</p>
                          </div>
                        </div>';
              if($cnt == $total_count) {
                  $str .= '</div>';
              }
            $cnt++;
        }
        $str .= '<a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>';
    
      return $str;
    }
}


# Registered Users
add_shortcode( 'SS_REGISTERED_USERS', 'ss_registered_users' );
function ss_registered_users($atts) {
  global $post;
	
    $users = get_users( array( 'role__in' => array( 'subscriber' ) ) );
    if($users) {
        
        $cnt = 1;
        
           $str .= '<table class="table">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">First Name</th>
          <th scope="col">Last Name</th>
          <th scope="col">Username</th>
          <th scope="col">Email</th>
        </tr>
      </thead>
      <tbody>';
        foreach ( $users as $user ) {
            //print2($user);
            
            $first_name = get_user_meta( $user->ID, 'first_name', true);
            $last_name = get_user_meta( $user->ID, 'last_name', true);
            //print2($user_meta);
            $str .= '<tr>
              <th scope="row">' . $cnt . '</th>
              <td>' . esc_html( $first_name ) . '</td>
              <td>' . esc_html( $last_name ) . '</td>
              <td>' . esc_html( $user->user_login ) . '</td>
              <td>' . esc_html( $user->user_email ) . '</td>
            </tr>';
            
            $cnt++;
        }
        $str .= '</tbody>
</table>';
    }
  
  return $str;
}

