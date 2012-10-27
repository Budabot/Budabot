<?php
/**
 * Copyright 2012 Dennis Haarbrink <dhaarbrink@gmail.com>
 *
 * Permission to use, copy, modify, and distribute this software
 * and its documentation for any purpose and without fee is hereby
 * granted, provided that the above copyright notice appear in all
 * copies and that both that the copyright notice and this
 * permission notice and warranty disclaimer appear in supporting
 * documentation, and that the name of the author not be used in
 * advertising or publicity pertaining to distribution of the
 * software without specific, written prior permission.
 *
 * The author disclaim all warranties with regard to this
 * software, including all implied warranties of merchantability
 * and fitness.  In no event shall the author be liable for any
 * special, indirect or consequential damages or any damages
 * whatsoever resulting from loss of use, data or profits, whether
 * in an action of contract, negligence or other tortious action,
 * arising out of or in connection with the use or performance of
 * this software.
 */
namespace JsonRpc\Transport;

/**
 * 
 * Input implementation reading standard post (the default)
 *
 */
class BasicInput
implements Input
{
	/**
	 * @var string $payload
	 */
    protected $payload;
    /**
     * Tries to get the payload from either HTTP_RAW_POST_DATA or STDIN
     * @see JsonRpc\Transport.Input::getPayload()
     * @return string
     */
    public function getPayload()
    {
        if (null === $this->payload) {
            if (isset($HTTP_RAW_POST_DATA) && !empty($HTTP_RAW_POST_DATA)) {
                $this->payload = $HTTP_RAW_POST_DATA;
            } else {
                $this->payload = file_get_contents('php://input');
            }
        }
        return $this->payload;
    }
}
