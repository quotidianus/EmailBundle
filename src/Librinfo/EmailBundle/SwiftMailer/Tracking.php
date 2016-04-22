<?php

namespace Librinfo\EmailBundle\SwiftMailer;

use Symfony\Component\DependencyInjection\ContainerAware;

class Tracking extends ContainerAware
{

    public function addTracking($content, $address, $emailId)
    {
        $updatedContent = $this->processLinks($content, $address, $emailId) . $this->getTracker($address, $emailId);

        return $updatedContent;
    }

    private function processLinks($content, $address, $emailId)
    {
        $links = array();

        preg_match_all('!<a\s(.*)href="(http.*)"(.*)>(.*)</a>!U', $content, $links, PREG_SET_ORDER);

        foreach ($links as $link)
        {
            $content = str_replace(
                    $link[0], '<a ' . $link[1] . 'href="http://cube.office.libre-informatique.fr:8000/app_dev.php/tracking/'
                    . $emailId . '/' . $address . '/' . base64_encode($link[2]) . '" '
                    . $link[3] . '>' . $link[4] . '</a>', $content
            );
        }

        return $content;
    }

    private function getTracker($address, $emailId)
    {
        return '<img src="http:localhost:8000/app_dev.php/tracking/' .
                $emailId . '/' .
                $address .
                '.png" alt="logo" width="1" height="1"/>'
        ;
    }

}
