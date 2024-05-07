<?php

/** Same as `route()`, but with `absolute` fixed to `false`. */
function rroute(string $name, array $parameters = [])
{
    return route($name, $parameters, absolute: false);
}
