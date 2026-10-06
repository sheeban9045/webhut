<?php

if (!function_exists('load_env_file')) {

    function load_env_file($path) {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);

            // skip comments and malformed lines
            if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // strip matching surrounding quotes, e.g. KEY="some value"
            if (strlen($value) > 1) {
                $first = $value[0];
                $last = $value[strlen($value) - 1];
                if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                    $value = substr($value, 1, -1);
                }
            }

            if ($name === '') {
                continue;
            }

            // don't override real environment variables already set by the server/host
            if (getenv($name) === false && !isset($_ENV[$name])) {
                putenv($name . '=' . $value);
                $_ENV[$name] = $value;
            }
        }
    }

}

if (!function_exists('env')) {

    //reads a value from the environment (.env file or real server env vars), with an optional default
    function env($key, $default = null) {
        $value = getenv($key);
        return $value === false ? $default : $value;
    }

}

load_env_file(__DIR__ . '/.env');
