#!/usr/bin/env ruby

require 'rubygems'
require 'json'
require 'zip/zipfilesystem' # gem install rubyzip
require 'net/http'

$basedir = nil;
$config = nil;

def download(domain, file, to)
	Net::HTTP.start(domain) do |http|
		resp = http.get(file)
		open(to, "wb") do |file|
			file.write(resp.body)
		end#open
	end#http.start
end#def

def ensure_compiler()
	# ensure closure compiler jar is present
	librarydir = $basedir.gsub(/\/\\$/, '') + '/library/'
	if ! File.exists? librarydir + 'compiler.jar'
		Dir.chdir librarydir
		if ! File.exists? librarydir+"/composer.zip"
			download("closure-compiler.googlecode.com", "/files/compiler-latest.zip", librarydir+"/composer.zip")
		end
		Zip::ZipFile.open(librarydir+"/composer.zip") do |zipfile|
			zipfile.each do |file|
				if file.name == 'compiler.jar'
					puts "extracting #{file}"
					zipfile.extract(file.name, librarydir + 'compiler.jar')
				end#if
			end#each
		end#open
	end#def
	Dir.chdir $basedir
end#def

$compiler_options = ' --process_jquery_primitives --warning_level QUIET --third_party --compilation_level WHITESPACE_ONLY'

def read_configuration()
	json_config = `php -r "chdir('#{$basedir}'); echo json_encode(include '+scripts.php');"`;
	$config = JSON.parse json_config
	if $config['targeted-common'] == nil
		$config['targeted-common'] = [];
	else # not nil
		$config['targeted-common'] = $config['targeted-common'].find_all do |item| item !~ /(^[a-z]+:\/\/|^\/\/).*$/ end
	end#def

	# include common files
	$config['targeted-mapping'].each do |key, files|
		files = files.find_all do |item| item !~ /(^[a-z]+:\/\/|^\/\/).*$/ end
		$config['targeted-mapping'][key] = $config['targeted-common'] + files;
	end#each

	# convert to paths
	$config['targeted-mapping'].each do |key, files|
		files = files.find_all do |item| item !~ /(^[a-z]+:\/\/|^\/\/).*$/ end
		files.collect! do |file| 'src/'+file+'.js'; end#collect
		$config['targeted-mapping'][key] = files
	end#each

	# convert to paths
	files = $config['complete-mapping']
	files = files.find_all do |item| item !~ /(^[a-z]+:\/\/|^\/\/).*$/ end
	files.collect! do |file| 'src/'+file+'.js'; end#collect
	$config['complete-mapping'] = files
	
end#def

def recompile()
	if ($config['mode'] == 'complete')
		regenerate('master', $config['complete-mapping'])
	else # targeted mode
		$config['targeted-mapping'].each do |key, files|
			regenerate(key, files)
		end#each
	end#if
end#def

def regenerate(key, files)
	if files.size > 0
		puts " compiling #{key}"
		`java -jar library/compiler.jar #{$compiler_options} --js #{files.join(' ')} --js_output_file ./root/#{key}.min.js --create_source_map ./root/#{key}.min.js.map --source_map_format=V3`;
	end
end#def

def process (r)

	if r.eql? '+scripts.php'
		puts ' >>> recompiling all...'
		# reload $configuration
		read_configuration();
		if $config['mode'] == 'complete'
			regenerate('master', $config['complete-mapping'])
		else # non-complete mode
			# regenerate all
			$config['targeted-mapping'].each do |key, files|
				regenerate(key, files)
			end#each
		end#if
	end

	if $config['mode'] == 'complete'
	
		$config['complete-mapping'].each do |file|
			if file.eql? r
				puts " >>> recompiling [complete-script]"
				# regenerate the closure
				recompile()
				break;
			end#if
		end#each

	else # non-complete mode

		# search configuration for file
		$config['targeted-mapping'].each do |key, files|
			files.each do |file|
				if file.eql? r
					puts " >>> recompiling [#{key}]"
					# regenerate the closure
					regenerate(key, files);
				end#if
			end#each
		end#each
		
	end#if

end#def

# ---------------------------------------------------------------------------- #

$basedir = File.expand_path(File.dirname(__FILE__));
Dir.chdir $basedir
path = File.expand_path(File.dirname(__FILE__)+'/src');

ensure_compiler()

puts 
puts " Recompiling..."
puts " ----------------------------------------------------------------------- "
read_configuration();
recompile();
puts " >>> all files regenarated "
