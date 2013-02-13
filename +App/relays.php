<?php namespace mjolnir\access;

$mvc = \app\CFS::config('mjolnir/layer-stacks')['public'];

\app\Router::process('mjolnir:access/channel.route', $mvc);
\app\Router::process('mjolnir:access/endpoint.route', $mvc);
\app\Router::process('mjolnir:access/auth.route', $mvc);
