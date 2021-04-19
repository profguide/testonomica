<?php
/**
 * @author: adavydov
 * @since: 11.12.2020
 */

namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class FunctionsExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('progress', [$this, 'progress']),
            new TwigFunction('progress_value_percentage', [$this, 'progressValuePercentage']),
            new TwigFunction('thumbnails', [$this, 'thumbnails']),
        ];
    }

    public function progressValuePercentage(array $data, $label = null): Markup
    {
        $string = '<div class="progress">
            <div class="progress-bar" role="progressbar" 
                 style="width: ' . $data['percentage_value'] . '%;"
                 aria-valuenow="' . $data['percentage_value'] . '" 
                 aria-valuemin="0"
                 aria-valuemax="100">' . $data['percentage_value'] . '% 
            </div>
        </div>';
        if ($label) {
            $string = "<b>{$label}</b>" . $string;
        }
        return new Markup($string, 'UTF-8');
    }

    public function progress(int $value, $label = null)
    {
        return '<b>' . $label . '</b><div class="progress">
                <div class="progress-bar" role="progressbar" style="width: ' . $value . '%;">' . $value . '%</div>
                </div>';
    }

    public function thumbnails(?string $path): string
    {
        if ($path == null) {
            return 'images/sys/noimage_1000.png';
        } else {
            return 'images/thumbnails/' . $path;
        }
    }
}