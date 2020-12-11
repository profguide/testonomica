<?php
/**
 * @author: adavydov
 * @since: 11.12.2020
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FunctionsExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('progress', [$this, 'progress']),
        ];
    }

    public function progress(int $value, $label = null)
    {
        return '<b>' . $label . '</b><div class="progress">
                <div class="progress-bar" role="progressbar" style="width: ' . $value . '%;">' . $value . '%</div>
                </div>';
    }
}