<?php
// FILE: /app/core/View.php

class View {

    public static function render($view, $data = array(), $layout = 'main') {
        extract($data);

        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: $viewFile");
        }

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        if ($layout) {
            $layoutFile = __DIR__ . '/../views/layouts/' . $layout . '.php';

            if (file_exists($layoutFile)) {
                include $layoutFile;
            } else {
                echo $content;
            }
        } else {
            echo $content;
        }
    }

    public static function make($view, $data = array(), $layout = 'main') {
        return self::render($view, $data, $layout);
    }

    public static function partial($view, $data = array()) {
        extract($data);
        $viewFile = __DIR__ . '/../views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewFile)) {
            throw new Exception("Partial view file not found: $viewFile");
        }

        include $viewFile;
    }
}
