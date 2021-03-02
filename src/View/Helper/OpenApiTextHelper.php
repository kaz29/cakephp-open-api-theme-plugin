<?php
declare(strict_types=1);

namespace OpenApiTheme\View\Helper;

use Cake\Utility\Inflector;
use Cake\View\Helper;

class OpenApiTextHelper extends Helper
{
    public function singularize(string $value): string
    {
        return Inflector::singularize($value);
    }

    public function lower(string $value): string
    {
        return strtolower($value);
    }

    public function prefixToPath(string $prefix): string
    {
        return strtolower(substr($prefix, 1));
    }
}
