<?php
/**
 * @package       WT SEO Meta templates
 * @version       2.0.3
 * @Author        Sergey Tolkachyov, https://web-tolk.ru
 * @copyright     Copyright (C) 2023 Sergey Tolkachyov
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-3.0.html
 * @since         1.0.0
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Filesystem\Path;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseDriver;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(InstallerScriptInterface::class, new class ($container->get(AdministratorApplication::class)) implements InstallerScriptInterface {
            /**
             * The application object
             *
             * @var  AdministratorApplication
             *
             * @since  1.0.0
             */
            protected AdministratorApplication $app;

            /**
             * The Database object.
             *
             * @var   DatabaseDriver
             *
             * @since  1.0.0
             */
            protected DatabaseDriver $db;

            /**
             * Minimum Joomla version required to install the extension.
             *
             * @var  string
             *
             * @since  1.0.0
             */
            protected string $minimumJoomla = '4.3';

            /**
             * Minimum PHP version required to install the extension.
             *
             * @var  string
             *
             * @since  1.0.0
             */
            protected string $minimumPhp = '7.4';

            /**
             * Constructor.
             *
             * @param AdministratorApplication $app The application object.
             *
             * @since 1.0.0
             */
            public function __construct(AdministratorApplication $app)
            {
                $this->app = $app;
                $this->db = Factory::getContainer()->get('DatabaseDriver');
            }

            /**
             * Function called after the extension is installed.
             *
             * @param InstallerAdapter $adapter The adapter calling this method
             *
             * @return  boolean  True on success
             *
             * @since   1.0.0
             */
            public function install(InstallerAdapter $adapter): bool
            {
                //$this->enablePlugin($adapter);

                return true;
            }

            /**
             * Function called after the extension is updated.
             *
             * @param InstallerAdapter $adapter The adapter calling this method
             *
             * @return  boolean  True on success
             *
             * @since   1.0.0
             */
            public function update(InstallerAdapter $adapter): bool
            {
                return true;
            }

            /**
             * Function called after the extension is uninstalled.
             *
             * @param InstallerAdapter $adapter The adapter calling this method
             *
             * @return  boolean  True on success
             *
             * @since   1.0.0
             */
            public function uninstall(InstallerAdapter $adapter): bool
            {

                return true;
            }

            /**
             * Function called before extension installation/update/removal procedure commences.
             *
             * @param string $type The type of change (install or discover_install, update, uninstall)
             * @param InstallerAdapter $adapter The adapter calling this method
             *
             * @return  boolean  True on success
             *
             * @since   1.0.0
             */
            public function preflight(string $type, InstallerAdapter $adapter): bool
            {
                return true;
            }

            /**
             * Function called after extension installation/update/removal procedure commences.
             *
             * @param string $type The type of change (install or discover_install, update, uninstall)
             * @param InstallerAdapter $adapter The adapter calling this method
             *
             * @return  boolean  True on success
             *
             * @since   1.0.0
             */
            public function postflight(string $type, InstallerAdapter $adapter): bool
            {
                $smile = '';

                if ($type !== 'uninstall') {
                    if ($type != 'uninstall') {
                        $smiles = ['&#9786;', '&#128512;', '&#128521;', '&#128525;', '&#128526;', '&#128522;', '&#128591;'];
                        $smile_key = array_rand($smiles, 1);
                        $smile = $smiles[$smile_key];
                    }
                } else {
                    $smile = ':(';
                }

                $element = strtoupper($adapter->getElement());
                $type = strtoupper($type);

                $html = '
				<div class="row bg-white m-0">
				<div class="col-12 col-md-8 p-0 pe-2">
				<h2>' . $smile . ' ' . Text::_($element . '_AFTER_' . $type) . ' <br/>' . Text::_($element) . '</h2>
				' . Text::_($element . '_DESC');

                $html .= Text::_($element . '_WHATS_NEW');

                $uri_root = Uri::root(true);

                /**
                 * Joomla articles (com_content)
                 */

                $thirdpartyextensions = "
                <table class='table table-striped table-bordered'>
                    <caption>Provider pLugins for WT SEO Meta templates</caption>
                    <tbody>
                    %s
                </tbody>
                </table>";


                $com_content_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_content';
                if (!$this->installDependencies($adapter, $com_content_url))
                {

                    $this->app->enqueueMessage(
                        Text::sprintf('WT SEO Meta templates - Content not installed or updated',
                            Text::_('Cannot install or update the data-provider plugin for Joomla Articles. PLease, <a href="' . $com_content_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
                        ), 'error'
                    );
                    $installed_message = '<span class="badge bg-danger">not installed</span>';
                } else {
                    $installed_message = '<span class="badge bg-success">installed</span>';
                }
                $thirdpartyextensions_tbody = '
            <tr>
                <td><span class="badge bg-primary">Joomla Content</span></td>
                <td>'.$installed_message.'</td>
                <td>Plugin <code>System - WT SEO Meta templates - Content</code> for Joomla Content categories and articles.</td>
            </tr>
        ';

                $com_tags_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_tags';
                if (!$this->installDependencies($adapter, $com_tags_url))
                {

                    $this->app->enqueueMessage(
                        Text::sprintf('WT SEO Meta templates - Tags not installed or updated',
                            Text::_('Cannot install or update the data-provider plugin for Joomla Tags. PLease, <a href="' . $com_content_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
                        ), 'error'
                    );

                    $installed_message = '<span class="badge bg-danger">not installed</span>';
                } else {
                    $installed_message = '<span class="badge bg-success">installed</span>';
                }
                $thirdpartyextensions_tbody .= '
            <tr>
                <td><span class="badge bg-primary">Joomla Tags</span></td>
                <td>'.$installed_message.'</td>
                <td>Plugin <code>System - WT SEO Meta templates - Tags</code> for Joomla Tags list and items list by tag.</td>
            </tr>
        ';
                /**
                 * Virtuemart
                 */

                if (file_exists(JPATH_ADMINISTRATOR . "/components/com_virtuemart/virtuemart.xml"))
                {


                    $virtuemart           = simplexml_load_file(JPATH_ADMINISTRATOR . "/components/com_virtuemart/virtuemart.xml");
                    // Install Virtuemart data-provider plugin
                    $virtuemart_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_virtuemart';
                    if (!$this->installDependencies($adapter, $virtuemart_url))
                    {

                        $this->app->enqueueMessage(
                            Text::sprintf('WT SEO Meta templates - Virtuemart not installed or updated',
                                Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="' . $virtuemart_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
                            ), 'error'
                        );

                        $installed_message = '<span class="badge bg-danger">not installed</span>';
                    } else {
                        $installed_message = '<span class="badge bg-success">installed</span>.';
                    }

                    $thirdpartyextensions_tbody .= "
                <tr>
                    <td>
                        <img class='thirdpartyintegration-logo' src='$uri_root/administrator/components/com_virtuemart/assets/images/vm_menulogo.png'/>
                    </td>
                    <td>$installed_message</td>
                    <td><strong>$virtuemart->author</strong> <strong>$virtuemart->name v.$virtuemart->version</strong> detected. <a href='$virtuemart->authorUrl' target='_blank'>$virtuemart->authorUrl</a> <a href='mailto:$virtuemart->authorEmail' target='_blank'>$virtuemart->authorEmail</a>
                        <p>Plugin <code>System - WT SEO Meta templates - Virtuemart</code>.</p>
                    </td>
                </tr>    
            ";
                }

                /**
                 * JoomShopping
                 */

                if (file_exists(JPATH_ADMINISTRATOR . '/components/com_jshopping/jshopping.xml'))
                {
                    $jshop                = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_jshopping/jshopping.xml');
                    // Install JoomShopping data-provider pkugin
                    $jshop_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_joomshopping';
                    if (!$this->installDependencies($adapter, $jshop_url))
                    {
                        $this->app->enqueueMessage(
                            Text::sprintf('WT SEO Meta templates - JoomShopping not installed or updated',
                                Text::_('Cannot install or update the data-provider plugin for JoomShopping. PLease, <a href="' . $jshop_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
                            ), 'error'
                        );
                        $installed_message = '<span class="badge bg-danger">not installed</span>';
                    } else {
                        $installed_message = '<span class="badge bg-success">installed</span>';
                    }

                    $thirdpartyextensions_tbody .= "
                <tr>
                    <td>
                        <img class='thirdpartyintegration-logo' src='$uri_root/administrator/components/com_jshopping/images/joomshopping.png'/>
                    </td>
                    <td>$installed_message</td>
                    <td><strong>$jshop->author</strong> <strong>$jshop->name v.$jshop->version</strong> detected. <a href='$jshop->authorUrl' target='_blank'>$jshop->authorUrl</a> <a href='mailto:$jshop->authorEmail' target='_blank'>$jshop->authorEmail</a>
                        <p>Plugin <code>System - WT SEO Meta templates - JoomShopping</code></p>
                    </td>
                </tr>    
            ";
                }

                /**
                 * Phoca Gallery
                 */

                $com_phocagallery_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_phoca_gallery';
                if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocagallery/phocagallery.xml'))
                {
                    $phocagallery         = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_phocagallery/phocagallery.xml');

                    if (!$this->installDependencies($adapter, $com_phocagallery_url))
                    {
                        $this->app->enqueueMessage(
                            Text::sprintf('WT SEO Meta templates - Phoca Gallery not installed or updated',
                                Text::_('Cannot install or update the data-provider plugin for Phoca Gallery. PLease, <a href="' . $com_phocagallery_url . '" class="btn btn-small btn-sm btn-primary">download</a> it and install/update manually.')
                            ), 'error'
                        );
                        $installed_message = '<span class="badge bg-danger">not installed</span>';
                    } else {
                        $installed_message = '<span class="badge bg-success">installed</span>.';
                    }

                    $thirdpartyextensions_tbody .= "
                <tr>
                    <td>
                        <img class='thirdpartyintegration-logo' src='$uri_root/media/com_phocagallery/images/administrator/icon-phoca-logo-seal.png'/>
                    </td>
                    <td>$installed_message</td>
                    <td><strong>$phocagallery->author</strong> <strong>$phocagallery->name v.$phocagallery->version</strong> detected. <a href='$phocagallery->authorUrl' target='_blank'>$phocagallery->authorUrl</a> <a href='mailto:$phocagallery->authorEmail' target='_blank'>$phocagallery->authorEmail</a>
                        <p>Plugin <code>System - WT SEO Meta templates - Phoca Gallery</code>.</p>
                    </td>
                </tr>    
            ";
                }


                /**
                 * My City Selector package
                 */

                if (file_exists(JPATH_SITE . "/administrator/manifests/packages/pkg_mycityselector.xml"))
                {

                    $mcs = simplexml_load_file(JPATH_SITE . "/administrator/manifests/packages/pkg_mycityselector.xml");
                    // Install My City Selector data-provider plugin
                    $mcs_url = 'https://web-tolk.ru/get?element=wt_seo_meta_templates_mcs';
                    if (!$this->installDependencies($adapter, $mcs_url))
                    {
                        $this->app->enqueueMessage(
                            Text::sprintf('WT SEO Meta templates - My City Selector not installed or updated',
                                Text::_('Cannot install or update the data-provider plugin for My City Selector. PLease, download it and install/update manually. ' . $mcs_url)
                            ), 'error'
                        );

                        $installed_message = '<span class="badge bg-danger">not installed</span>';
                    } else {
                        $installed_message = '<span class="badge bg-success">installed</span>.';
                    }

                    $note='';
                    $mcs_min_version     = '3.0.77';
                    $mcs_version_compare = version_compare($mcs_min_version, $mcs->version, '<=');
                    if ($mcs_version_compare !== true)
                    {
                        $note               = "Note, You can only use the names of countries, provinces, and cities in one case in versions earlier <strong>" . $mcs_min_version . "</strong>";
                    }

                    $thirdpartyextensions_tbody .= "
                <tr>
                    <td>
                        <span class='badge bg-primary'>MCS</span>
                    </td>
                    <td>$installed_message</td>
                    <td><strong>$mcs->author</strong> <strong>$mcs->name v.$mcs->version</strong> detected. <a href='$mcs->authorUrl' target='_blank'>$mcs->authorUrl</a> <a href='mailto:$mcs->authorEmail' target='_blank'>$mcs->authorEmail</a>
                        <p>Plugin <code>System - WT SEO Meta templates - My City Selector</code>. $note.</p>
                    </td>
                </tr>    
            ";
                }

                $thirdpartyextensions = sprintf($thirdpartyextensions,$thirdpartyextensions_tbody);
                echo "<h4>Supported third-party extensions was found</h4>" . $thirdpartyextensions;


                $html .= '</div>
				<div class="col-12 col-md-4 p-0 d-flex flex-column justify-content-start">
				<img width="180" src="https://web-tolk.ru/web_tolk_logo_wide.png">
				<p>Joomla Extensions</p>
				<p class="btn-group">
					<a class="btn btn-sm btn-outline-primary" href="https://web-tolk.ru" target="_blank"> https://web-tolk.ru</a>
					<a class="btn btn-sm btn-outline-primary" href="mailto:info@web-tolk.ru"><i class="icon-envelope"></i> info@web-tolk.ru</a>
				</p>
				<p><a class="btn btn-danger w-100" href="https://t.me/joomlaru" target="_blank">'.Text::_($element.'_JOOMLARU_TELEGRAM_CHAT').'</a></p>
				' . Text::_($element . "_MAYBE_INTERESTING") . '
				</div>

				';
                $this->app->enqueueMessage($html, 'info');
                return true;
            }


            /**
             * Enable plugin after installation.
             *
             * @param InstallerAdapter $adapter Parent object calling object.
             *
             * @since  1.0.0
             */
            protected function enablePlugin(InstallerAdapter $adapter)
            {
                // Prepare plugin object
                $plugin = new \stdClass();
                $plugin->type = 'plugin';
                $plugin->element = $adapter->getElement();
                $plugin->folder = (string)$adapter->getParent()->manifest->attributes()['group'];
                $plugin->enabled = 1;

                // Update record
                $this->db->updateObject('#__extensions', $plugin, ['type', 'element', 'folder']);
            }


            /**
             * @param $parent
             *
             * @throws Exception
             *
             *
             * @return bool
             * @since 1.0.0
             */
            protected function installDependencies($parent, $url)
            {
                // Load installer plugins for assistance if required:
                PluginHelper::importPlugin('installer');

                $app = Factory::getApplication();

                $package = null;

                // This event allows an input pre-treatment, a custom pre-packing or custom installation.
                // (e.g. from a JSON description).
                $results = $app->triggerEvent('onInstallerBeforeInstallation', array($this, &$package));

                if (in_array(true, $results, true))
                {
                    return true;
                }

                if (in_array(false, $results, true))
                {
                    return false;
                }


                // Download the package at the URL given.
                $p_file = InstallerHelper::downloadPackage($url);

                // Was the package downloaded?
                if (!$p_file)
                {
                    $app->enqueueMessage(Text::_('COM_INSTALLER_MSG_INSTALL_INVALID_URL'), 'error');

                    return false;
                }

                $config   = Factory::getConfig();
                $tmp_dest = $config->get('tmp_path');

                // Unpack the downloaded package file.
                $package = InstallerHelper::unpack($tmp_dest . '/' . $p_file, true);

                // This event allows a custom installation of the package or a customization of the package:
                $results = $app->triggerEvent('onInstallerBeforeInstaller', array($this, &$package));

                if (in_array(true, $results, true))
                {
                    return true;
                }

                if (in_array(false, $results, true))
                {
                    InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

                    return false;
                }

                // Get an installer instance.
                $installer = new Installer();

                /*
                 * Check for a Joomla core package.
                 * To do this we need to set the source path to find the manifest (the same first step as JInstaller::install())
                 *
                 * This must be done before the unpacked check because JInstallerHelper::detectType() returns a boolean false since the manifest
                 * can't be found in the expected location.
                 */
                if (is_array($package) && isset($package['dir']) && is_dir($package['dir']))
                {
                    $installer->setPath('source', $package['dir']);

                    if (!$installer->findManifest())
                    {
                        InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
                        $app->enqueueMessage(Text::sprintf('COM_INSTALLER_INSTALL_ERROR', '.'), 'warning');

                        return false;
                    }
                }

                // Was the package unpacked?
                if (!$package || !$package['type'])
                {
                    InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);
                    $app->enqueueMessage(Text::_('COM_INSTALLER_UNABLE_TO_FIND_INSTALL_PACKAGE'), 'error');

                    return false;
                }

                // Install the package.
                if (!$installer->install($package['dir']))
                {
                    // There was an error installing the package.
                    $msg     = Text::sprintf('COM_INSTALLER_INSTALL_ERROR',
                        Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
                    $result  = false;
                    $msgType = 'error';
                }
                else
                {
                    // Package installed successfully.
                    $msg     = Text::sprintf('COM_INSTALLER_INSTALL_SUCCESS',
                        Text::_('COM_INSTALLER_TYPE_TYPE_' . strtoupper($package['type'])));
                    $result  = true;
                    $msgType = 'message';
                }

                // This event allows a custom a post-flight:
                $app->triggerEvent('onInstallerAfterInstaller', array($parent, &$package, $installer, &$result, &$msg));

                $app->enqueueMessage($msg, $msgType);

                // Cleanup the install files.
                if (!is_file($package['packagefile']))
                {
                    $package['packagefile'] = $config->get('tmp_path') . '/' . $package['packagefile'];
                }

                InstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

                return $result;
            }
        });
    }
};