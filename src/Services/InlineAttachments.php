<?php

namespace Librinfo\EmailBundle\Services;

use Swift_Message;
use Swift_Attachment;

class InlineAttachments {

    /**
     * Turns inline attachments into links to attachments
     *
     * @param Email $email
     * @return Array
     */
    public function handle($content, Swift_Message $message)
    {   
        preg_match_all('!<img\s(.*)src="data:(image/\w+);base64,(.*)" alt="(.*)" (.*)/>!U', $content, $imgs, PREG_SET_ORDER);
        
        foreach ($imgs as $i => $img) {
            $att = Swift_Attachment::newInstance()
                    ->setFileName($img[4] . '.' . str_replace('image/', '', $img[2]))
                    ->setContentType($img[2])
                    ->setDisposition('inline')
                    ->setBody(base64_decode($img[3]))
                    ->setId("img$i.$i@librinfo_email")
            ;

            // embedding the image
            $content = str_replace(
                    $img[0], '<img ' . $img[1] . ' ' . $img[5] . ' src="' . $message->embed($att) . '" />', $content
            );
        }
        
        return $content;
    }

}
