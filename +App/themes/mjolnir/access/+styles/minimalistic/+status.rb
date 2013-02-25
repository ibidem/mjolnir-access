#!/usr/bin/env ruby

$silent = true;

require_relative 'library/cleanup.rb'

# run compass compiler
Kernel.exec("compass stats -c compass-dev.rb -q")
