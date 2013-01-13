<?php namespace mjolnir\access;

$mvc = \app\CFS::config('mjolnir/layer-stacks')['mvc'];

\app\Relay::process('\mjolnir\access\channel', $mvc);
\app\Relay::process('\mjolnir\access\endpoint', $mvc);
\app\Relay::process('\mjolnir\access\a12n', $mvc);
