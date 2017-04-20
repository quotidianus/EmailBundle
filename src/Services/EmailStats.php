<?php

namespace Librinfo\EmailBundle\Services;

class EmailStats
{

    /**
     * Returns an array with all the stats concerning $email
     * 
     * @param Email $email
     * @return Array
     */
    public function getStats($email)
    {
        $stats = array();
        $recipients = explode(';', $email->getFieldTo());
        $linkStats = self::getLinkStats($email->getContent(), $email->getLinks(), $recipients);

        $stats['receipts'] = self::successRate($recipients, $email->getReceipts());

        $stats['links']['average'] = self::linkSuccessRate($linkStats);

        $stats['links']['mostClicked'] = self::mostClicked($linkStats);

        $stats['links']['leastClicked'] = self::leastClicked($linkStats);

        return $stats;
    }

    /**
     * Returns the percentage of read receipts from the number of recepients
     * 
     * @param Array $recipients
     * @param Array $receipts
     * @return int
     */
    protected function successRate($recipients, $receipts)
    {

        return number_format(self::getPercentage(count($recipients), count($receipts)), 0);
    }

    /**
     * Returns average percentage of total email links clicked
     * 
     * @param Array $linkStats
     * @return int
     */
    protected function linkSuccessRate($linkStats)
    {
        if (!$linkStats)
            return 0;

        return number_format(self::getAverage($linkStats), 0);
    }

    /**
     * Returns the url of the most clicked link with its average rate
     * 
     * @param type $linkStats
     * @return type
     */
    protected function mostClicked($linkStats)
    {
        $mostClicked = 0;

        foreach ($linkStats as $stat)
            if ($stat > $mostClicked)
                $mostClicked = $stat;

        return array(
            'link' => array_search($mostClicked, $linkStats),
            'value' => $mostClicked
                )
        ;
    }

    /**
     * Returns the url of the least clicked link with its average rate
     * 
     * @param type $linkStats
     * @return type
     */
    protected function leastClicked($linkStats)
    {
        if (!$linkStats)
            return array(
                'link' => '',
                'value' => 0
                    )
            ;

        $leastClicked = 100;

        foreach ($linkStats as $stat)
            if ($stat < $leastClicked)
                $leastClicked = $stat;

        return array(
            'link' => array_search($leastClicked, $linkStats),
            'value' => $leastClicked
                )
        ;
    }

    /**
     * Returns an array with individual success rates of all links in the email
     * 
     * @param String $content
     * @param Array $links
     * @param Array $recipients
     * @return Array
     */
    protected function getLinkStats($content, $links, $recipients)
    {
        $results = array();

        foreach (self::getClickCount($content, $links) as $key => $value)
        {
            $results[$key] = number_format(self::getPercentage(count($recipients), $value), 0);
        }

        return $results;
    }

    /**
     * Returns an array with individual click count of all links in the email
     * 
     * @param String $content
     * @param Array $links
     * @return int
     */
    protected function getClickCount($content, $links)
    {
        $clicks = array();

        foreach ($links as $link)
        {
            $uri = $link->getDestination();

            if (!isset($clicks[$uri]))
            {
                $clicks[$uri] = 1;
            } else
            {
                $clicks[$uri] ++;
            }
        }

        $emailLinks = array();

        preg_match_all('!<a\s(.*)href="(http.*)"(.*)>(.*)</a>!U', $content, $emailLinks, PREG_SET_ORDER);

        //put links that were not clicked to 0;
        foreach ($emailLinks as $emailLink)
        {
            $uri = $emailLink[2];

            if (!isset($clicks[$uri]))
                $clicks[$uri] = 0;
        }

        return $clicks;
    }

    /**
     * 
     * @param Float $total
     * @param int $number
     * @return int
     */
    protected function getPercentage($total, $number = 0)
    {

        if ($number === 0 || $total === 0)
            return 0;

        return ( $number / $total ) * 100;
    }

    /**
     * 
     * @param Array $values
     * @return int
     */
    protected function getAverage($values)
    {
        if (!$values)
            return 0;

        return array_sum($values) / count($values);
    }

}
