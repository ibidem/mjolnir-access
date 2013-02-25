#!/usr/bin/env ruby

require 'rubygems'
require 'fssm'

require_relative '+compile.rb'

path = File.expand_path(File.dirname(__FILE__)+'/src');

puts ""
puts " Monitoring [#{path}] for changes."
puts " ----------------------------------------------------------------------- "
FSSM.monitor($basedir) do

	update do |b, r|
		puts " >>> changed: #{r}"
		process(r);
	end#do

	create do |b, r|
		puts " >>> created: #{r}"
		process(r);
	end#do

	delete do |b, r|

		# we ignore the remove action

		# puts " >>> removed: #{r}"
		# process(r);
		
	end#do

end#FSSM
