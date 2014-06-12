BFI Focal Point
===============

This is an addon for BFI Image (forked version), which let's the user specify an image focal point and saving it to post meta as a comma separated string.

Currently only works with the version BFI Image that I forked to add the crop offset parameter.

[dominicwhittle/bfi_thumb](https://github.com/dominicwhittle/bfi_thumb)


Usage
=====

To get the focal point (if one exists) as a comma separated string, use:

$string = get_post_meta( $attachment_id, '_wknds_focus', true );


To get the focal point as an array — e.g., array( 0.5, 0.5 ) — suitable for
use in the bfi_image crop parameter, use:

$array = get_crop_focus( $attachment_id, $return_false_if_not_set );



@TODO
=====

- replace param on get_crop_focus() with has_crop_focus() function
- namespace consistently
