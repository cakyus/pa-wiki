<?php

/**
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 **/

function print_help(){
	echo <<<EOF
Clean text files
Usage: php clean.php <file>

EOF;

}

function main(){

	if ($_SERVER['argc'] == 1){
		print_help();
		exit(0);
	}

	$file = $_SERVER['argv'][1];

	clean_file($file);
}

function clean_file($file){

	$text = file_get_contents($file);

	// GLOBAL REPLACEMENT

	// use LF for line break

	$text = str_replace("\r\n", "\n", $text);

	// replace tab with space

	$text = str_replace("\t", " ", $text);

	// replace excessive spaces

	$text = preg_replace("/ +/", " ", $text);

	// remove page number

	$text = preg_replace("/^-\s+[0-9]+\s+\-/m", "", $text);

	// replace excessive line break

	$text = preg_replace("/\n{2,}/", "\n\n", $text);

	// no space after "(" and before ")"

	$text = preg_replace("/\( +/", "(", $text);
	$text = preg_replace("/ +\)/", ")", $text);

	// no space after "/" and before "/"

	$text = preg_replace("/\/ +/", "/", $text);
	$text = preg_replace("/ +\//", "/", $text);

	file_put_contents($file, $text);
}

main();
