#!/usr/bin/env ruby

require_relative 'library/cleanup.rb'

# run compass compiler
Kernel.exec('compass compile -c library/compass-production.rb --environment production')
