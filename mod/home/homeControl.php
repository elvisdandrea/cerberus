<?

/**
 * Class homeControl
 *
 */


class homeControl extends Control {

    private $view;

    public function __construct() {
        $this->view = new homeView();
    }

    public function itStarts() {
        $this->view->loadTemplate('header');
        $this->view->render();
    }
}