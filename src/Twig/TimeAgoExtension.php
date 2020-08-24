<?php

namespace App\Twig;

use App\Utils\DateUtils;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TimeAgoExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [DateUtils::class, 'getTimeAgo']),
        ];
    }
}
