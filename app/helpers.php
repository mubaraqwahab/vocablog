<?php

function rroute(string $name, array $parameters = [])
{
    return route($name, $parameters, absolute: false);
}
