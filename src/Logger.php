<?php

namespace Arrilot\BitrixMigrations;

class Logger
{
    const COLOR_BLACK = '0;30';
    const COLOR_DARK_GRAY = '1;30';
    const COLOR_BLUE = '0;34';
    const COLOR_LIGHT_BLUE = '1;34';
    const COLOR_GREEN = '0;32';
    const COLOR_LIGHT_GREEN = '1;32';
    const COLOR_CYAN = '0;36';
    const COLOR_LIGHT_CYAN = '1;36';
    const COLOR_RED = '0;31';
    const COLOR_LIGHT_RED = '1;31';
    const COLOR_PURPLE = '0;35';
    const COLOR_LIGHT_PURPLE = '1;35';
    const COLOR_BROWN = '0;33';
    const COLOR_YELLOW = '1;33';
    const COLOR_LIGHT_GRAY = '0;37';
    const COLOR_WHITE = '1;37';

    public static function log($string, $foreground_color = null)
    {
        $colored_string = "";

        if ($foreground_color) {
            $colored_string .= "\033[" . $foreground_color . "m";
        }

        $colored_string .= $string . "\033[0m\n";

        fwrite(STDOUT, $colored_string);
    }
}
