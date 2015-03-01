<?php

/**
 * Class HighCharts (Unfinished)
 *
 * Class for manipulating HighChart graphics
 *
 */

class HighCharts {

    /**
     * The Result Data that will be
     * converted into the JSON that
     * renders the chart
     *
     * @var array
     */
    public $data = array(

        'title'     => array(
            'text'  => '',
            'x'     => -20
        ),

        'subtitle'  => array(
            'text'  => '',
            'x'     => -20
        ),

        'series'    => array()

    );


    /**
     * Sets the Chart Title
     *
     * @param   string      $title      - The title
     * @param   int         $position   - The X position of the title (-20 = centered)
     */
    public function setTitle($title, $position = -20) {

        $this->data['title']['text'] = $title;
        $this->data['title']['x']    = $position;
    }

    /**
     * Sets the Chart SubTitle
     *
     * @param   string      $subtitle   - The subtitle
     * @param   int         $position   - The X position of the title (-20 = centered)
     */
    public function setSubTitle($subtitle, $position = -20) {

        $this->data['subtitle']['text'] = $subtitle;
        $this->data['subtitle']['x']    = $position;
    }

    /**
     * Adds a xAxis Category to the chart
     *
     * @param   string      $category       - The category name
     */
    public function addCategory($category) {

        if (is_array($category)) {
            isset($this->data['xAxis']['categories']) || $this->data['xAxis']['categories'] = array();
            $this->data['xAxis']['categories'] = array_merge($this->data['xAxis']['categories'], $category);
        } else {
            $this->data['xAxis']['categories'][] = $category;
        }
    }

    /**
     * Checks if a category is in the data
     *
     * @param   string      $category       - The category name
     * @return  bool
     */
    public function hasCategory($category) {

        return is_array($this->data['xAxis']['categories']) && in_array($category, $this->data['xAxis']['categories']);
    }

    /**
     * Returns all categories
     *
     * @return  array
     */
    public function getCategories() {

        return $this->data['xAxis']['categories'];
    }

    /**
     * Add a data series to the chart
     *
     * @param   string      $name       - The series name
     * @param   array       $data       - The data array
     */
    public function addSeries($name, array $data) {

        $this->data['series'][] = array(
            'name'  => $name,
            'data'  => $data
        );
    }

    /**
     * Renders the chart into an element
     *
     * @param   $element        - The element string
     */
    public function render($element) {

        echo '$("' . $element . '").highcharts(' . json_encode($this->data, JSON_UNESCAPED_UNICODE) . ')';
    }


}