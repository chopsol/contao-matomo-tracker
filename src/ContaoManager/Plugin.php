<?php

namespace Chopsol\ContaoMatomoTracker\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;

use Chopsol\ContaoMatomoTracker\ContaoMatomoTracker;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser)
    {
        return [
            BundleConfig::create(ContaoMatomoTracker::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
