<?php

defined('DS') or define('DS', '/');
defined('RN') or define('RN', "\r\n");
defined('EOL') or define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
defined('TAB') or define('TAB', "\t");
defined('DATE_LONG_DATE') or define('DATE_LONG_DATE', 'd.m.Y H:i');
defined('DATE_LONG_DATE_2') or define('DATE_LONG_DATE_2', 'd.m.y H:i');
defined('DATE_LONG_DATE_SEC') or define('DATE_LONG_DATE_SEC', 'd.m.Y H:i:s');
defined('DATE_SB_LONG') or define('DATE_SB_LONG', 'd.m.Y.H.i.s');
defined('DATE_SHORT_DATE') or define('DATE_SHORT_DATE', 'd.m.Y');
defined('DATE_SHORT_DATE_2') or define('DATE_SHORT_DATE_2', 'd.m.y');
defined('DATE_SHORT_TIME') or define('DATE_SHORT_TIME', 'H:i');
defined('DATE_LONG_TIME') or define('DATE_LONG_TIME', 'H:i:s');
defined('DATE_TIMESTAMP') or define('DATE_TIMESTAMP', 'U');
defined('DATE_ATOM_1') or define('DATE_ATOM_1', 'Y-m-d H:i:s');
defined('DATE_ATOM_SHORT') or define('DATE_ATOM_SHORT', 'Y-m-d');

if (!function_exists("d")) {
    /**
     * Debug function
     */
    function d()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo 'File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . RN;
        array_map(function ($x) {
            var_dump($x);
            echo EOL;
        }, $caller['args']);
        die;
    }
}

if (!function_exists("dd")) {
    /**
     * Debug function
     */
    function dd()
    {
        $caller = debug_backtrace();
        $caller = array_shift($caller);
        echo '<code>File: ' . $caller['file'] . ' / Line: ' . $caller['line'] . '</code>';
        echo '<pre>';
        array_map(function ($x) {
            var_dump($x);
        }, $caller['args']);
        echo '</pre>';
        die;
    }
}
