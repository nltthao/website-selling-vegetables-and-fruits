<?php

namespace ReviewX\Controllers\Admin\Email;

use ReviewX\Controllers\Controller;

/**
 * Class Behaviour
 * @package ReviewX\Controllers\Admin\Email
 */
class Behaviour extends Controller
{
    /**
     * @param $value
     * @return string
     */
    public function getText($value)
    {
        return (string) $value;
    }

    /**
     * @param $param
     * @return string
     */
    public function getLink($param)
    {
        if (is_array($param) && ( !isset( $param['type'] ) || $param['type'] != 'product_image' ) ) {
            return $this->getLinkArray($param);
        }
        if ( isset( $param['type'] ) && $param['type'] == 'product_image' ) {
            return $this->productImage($param['url']);
        }

        if (is_string($param)) {
            return $this->getLinkText($param, $param);
        }
    }

    /**
     * @param $param
     * @return false|string
     */
    public function getDate($param)
    {
        return date('d-M-Y', strtotime($param));
    }

    /**
     * @param $link
     * @param $label
     * @return string
     */
    protected function getLinkText($link, $label)
    {
        $link   = esc_url($link);
        $label  = esc_attr($label);
        return "<a href='{$link}' target='_blank'>{$label}</a>";
    }

    /**
     * @param $params
     * @return string
     */
    protected function getLinkArray($params)
    {
        $links = [];
        foreach ($params as $label => $link) {
            $link   = esc_url($link);
            $label  = esc_attr($label);            
            $links[] = $this->getLinkText($link, $label) . '<br>';
        }

        return implode('', $links);
    }

    protected function productImage($link)
    {
        return "<img src='{$link}' style='height:90px; widht:90px;'/>";
    }
}