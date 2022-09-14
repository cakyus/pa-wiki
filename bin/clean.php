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

	$document = new \Document;
	$document->clean($file);
}

class Paragraph {

	protected $items;
	protected $itemIndex;
	protected $itemCount;

	public function __construct() {
		$this->items = array();
		$this->itemIndex = 0;
	}

	public function clean($text) {

		$this->items = explode("\n\n", $text);
		$this->itemCount = count($this->items);

		while ($this->itemIndex < $this->itemCount){

			$paragraph = $this->items[$this->itemIndex];
			$originalParagraph = $paragraph;

			$paragraph = $this->replaceAyatNumber($paragraph);
			$paragraph = $this->replaceNumberingBracket($paragraph);
			$paragraph = $this->mergeNumbering($paragraph);
			// $paragraph = $this->replaceWordwarp($paragraph);
			$paragraph = $this->replaceTrim($paragraph);

			if ($originalParagraph !== $paragraph){
				echo $this->itemIndex." > $originalParagraph\n";
				echo $this->itemIndex." < $paragraph\n";
			}

			$this->items[$this->itemIndex] = $paragraph;
			$this->itemIndex++;
		}

		return implode("\n\n", $this->items);
	}

	/**
	 * Replace numbering "(1) " to "1. "
	 * Replace numbering "1) " to "1. "
	 **/

	public function replaceNumberingBracket($paragraph) {
		$paragraph = preg_replace("/^\(([0-9]+)\)$/", "$1.", $paragraph);
		$paragraph = preg_replace("/^\(([0-9]+)\) /", "$1. ", $paragraph);
		$paragraph = preg_replace("/^([0-9a-z]+)\) /", "$1. ", $paragraph);
		return $paragraph;
	}

	/**
	 * Merge numbering "1." to "1. -"
	 **/

	public function mergeNumbering($paragraph) {
		return $paragraph;
	}

	/**
	 * Replace "ayat(1)" to "ayat 1"
	 * Replace "ayat (1)" to "ayat 1"
	 **/

	public function replaceAyatNumber($paragraph) {
		$paragraph = preg_replace("/ayat\(([0-9]+)\)/", "ayat $1", $paragraph);
		$paragraph = preg_replace("/ayat +\(([0-9]+)\)/", "ayat $1", $paragraph);
		return $paragraph;
	}

	/**
	 * Wordwarp
	 **/

	public function replaceWordwarp($paragraph) {
		$paragraph = wordwrap($paragraph);
		return $paragraph;
	}

	/**
	 * Trim
	 **/

	public function replaceTrim($paragraph) {
		$paragraph = trim($paragraph);
		return $paragraph;
	}
}

class Document {

	public function clean($file) {

		$text = file_get_contents($file);

		$originalText = $text;

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

		$paragraph = new \Paragraph;
		$text = $paragraph->clean($text);
		$text .= "\n";

		if ($originalText !== $text){
			file_put_contents($file, $text);
		}
	}
}

main();
