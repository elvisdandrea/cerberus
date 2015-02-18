<?php

/**
 * Class HighCharts
 *
 * Class for manipulating HighCharts
 */


class HighCharts {

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


    public function setTitle($title, $position = -20) {

        $this->data['title']['text'] = $title;
        $this->data['title']['x']    = $position;
    }

    public function setSubTitle($subtitle, $position = -20) {

        $this->data['subtitle']['text'] = $subtitle;
        $this->data['subtitle']['x']    = $position;
    }

    public function addCategory($category) {

        if (is_array($category)) {
            $this->data['xAxis']['categories'] = array_merge($this->data['xAxis']['categories'], $category);
        } else {
            $this->data['xAxis']['categories'][] = $category;
        }
    }

    public function addSeries($name, $data) {

        $this->data['series'][] = array(
            'name'  => $name,
            'data'  => $data
        );
    }

    public function render($element) {

        echo '$(' . $element . ').highcharts(' . json_encode($this->data, JSON_UNESCAPED_UNICODE) . ')';
    }


}