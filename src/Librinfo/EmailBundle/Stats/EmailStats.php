<?php

namespace Librinfo\EmailBundle\Stats;

/**
 * Description of StatCalculator
 *
 * @author romain
 */
class EmailStats
{

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

    protected function successRate($recipients, $receipts)
    {

        return number_format(self::getPercentage(count($recipients), count($receipts)), 0);
    }

    protected function linkSuccessRate($linkStats)
    {

        return number_format(self::getAverage($linkStats), 0);
    }

    protected function mostClicked($linkStats)
    {
        $mostClicked = 0;

        foreach ($linkStats as $stat)
            if ($stat > $mostClicked)
                $mostClicked = $stat;

        return array(
            array_search($mostClicked, $linkStats) => $mostClicked
                )
        ;
    }

    protected function leastClicked($linkStats)
    {
        $leastClicked = 100;

        foreach ($linkStats as $stat)
            if ($stat < $leastClicked)
                $leastClicked = $stat;

        return array(
            array_search($leastClicked, $linkStats) => $leastClicked
                )
        ;
    }

    protected function getLinkStats($content, $links, $recipients)
    {
        $results = array();

        foreach (self::getClickCount($content, $links) as $key => $value)
        {
            $results[$key] = number_format(self::getPercentage(count($recipients), $value), 0);
        }
        dump($results);
        return $results;
    }

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

    protected function getPercentage($total, $number = 0)
    {

        if ($number === 0)
            return 0;

        return ( $number / $total ) * 100;
    }

    protected function getAverage($values)
    {

        return array_sum($values) / count($values);
    }

    protected function getClickStats($recipients, $links)
    {
        $results = array();

        foreach ($recipients as $recipient)
        {
            $count = 0;

            foreach ($links as $link)
            {
                if ($recipient == $link->getAddress())
                    $count ++;
            }

            if ($count > 0)
                $results[$link->getDestination()] = self::getPercentage(count($links), $count);
        }

        return $results;
    }

}
