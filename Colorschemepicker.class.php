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
        $navigation->setImage(Icon::create('group4', Icon::ROLE_NAVIGATION));
        $navigation->setDescription(_('Alle Farben des Stud.IP Farbklimas'));

        if (Navigation::hasItem('/contents')) {
            Navigation::addItem('/contents/colorschemepicker', $navigation);
        } elseif (Navigation::hasItem('/tools')) {
            Navigation::addItem('/tools/colorschemepicker', $navigation);
        } elseif (Navigation::hasItem('/profile/settings')) {
            Navigation::addItem('/profile/settings/colorschemepicker', $navigation);
        }
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
        if (Navigation::hasItem('/contents/colorschemepicker')) {
            Navigation::activateItem('/contents/colorschemepicker');
        } elseif (Navigation::hasItem('/tools/colorschemepicker')) {
            Navigation::activateItem('/tools/colorschemepicker');
        } else {
            Navigation::activateItem('/profile/settings/colorschemepicker');
        }

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
