<?php

namespace Core\System;

use Core\Service\Service;
use Core\Auth\Session;

class View
{
    private $page_title;

    private $module;
    private $controller;
    private $action;
    private $params;

    private $feedback_positive = array();
    private $feedback_negative = array();

    private $views_path;

    function __construct($data = array())
    {
        $this->views_path = Service::getConfig()->get("PATH_MODULE") . DS . "Views" . DS;
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function assign($key, $value)
    {
        $this->{$key} = $value;
        return $this;
    }

    public function render($filename, $data = null)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        $this->loadView('_templates/header.php');
        $this->loadView($filename . '.php');
        $this->loadView('_templates/footer.php');
    }

    public function renderWithoutHeaderAndFooter($filename, $data = null)
    {
        if ($data) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }

        require $this->views_path . $filename . '.php';
    }

    public function renderJSON($data)
    {
        header("Content-Type: application/json");
        echo json_encode($data);
    }


    public function renderFeedbackMessages()
    {
        $this->feedback_positive = Service::getSession()->get('feedback_positive');
        $this->feedback_negative = Service::getSession()->get('feedback_negative');

        require $this->views_path . '_templates/feedback.php';

        Service::getSession()->set('feedback_positive', null);
        Service::getSession()->set('feedback_negative', null);
    }

    public function errorPage()
    {
        $this->setTitle(Service::getText()->get("ERROR_PAGE_TITLE"))->render("error_page");
        exit;
    }

    public function loadView($file)
    {
        require $this->views_path . $file;
    }

    public function setTitle($title = "")
    {
        $this->page_title = $title;
        return $this;
    }

    private function IsLoggedIn()
    {
        return Service::getAuth()->IsLoggedIn();
    }

    private function form()
    {
        return Service::getForm();
    }

    private function text($key)
    {
        return Service::getText()->get($key);
    }

    private function get($property, $default = "")
    {
        return (property_exists($this, $property)) ? $this->$property : $default;
    }

    private function getConfig($key, $default = "")
    {
        return Service::getConfig()->get($key, $default);
    }

    private function getFromSession($key)
    {
        return Service::getSession()->get($key);
    }

    private function route(array $data)
    {
        $route = array();
        if ($this->module != "Frontend" || isset($data["module"])) {
            $route[] = (isset($data["module"])) ? $data["module"] : $this->module;
        }

        $route[] = (isset($data["controller"])) ? $data["controller"] : $this->controller;
        if (isset($data["action"])) {
            $route[] = $data["action"];
        }

        if (isset($data["params"])) {
            foreach ((array)$data["params"] as $value) {
                $route[] = $value;
            }
        }

        return $this->getConfig("URL") . "/" . implode("/", array_map("strtolower", $route));
    }

    public function selfRoute()
    {
        return $this->route(["controller" => $this->controller, "action" => $this->action, "params" => $this->params]);
    }

    public function encodeHTML($str)
    {
        return htmlentities($str, ENT_QUOTES, 'UTF-8');
    }

    public function strDate($date, $format = "F j, Y")
    {
        return date($format, strtotime($date));
    }

    public function beautifyText($str)
    {
        $str = wordwrap($str);
        $str = nl2br($str);
        return $str;
    }

    public function loadCSSLink($url)
    {
        return sprintf("<link rel='stylesheet' href='%s'>\n", $url);
    }

    public function loadJSFile($url)
    {
        return sprintf("<script type='text/javascript' src='%s'></script>\n", $url);
    }

    public function loadImg($filename)
    {
        return sprintf("<img src='%s/assets/%s'/>\n", Service::getConfig()->get("URL"), $filename);
    }

    public function renderImage($file, $default = "", $html = true, $attrs = [])
    {
        $url = "";
        if (!empty($file)) {
            $url = $this->uploadedFile($file);
        } else {
            $url = Service::getConfig()->get("URL") . $default;
        }
        $_attrs = [];
        foreach ($attrs as $key => $value) {
            $_attrs[] = sprintf('%s="%s"', $key, $value);
        }
        return ($html) ? sprintf("<img %s src='%s' />", implode(" ", $_attrs), $url) : $url;
    }

    public function uploadedFile($file)
    {
        return Service::getConfig()->get("URL") . '/uploads/' . $file;
    }
}
