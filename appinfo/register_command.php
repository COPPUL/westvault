<?php

use OCA\WestVault\Command\Deposit;
use OCA\WestVault\Command\Status;

/** @var $application Symfony\Component\Console\Application */
$application->add(new Deposit());
$application->add(new Status());