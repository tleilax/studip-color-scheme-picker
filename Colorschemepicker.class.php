<?php
/**
 * Colorschemepicker.class.php
 *
 * @author  Jan-Hendrik Willms <tleilax+studip@gmail.com>
 * @version 2.0
 * @license GPL2 or any later version
 */
class Colorschemepicker extends StudIPPlugin implements SystemPlugin
{
    /**
     * Initialize this plugin.
     */
    public function __construct()
    {
        parent::__construct();

        $navigation = new Navigation(_('Farbklima'));
        $navigation->setURL(PluginEngine::getURL($this, [], 'index'));

        Navigation::addItem('/tools/colorschemepicker', $navigation);
    }

    /**
     * Return plugin name
     * @return String containing the plugin name
     */
    public function getPluginName()
    {
        return _('Farbklima');
    }

    /**
     * Show the Picker
     */
    public function index_action()
    {
        Navigation::activateItem('/tools/colorschemepicker');

        $this->addStylesheet('assets/style.scss');
        $this->addScript('assets/script.js');

        $widget = Sidebar::get()->addWidget(new SearchWidget());
        $widget->setTitle(_('Farbwert suchen'));
        $widget->addNeedle('#abcdef', 'search-color', true, null, null, null, [
            'pattern' => '#?[a-fA-F0-9]{6}'
        ]);

        $factory = new Flexi_TemplateFactory($this->getPluginPath() . '/templates');
        echo $factory->render(
            'index.php',
            [],
            $GLOBALS['template_factory']->open('layouts/base.php')
        );
    }
}
