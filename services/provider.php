<?php
/**
 * @package    WT SEO Meta templates
 * @subpackage WT SEO Meta templates - tags
 * @copyright  Copyright Sergey Tolkachyov
 * @license    GNU General Public License version 3, or later
 * @link       https://web-tolk.ru
 */

defined('_JEXEC') || die;

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Wt_seo_meta_templates\Extension\Wt_seo_meta_templates;

return new class implements ServiceProviderInterface {
    /**
     * Registers the service provider with a DI container.
     *
     * @param Container $container The DI container.
     *
     * @return  void
     *
     * @since   4.0.0
     */
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $subject = $container->get(DispatcherInterface::class);
                $config = (array)PluginHelper::getPlugin('system', 'wt_seo_meta_templates');
                $plugin = new Wt_seo_meta_templates($subject, $config);
                $plugin->setApplication(\Joomla\CMS\Factory::getApplication());
                return $plugin;
            }
        );
    }
};