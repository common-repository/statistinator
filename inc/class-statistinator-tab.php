<?php 

/*
 * Factory Class for Admin Tabs
 */

class Statistinator_Tab {

    public $class;

    public function __construct( $tab ) {

        //Include Classes
        include STS_DIR . 'inc/class-statistinator-tab-graph.php';
        include STS_DIR . 'inc/class-statistinator-tab-account.php';
        include STS_DIR . 'inc/class-statistinator-tab-authentication.php';

        switch( $tab ) {
        case 'graph':
            $this->class = new Statistinator_Tab_Graph();
            return $this->class;
            break;
        case 'account':
            $this->class = new Statistinator_Tab_Account();
            return $this->class;
            break;
        case 'authentication':
            $this->class = new Statistinator_Tab_Authentication();
            return $this->class;
            break;
        }
    }

    public function render() {
        $this->class->render();
    }

}
