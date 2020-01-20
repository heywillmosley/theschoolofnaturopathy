<?php
/**
 * Plugin Name: EWSN Sidebar
 * Plugin URI: theschoolofnaturopathy.com
 * Description: Sidebar that displays recommended book and product suggestions based on the user's progress in the School's tracks, courses and lessons.
 * Version: 1.0
 * Author: Will Mosley
 * Author URI: https://heywillmosley.com
 */

class EWSN_sidebar {

    private $books;
    private $products;
    private $relatedContent;
    private $relatedBooks;
    private $relatedProducts;
    private $sidePosts;

    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts_and_css' ) );
        add_action('dynamic_sidebar', array($this, 'runSidebar'));
    }

    public function enqueue_scripts_and_css() {
        wp_register_style( 'side-post', plugin_dir_url( __FILE__ ) . 'css/ewsn-sidebar.css' );
        wp_enqueue_style( 'side-post' );
    }

    public function runSidebar() {

        $this->setBooks();
        $this->setProducts();
        $this->relatedBooks = $this->setRelatedContent($this->books);
        $this->relatedProducts = $this->setRelatedContent($this->products);
        $this->setSidePosts($this->relatedBooks, 'Book');
        $this->getSidePosts();
        $this->setSidePosts($this->relatedProducts, 'Product');
        $this->getSidePosts();
    }

    protected function setBooks() {
        $this->books = get_field('side_books', 'options');
    }

    protected function getBooks() {
        echo $this->books;
    }

    protected function setProducts() {
        $this->products = get_field('side_products', 'options');
    }

    protected function getProducts() {
        echo $this->products;
    }

    protected function setRelatedContent($contents) {

        $related_content = array();
        $i = 0;

        foreach($contents as $content) { // run through content
            foreach($content['display_where'] as $ID) { // run through IDs this content is associated with
                if( $ID == get_the_ID() ) { // find books that match current page ID
                    $related_content[$i] = $content;
                    $i++;
                    break;
                } // end if
            } // end foreach
        } // end foreach

        shuffle( $related_content ); // shuffle sidebar

        $this->relatedContent = $related_content;
        return $related_content;
    }

    protected function getRelatedContent() {
        echo $this->relatedContent;
    }

    protected function setSidePosts($items, $type) {
        
        $count = count($items);

        if($count > 0 )
            echo "<h4 class='card-header'>Recommended $type(s)</h4>";

        $i = 0;
        $renders = NULL;

        foreach($items as $item) {

            if($i == 4) // max amount of items to display
                break;

            $title = $item['title'];
            if($type == 'product')
                $author = $item['author'];
            $image = $item['image'];
            $summary = shorten_string_by_words( $item['summary'] );
            $url = $item['url'];

            if( $i == 0 && $type =='Book' ) { // first item display as featured

                $render = "<div class='card sc-card sc-card-md'>";
                if(!empty( $image ) )
                    $render .= "<a href='$url' target='_blank'><img src='$image' class='card-img-top' alt='$title'></a>";
                $render .= "<div class='card-body'>";
                $render .= "<a href='$url' target='_blank'><h5 class='card-title'>$title</h5></a>";
                if(!empty($author))
                    $render .= "<p class='card-text caption'>$author</p>";
                $render .= "<a href='$url' target='_blank' class='btn btn-primary'>View</a>";
                $render .= "</div>";
                $render .= "</div>";

            } else { // display as thumbnail

                $render = "<div class='media sc-card sc-card-sm'>";
                if(!empty( $image ) )
                    $render .= "<a href='$url' target='_blank'><img src='$image' class='mr-3' alt='$title'></a>";
                $render .= "<div class='media-body'>";
                $render .= "<a href='$url' target='_blank'><h5 class='mt-0'>$title</h5></a>";
                if(!empty($author))
                    $render .= "<p class='card-text caption'>$author</p>";
                $render .= "<p class='caption'>$summary</p>";
                $render .= "<a href='$url' target='_blank'>View</a>";
                $render .= "</div>";
                $render .= "</div>";

            } // end else

            $renders .= $render;
            $i++;
            
        } // end foreach

        $this->sidePosts = $renders;
        return $renders;
    }

    protected function getSidePosts() {
        echo $this->sidePosts;
    }

} // end class
$ewsn_sidebar = new EWSN_sidebar;