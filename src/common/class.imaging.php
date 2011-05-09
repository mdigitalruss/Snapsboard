<?php
/*
* File: SimpleImage.php
* Author: Simon Jarvis
* Copyright: 2006 Simon Jarvis
* Date: 08/11/06
* Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
* 
* This program is free software; you can redistribute it and/or 
* modify it under the terms of the GNU General Public License 
* as published by the Free Software Foundation; either version 2 
* of the License, or (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful, 
* but WITHOUT ANY WARRANTY; without even the implied warranty of 
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the 
* GNU General Public License for more details: 
* http://www.gnu.org/licenses/gpl.html
*
*/
 
class Imaging 
{
   
   var $image;

   
	function watermark($string, $size = 12)
	{
		$black = ImageColorAllocate($this->image, 0, 0, 0); 
		$start_x = 10; 
		$start_y = 30; 

		Imagettftext($this->image, $size, 0, $start_x, $start_y, $black, 'BPreplay-webfont.ttf', $string);
	}
 
	function __construct($string) 
	{

		$this->image = imagecreatefromstring($string); 
	  
	}

	function output() {
		//output as PNG
		header('Content-Type: image/jpeg');
		imagejpeg($this->image,null,85);
		imagedestroy($this->image);
	}
	
	function save($filename) {
		imagejpeg($this->image,$filename,85);
	}
	
	function getWidth() {
		return imagesx($this->image);
	}
	
	function getHeight() {
		return imagesy($this->image);
	}
	
	function resizeToHeight($height) {
		if($height > 1000){ $height = 1000;}
		
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	
	function resizeToWidth($width) {
		if($width > 1000){ $width = 1000;}
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	
	function scale($scale) {
		if($scale > 500){ $width = 500;}
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100; 
		$this->resize($width,$height);
	}
	
	function resize($width,$height) {
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;   
	}      
}
?>