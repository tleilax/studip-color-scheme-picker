<?php
/**
 * Colorschemepicker.class.php
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @version 1.0
 * @license GPL2 or any later version
 */
class Colorschemepicker extends StudIPPlugin implements SystemPlugin
{
    private $colors = null;

    /**
     * Initialize this plugin.
     */
    public function __construct()
    {
        parent::__construct();

        $navigation = new Navigation(_('Farbklima'));
        $navigation->setURL(PluginEngine::getLink($this, array(), 'search'));
        
        $navigation->addSubnavigation('search', new Navigation('Suche', PluginEngine::getLink($this, array(), 'search')));
        $navigation->addSubnavigation('list', new Navigation('Liste', PluginEngine::getLink($this, array(), 'list')));

        Navigation::addItem('/tools/colorschemepicker', $navigation);

        $this->template_factory = new Flexi_TemplateFactory($this->getPluginPath() . '/templates');
    }

    /**
     * Return plugin name
     * @return String containing the plugin name
     */
    public function getPluginName()
    {
        return _('Farbklima');
    }

    private function getTemplate($template)
    {
        PageLayout::addStylesheet($this->getPluginURL() . '/assets/style.css');

        $template = $this->template_factory->open($template);
        $template->set_layout($GLOBALS['template_factory']->open('layouts/base.php'));
        return $template;
    }

    /**
     * Show the Picker
     */
    public function search_action()
    {
        Navigation::activateItem('/tools/colorschemepicker/search');

        $template = $this->getTemplate('search.php');
        $template->colors = $this->getColors();
        $template->action_url = PluginEngine::getLink($this, array(), 'search');

        if (!empty($_REQUEST['color'])) {
            $color               = $_REQUEST['color'];
            $distances           = $this->getDistances($color);

            $template->color     = $color;
            $template->distances = array_slice($distances, 0, 10);
        }

        echo $template->render();
    }

    /**
     * Lists all available colors from the color scheme.
     */
    public function list_action()
    {
        Navigation::activateItem('/tools/colorschemepicker/list');

        $template = $this->getTemplate('list.php');
        $template->colors = $this->getColors();
        echo $template->render();
    }

    /**
     * Converts a hex color (#rrggbb) to it's channels (r, g, b, mono).
     * 'mono' indicates the monochrome factor used for the distance
     * calculation.
     *
     * @param String $color Hex color input
     * @return Array of channels (r, g, b, mono)
     */
    private function hex2rgb($color)
    {
        $color = trim($color);
        $color = ltrim($color, '#');
        $color = strtolower($color);
        list($r, $g, $b) = array_map('hexdec', str_split($color, 2));
        $mono = 0.2125 * $r + 0.7154 * $g + 0.0721 * $b;

        return compact('r', 'g', 'b', 'mono');
    }

    /**
     * Calculates the distance of two colors.
     * The Distance is defined as the square root the squared differences
     * of all channels, expect for the mono channel which is not squared
     * to add less weight to the distance.
     *
     * @param String $color0 First color
     * @param String $color1 Second color
     * @return float Distance between the two colors
     */
    private function getDistance($color0, $color1)
    {
        $rgb0 = $this->hex2rgb($color0);
        $rgb1 = $this->hex2rgb($color1);

        $dist_r    = abs($rgb0['r'] - $rgb1['r']);
        $dist_g    = abs($rgb0['g'] - $rgb1['g']);
        $dist_b    = abs($rgb0['b'] - $rgb1['b']);
        $dist_mono = abs($rgb0['mono'] - $rgb1['mono']);

        return sqrt($dist_r * $dist_r + $dist_g * $dist_g + $dist_b * $dist_b + $dist_mono);
    }

    /**
     * Calculates the distances of all available stud.ip colors to the given
     * color.
     * The distance of each of stud.ip's color is calculated and put in an
     * array. The resulting is sorted ascendingly by the distance.
     *
     * @param String $color The color in RGB form
     * @return Array containg all calculated distances
     */
    private function getDistances($color)
    {
        $colors = $this->getColors();

        $distances = array();
        foreach ($colors as $index => $probe) {
            $distances[$index] = $this->getDistance($color, $probe);
        }
        asort($distances);

        return $distances;
    }

    /**
     * Extracts the colors from the according mixin file from Stud.IP.
     *
     * @return Array containing all colors in an associative manner, index
     *         is the name of the variable and the value is the rgb color
     *         value
     */
    private function getColors()
    {
        if ($this->colors !== null) {
            return $this->colors;
        }
        
        $less_file = $GLOBALS['ABSOLUTE_PATH_STUDIP'] . 'assets/stylesheets/mixins/colors.less';
        
        if (!file_exists($less_file)) {
            throw new Exception('Stud.IP mixin "colors.less" not found in expected location');
        }

        $this->colors = array();

        $fp = fopen($less_file, 'r');
        while (!feof($fp)) {
            $line = fgets($fp);
            $line = trim($line);
            
            // @brand-color-darker: #1e3e70;
            if (!preg_match('/(@\S+)\s*:.*?(#[a-f0-9]{3}(?:[a-f0-9]{3})?);?$/', $line, $matches)) {
                continue;
            }

            $index = $matches[1];
            $color = $matches[2];
            
            $this->colors[$index] = $color;
        }

        return $this->colors;
    }
}
