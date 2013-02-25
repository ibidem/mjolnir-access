#!/usr/bin/env ruby

require_relative 'library/cleanup.rb'

# run compass compiler
Kernel.exec('compass watch -c library/compass-dev.rb --environment development')
