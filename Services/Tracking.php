<?php

namespace Librinfo\EmailBundle\Services;

class Tracking
{
    private $router;
    
    /**
     * @param Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    
    /**
     * Adds tracking to an Email
     * 
     * @param String $content
     * @param String $address
     * @param String $emailId
     * @return string email content updated with tracking info
     */
    public function addTracking($content, $address, $emailId)
    {
        $updatedContent = $this->processLinks($content, $address, $emailId) . $this->getTracker($address, $emailId);

        return $updatedContent;
    }

    /**
     * Parse links in the content and redirect them to track clicks
     * 
     * @param type $content
     * @param type $address
     * @param type $emailId
     * @return type
     */
    private function processLinks($content, $address, $emailId)
    {
        $links = array();

        preg_match_all('!<a\s(.*)href="(http.*)"(.*)>(.*)</a>!U', $content, $links, PREG_SET_ORDER);
        
        foreach ($links as $link)
        {
            $url = $this->router->generate('librinfo_email.track_links', [
                'emailId'     => $emailId,
                'recipient'   => $address,
                'destination' => base64_encode($link[2])
            ]);
            
            $content = str_replace($link[0], 
                    '<a' . $link[1] .
                    'href="' . $url . '"' .
                    $link[3] . '>' .
                    $link[4] . '</a>', 
                $content
            );   
//            $content = str_replace(
//                    $link[0], '<a ' . $link[1] . 'href="http://cube.office.libre-informatique.fr:8000/app_dev.php/tracking/'
//                    . $emailId . '/' . $address . '/' . base64_encode($link[2]) . '" '
//                    . $link[3] . '>' . $link[4] . '</a>', $content
//            );
        }
        return $content;
    }

    /**
     * Add 1*1 img to track opened emails
     * 
     * @param type $address
     * @param type $emailId
     * @return type
     */
    private function getTracker($address, $emailId)
    {
        $url = $this->router->generate('librinfo_email.track_opens', [
            'emailId'   => $emailId,
            'recipient' => $address
        ]);
        
        return '<img src="' . $url . '.png" alt="logo" widht="1" height="1"/>';
//        return '<img src="http:localhost:8000/app_dev.php/tracking/' .
//                $emailId . '/' .
//                $address .
//                '.png" alt="logo" width="1" height="1"/>'
//        ;
    }

}
