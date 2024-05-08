<?php

if (!function_exists("rroute")) {
    /** Same as `route()`, but with `absolute` fixed to `false`. */
    function rroute(string $name, array $parameters = [])
    {
        return route($name, $parameters, absolute: false);
    }
}

if (!function_exists("omit_origin")) {
    function omit_origin($url)
    {
        $parsed = parse_url($url);
        $omitted =
            ($parsed["path"] ?? "/") .
            (isset($parsed["query"]) ? "?{$parsed["query"]}" : "") .
            (isset($parsed["fragment"]) ? "#{$parsed["fragment"]}" : "");
        return $omitted;
    }
}
