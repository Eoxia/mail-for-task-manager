<?php
/**
 * Find attachment and body content of mail.
 * @param  object $mbox link to the mail box connection
 * @param  integer $mid the id of the mail we're fetching.
 * @return array description]
 */
 function _get_body_attach( $mbox, $mid ) {
    $struct = imap_fetchstructure( $mbox, $mid );
    $parts = $struct->parts;
    $i = 0;
    if (!$parts) { /* Simple message, only 1 piece */
        $attachment = array(); /* No attachments */
        $content = imap_body($mbox, $mid);
    } else { /* Complicated message, multiple parts */

        $endwhile = false;

        $stack = array(); /* Stack while parsing message */
        $content = "";    /* Content of message */
        $attachment = array(); /* Attachments */

        while (!$endwhile) {
            if (!$parts[$i]) {
                if (count($stack) > 0) {
                    $parts = $stack[count($stack)-1]["p"];
                    $i     = $stack[count($stack)-1]["i"] + 1;
                    array_pop($stack);
                } else {
                    $endwhile = true;
                }
            }

            if (!$endwhile) {
                /* Create message part first (example '1.2.3') */
                $partstring = "";
                foreach ($stack as $s) {
                    $partstring .= ($s["i"]+1) . ".";
                }
                $partstring .= ($i+1);

                if (strtoupper($parts[$i]->disposition) == "ATTACHMENT" || strtoupper($parts[$i]->disposition) == "INLINE") { /* Attachment or inline images */
                    $filedata = imap_fetchbody($mbox, $mid, $partstring, FT_PEEK);
                    if ( $filedata != '' ) {
                        // handles base64 encoding or plain text
                        $decoded_data = base64_decode($filedata);
                        if ( $decoded_data == false ) {
                            $attachment[] = array("filename" => $parts[$i]->parameters[0]->value,
                                "filedata" => $filedata);
                        } else {
                            $attachment[] = array("filename" => $parts[$i]->parameters[0]->value,
                                "filedata" => $decoded_data);
                        }
                    }
                } elseif (strtoupper($parts[$i]->subtype) == "PLAIN" && strtoupper($parts[$i+1]->subtype) != "HTML") { /* plain text message */
                    $content .= imap_fetchbody($mbox, $mid, $partstring, FT_PEEK);
                } elseif ( strtoupper($parts[$i]->subtype) == "HTML" ) {
                    /* HTML message takes priority */
                    $content .= imap_fetchbody($mbox, $mid, $partstring, FT_PEEK );
                }
            }

            if ($parts[$i]->parts) {
                if ( $parts[$i]->subtype != 'RELATED' ) {
                    // a glitch: embedded email message have one additional stack in the structure with subtype 'RELATED', but this stack is not present when using imap_fetchbody() to fetch parts.
                    $stack[] = array("p" => $parts, "i" => $i);
                }
                $parts = $parts[$i]->parts;
                $i = 0;
            } else {
                $i++;
            }
        } /* while */
    } /* complicated message */

    $ret = array();
    $ret['body'] = quoted_printable_decode($content);
    $ret['attachment'] = $attachment;
    return $ret;
}
