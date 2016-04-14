<?php

namespace Librinfo\EmailBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Librinfo\UserBundle\Entity\User;

/**
 * Send Emails from the spool.
 */
class SpoolSendCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('librinfo:spool:send')
            ->setDescription('Sends emails from the spool')
            ->addOption('message-limit', 0, InputOption::VALUE_OPTIONAL, 'The maximum number of messages to send.')
            ->addOption('time-limit', 0, InputOption::VALUE_OPTIONAL, 'The time limit for sending messages (in seconds).')
            ->addOption('recover-timeout', 0, InputOption::VALUE_OPTIONAL, 'The timeout for recovering messages that have taken too long to send (in seconds).')
            ->addOption('mailer', null, InputOption::VALUE_OPTIONAL, 'The mailer name.')
            ->setHelp(<<<EOF
The <info>librinfo:spool:send</info> command sends all emails from the spool.
<info>php %command.full_name% --message-limit=10 --time-limit=10 --recover-timeout=900 --mailer=default</info>
EOF
            )
        ;
    }
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $user = new User();
        $user->setUsername("spool");
        $user->setPlainPassword("spool");
        $user->setEnabled(true);
        $user->setSuperAdmin(true);

        $token = new UsernamePasswordToken($user,null,'main',$user->getRoles());

        $this->getContainer()->get('security.context')->setToken($token);

        $name = $input->getOption('mailer');
        if ($name) {
            $this->processMailer($name, $input, $output);
        } else {
            $mailers = array_keys($this->getContainer()->getParameter('swiftmailer.mailers'));
            foreach ($mailers as $name) {
                $this->processMailer($name, $input, $output);
            }
        }
    }
    private function processMailer($name, $input, $output)
    {
        if (!$this->getContainer()->has(sprintf('swiftmailer.mailer.%s', $name))) {
            throw new \InvalidArgumentException(sprintf('The mailer "%s" does not exist.', $name));
        }
        $output->write(sprintf('<info>[%s]</info> Processing <info>%s</info> mailer... ', date('Y-m-d H:i:s'), $name));
        if ($this->getContainer()->getParameter(sprintf('swiftmailer.mailer.%s.spool.enabled', $name))) {
            $mailer = $this->getContainer()->get(sprintf('swiftmailer.mailer.%s', $name));
            $transport = $mailer->getTransport();
            if ($transport instanceof \Swift_Transport_SpoolTransport) {
                $spool = $transport->getSpool();
                if ($spool instanceof \Swift_ConfigurableSpool) {
                    $spool->setMessageLimit($input->getOption('message-limit'));
                    $spool->setTimeLimit($input->getOption('time-limit'));
                }

                $sent = $spool->flushQueue($this->getContainer()->get(sprintf('swiftmailer.mailer.%s.transport.real', $name)));
                $output->writeln(sprintf('<comment>%d</comment> emails sent', $sent));
            }
        } else {
            $output->writeln('No email to send as the spool is disabled.');
        }
    }
}
