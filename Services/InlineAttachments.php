<?php

namespace Librinfo\EmailBundle\Services;

class InlineAttachments {

    /**
     * Turns inline attachments into links to attachments
     *
     * @param Email $email
     * @return Array
     */
    public function handle($email)
    {
        $post_treated_content = $this->content;
        preg_match_all('!<img\s(.*)src="data:(image/\w+);base64,(.*)"(.*)/>!U', $post_treated_content, $imgs, PREG_SET_ORDER);
        foreach ($imgs as $i => $img) {
            $att = Swift_Attachment::newInstance()
                    ->setFileName("img-$i." . str_replace('image/', '', $img[2]))
                    ->setContentType($img[2])
                    ->setDisposition('inline')
                    ->setBody(base64_decode($img[3]))
                    ->setId("img$i.$i@e-venement")
            ;

            // embedding the image
            $post_treated_content = str_replace(
                    $img[0], '<img ' . $img[1] . ' ' . $img[4] . ' src="' . $this->message->embed($att) . '" />(.*)</a>', $post_treated_content
            );
        }
    }
}
