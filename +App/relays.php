<?php namespace mjolnir\access;

$mvc = \app\CFS::config('mjolnir/layer-stacks')['mvc'];

\app\Router::process('\mjolnir\access\channel', $mvc);
\app\Router::process('\mjolnir\access\endpoint', $mvc);
\app\Router::process('\mjolnir\access\a12n', $mvc);
