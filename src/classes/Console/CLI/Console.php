<?php
/**
 * Class Console
 */
namespace hcore\cli;

class Console
{

    /**
     * @var array
     */
    private $foreground_colors = array();

    /**
     * @var array
     */
    private $background_colors = array();

    public function __construct()
    {
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    /**
     * @param $string
     * @param null $foreground_color
     * @param null $background_color
     * @return string
     */
    private function getColoredString(string $string, ?string $foreground_color = null, ?string $background_color = null):string
    {
        $colored_string = "";

        // Check if given foreground color found
        if (isset($this->foreground_colors[$foreground_color])) {
            $colored_string .= "\033[" . $this->foreground_colors[$foreground_color] . "m";
        }
        // Check if given background color found
        if (isset($this->background_colors[$background_color])) {
            $colored_string .= "\033[" . $this->background_colors[$background_color] . "m";
        }

        // Add string and end coloring
        $colored_string .=  $string . "\033[0m";

        return $colored_string;
    }

    /**
     * Get Foreground Colors
     * @return array
     */
    public function getForegroundColors():array
    {
        return array_keys($this->foreground_colors);
    }

    /**
     * Get Background Colors
     * @return array
     */
    public function getBackgroundColors():array
    {
        return array_keys($this->background_colors);
    }

    /**
     * Display Message
     *
     * @example display('hello', 'green')
     *
     * @param string $message
     * @param string|null $foreground_color
     * @param string|null $background_color
     *
     * @return self
     */
    public function display(string $message, string $foreground_color = null, string $background_color = null):self
    {
        print $this->getColoredString($message, $foreground_color, $background_color);
        return $this;
    }

    /**
     * short Display Message
     * @param string $message
     * @param string|null $foreground_color
     * @param string|null $background_color
     * @return $this
     */
    public function d(string $message, string $foreground_color = null, string $background_color = null):Console
    {
        print $this->getColoredString($message, $foreground_color, $background_color);
        return $this;
    }

    public function nl(int $lines = 1):Console
    {
        for ($i = 0; $i < $lines; $i++) {
            echo "\n";
        }
        return $this;
    }
    public function space(int $rows = 1):Console
    {
        for ($i = 0; $i < $rows; $i++) {
            print " ";
        }
        return $this;
    }

    public function displayError(string $message):Console
    {
        print $this->getColoredString($message, 'red');
        print $this->getColoredString(PHP_EOL); // Fix the red cursor
        return $this;
    }

    public function displaySuccess(string $message):Console
    {
        print $this->getColoredString($message . PHP_EOL, 'green');
        print $this->getColoredString(PHP_EOL); // Fix the coloured cursor
        return $this;
    }
}
