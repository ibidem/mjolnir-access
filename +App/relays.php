<?php namespace mjolnir\access;

$public = \app\CFS::config('mjolnir/layer-stacks')['public'];

\app\Router::process('mjolnir:access/channel.route', $public);
\app\Router::process('mjolnir:access/endpoint.route', $public);
\app\Router::process('mjolnir:access/auth.route', $public);
