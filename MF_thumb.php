<?php
/*
	Magic Fields Thumb class
	
	Paramters
	---------
	w: width
	h: height
	zc: zoom crop (0 or 1)
	q: quality (default is 75 and max is 100)
*/
class mfthumb{

	function __construct(){
		require_once(ABSPATH."/wp-admin/includes/image.php");
		require_once(ABSPATH."/wp-includes/media.php");
	}

	/**
	 * This function is almost equal to the image_resize (native function of wordpress)
	 */
	function image_resize( $file, $max_w, $max_h, $crop = false, $dest_path = null,$jpeg_quality = 90 ) {
		$image = wp_load_image( $file );
		if ( !is_resource( $image ) )
			return new WP_Error('error_loading_image', $image);

		$size = @getimagesize( $file );
		if ( !$size )
				return new WP_Error('invalid_image', __('Could not read image size'), $file);
		list($orig_w, $orig_h, $orig_type) = $size;
		
		$dims = image_resize_dimensions($orig_w, $orig_h, $max_w, $max_h, $crop);
		if ( !$dims ){
			$dims = array(0,0,0,0,$orig_w,$orig_h,$orig_w,$orig_h);
		}
		list($dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h) = $dims;

		$newimage = imagecreatetruecolor( $dst_w, $dst_h );
		imagecopyresampled( $newimage, $image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

		// convert from full colors to index colors, like original PNG.
		if ( IMAGETYPE_PNG == $orig_type && !imageistruecolor( $image ) )
			imagetruecolortopalette( $newimage, false, imagecolorstotal( $image ) );

		// we don't need the original in memory anymore
		imagedestroy( $image );
		$info = pathinfo($dest_path);
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = basename($dest_path, ".{$ext}");
		
		$destfilename = "{$dir}/{$name}.{$ext}";
		
		if ( IMAGETYPE_GIF == $orig_type ) {
 			if ( !imagegif( $newimage, $destfilename ) )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		} elseif ( IMAGETYPE_PNG == $orig_type ) {
			if ( !imagepng( $newimage, $destfilename ) )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		} else {
			// all other formats are converted to jpg
			if ( !imagejpeg( $newimage, $destfilename, apply_filters( 'jpeg_quality', $jpeg_quality, 'image_resize' ) ) )
				return new WP_Error('resize_path_invalid', __( 'Resize path invalid' ));
		}

		imagedestroy( $newimage );

		// Set correct file permissions
		$stat = stat( dirname( $destfilename ));
		$perms = $stat['mode'] & 0000666; //same permissions as parent folder, strip off the executable bits
		@ chmod( $destfilename, $perms );

		return $destfilename;
	}
}
?>