<?php

// Para imagens JPEG
function ResizeJpeg($image, $newWidth, $newHeight) {

	//Open the file to resize
	$srcImage = ImageCreateFromJpeg ( $image );

	//Obtain the original image Height and Width
	$srcWidth  = ImageSX( $srcImage );
	$srcHeight = ImageSY( $srcImage );

	// the follwing portion of code checks to see if
	// the width > height or if width < height
	// if so it adjust accordingly to make sure the image
	// stays smaller then the $newWidth and $newHeight
	if ( $srcWidth < $srcHeight ) {
		$destWidth  = $newWidth * $srcWidth/$srcHeight;
		$destHeight = $newHeight;
	}
	else {
		$destWidth  = $newWidth;
		$destHeight = $newHeight * $srcHeight/$srcWidth;
	}
	
	// creating the destination image with the new Width and Height
	$destImage = ImageCreateTrueColor( $destWidth, $destHeight);

	//copy the srcImage to the destImage
	ImageCopyResized( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );

	//create the file
	ImageJpeg ( $destImage );

	//free the memory used for the images
	ImageDestroy( $srcImage  );
	ImageDestroy( $destImage );

}

// Para imagens GIF
function ResizeGif($image, $newWidth, $newHeight) {

	//Open the file to resize
	$srcImage = ImageCreateFromGif ( $image );
	
	//Obtain the original image Height and Width
	$srcWidth  = ImageSX( $srcImage );
	$srcHeight = ImageSY( $srcImage );

	// the follwing portion of code checks to see if
	// the width > height or if width < height
	// if so it adjust accordingly to make sure the image
	// stays smaller then the $newWidth and $newHeight
	if ( $srcWidth < $srcHeight ) {
		$destWidth  = $newWidth * $srcWidth/$srcHeight;
		$destHeight = $newHeight;
	}
	else {
		$destWidth  = $newWidth;
		$destHeight = $newHeight * $srcHeight/$srcWidth;
	}

	// creating the destination image with the new Width and Height
	$destImage = ImageCreateTrueColor( $destWidth, $destHeight);

	//copy the srcImage to the destImage
	ImageCopyResized( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );

	//create the file
	ImageGif ( $destImage );

	//free the memory used for the images
	ImageDestroy( $srcImage  );
	ImageDestroy( $destImage );

}


// Para imagens BMP
function ResizeBmp($image, $newWidth, $newHeight) {

	//Open the file to resize
	$srcImage = ImageCreateFromWbmp ( $image );

	//Obtain the original image Height and Width
	$srcWidth  = ImageSX( $srcImage );
	$srcHeight = ImageSY( $srcImage );

	// the follwing portion of code checks to see if
	// the width > height or if width < height
	// if so it adjust accordingly to make sure the image
	// stays smaller then the $newWidth and $newHeight
	if ( $srcWidth < $srcHeight ) {
		$destWidth  = $newWidth * $srcWidth/$srcHeight;
		$destHeight = $newHeight;
	}
	else {
		$destWidth  = $newWidth;
		$destHeight = $newHeight * $srcHeight/$srcWidth;
	}

	// creating the destination image with the new Width and Height
	$destImage = ImageCreateTrueColor( $destWidth, $destHeight);

	//copy the srcImage to the destImage
	ImageCopyResized( $destImage, $srcImage, 0, 0, 0, 0, $destWidth, $destHeight, $srcWidth, $srcHeight );

	//create the file
	Image2wbmp ( $destImage );

	//free the memory used for the images
	ImageDestroy( $srcImage  );
	ImageDestroy( $destImage );

}



?>

