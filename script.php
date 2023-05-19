<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

class plgSystemWt_seo_meta_templatesInstallerScript
{
	/**
	 * Runs just before any installation action is performed on the component.
	 * Verifications and pre-requisites should run in this function.
	 *
	 * @param   string     $type    - Type of PreFlight action. Possible values are:
	 *                              - * install
	 *                              - * update
	 *                              - * discover_install
	 * @param   \stdClass  $installer  - Parent object calling object.
	 *
	 * @return void
	 */
	public function preflight($type, $installer)
	{
        if ((new Version())->isCompatible('4.0') === false)
        {
            Factory::getApplication()->enqueueMessage('&#128546; <strong>WT SEO Meta templates - tags</strong> plugin doesn\'t support Joomla versions <span class="alert-link">lower 4</span>. Your Joomla version is <span class="badge badge-important">'.(new Version())->getShortVersion().'</span>','error');
            return false;
        }
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


	/**
	 * Runs right after any installation action is performed on the component.
	 *
	 * @param   string     $type       - Type of PostFlight action. Possible values are:
	 *                                 - * install
	 *                                 - * update
	 *                                 - * discover_install
	 * @param   \stdClass  $installer  - Parent object calling object.
	 *
	 * @return void
	 */
	function postflight($type, $installer)
	{
		$app   = Factory::getApplication();
		$smile = '';
		if ($type != 'uninstall')
		{
			$smiles    = ['&#9786;', '&#128512;', '&#128521;', '&#128525;', '&#128526;', '&#128522;', '&#128591;'];
			$smile_key = array_rand($smiles, 1);
			$smile     = $smiles[$smile_key];
		}

        $element            = strtoupper($installer->getElement());
        $class              = 'col-';
        $web_tolk_site_icon = '';


		echo "
		<style>	.thirdpartyintegration {
				display:flex;
				padding: 8px 12px;
				align-items:center;
			}
			.thirdpartyintegration-logo {
				height:32px;
				float:left; 
				margin-right: 5px;
			}
			
			.thirdpartyintegration.success {
				border: 1px solid #2F6F2F;
				background-color:#dfffdf;
			}
			.thirdpartyintegration.error {
				border: 1px solid #bd362f;
				background-color:#ffdddb;
			}
		</style>
		<div class='row bg-white' style='margin:25px auto; border:1px solid rgba(0,0,0,0.125); box-shadow:0px 0px 10px rgba(0,0,0,0.125); padding: 10px 20px;'>
		<div class='" . $class . "8'>
		<h2>".$smile." " . Text::_("PLG_" . strtoupper($element) . "_AFTER_" . strtoupper($type)) . " <br/>" . Text::_("PLG_" . strtoupper($element)) . "</h2>
		" . Text::_("PLG_" . strtoupper($element) . "_DESC");


		echo Text::_("PLG_" . strtoupper($element) . "_WHATS_NEW");


		$thirdpartyextensions = "";

		/**
		 * Joomla articles (com_content)
		 */

		$thirdpartyextensions .= "<div class='thirdpartyintegration success'><span class='thirdpartyintegration-logo'>Joomla Content</span>
										<div class='media-body'>
										<p>Plugin <code>System - WT SEO Meta templates - Content</code> for Joomla Content categories and articles.</p>
										</div>
									</div>";

		$com_content_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_content';
		if (!$this->installDependencies($installer, $com_content_url))
		{

			$app->enqueueMessage(
				Text::sprintf('WT SEO Meta templates - Content not installed or updated',
					Text::_('Cannot install or update the data-provider plugin for Joomla Articles. PLease, <a href="' . $com_content_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
				), 'error'
			);

		}

		/**
		 * Virtuemart
		 */

		if (file_exists(JPATH_ADMINISTRATOR . "/components/com_virtuemart/virtuemart.xml"))
		{


			$virtuemart           = simplexml_load_file(JPATH_ADMINISTRATOR . "/components/com_virtuemart/virtuemart.xml");
			$thirdpartyextensions .= "<div class='thirdpartyintegration success'><img class='thirdpartyintegration-logo' src='" . JUri::root(true) . "/administrator/components/com_virtuemart/assets/images/vm_menulogo.png'/>
										<div class='media-body'><strong>" . $virtuemart->author . "'s</strong> <strong>" . $virtuemart->name . " v." . $virtuemart->version . "</strong> detected. <a href='" . $virtuemart->authorUrl . "' target='_blank'>" . $virtuemart->authorUrl . "</a> <a href='mailto:" . $virtuemart->authorEmail . "' target='_blank'>" . $virtuemart->authorEmail . "</a>
										<p>Use plugin <code>System - WT SEO Meta templates - Virtuemart</code>.</p>
										</div>
									</div>";

			// Install Virtuemart data-provider plugin
			$virtuemart_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_virtuemart';
			if (!$this->installDependencies($installer, $virtuemart_url))
			{

				$app->enqueueMessage(
					Text::sprintf('WT SEO Meta templates - Virtuemart not installed or updated',
						Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="' . $virtuemart_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
					), 'error'
				);

			}
		}

		/**
		 * JoomShopping
		 */

		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_jshopping/jshopping.xml'))
		{
			$jshop                = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_jshopping/jshopping.xml');
			$thirdpartyextensions .= "<div class='thirdpartyintegration success'><img class='thirdpartyintegration-logo' src='" . JUri::root(true) . "/administrator/components/com_jshopping/images/jshop_logo.jpg'/>
													<div class='media-body'><strong>" . $jshop->author . "'s</strong> <strong>" . $jshop->name . " v." . $jshop->version . "</strong> detected. <a href='" . $jshop->authorUrl . "' target='_blank'>" . $jshop->authorUrl . "</a> <a href='mailto:" . $jshop->authorEmail . "' target='_blank'>" . $jshop->authorEmail . "</a>
													<p>Use plugin <code>System - WT SEO Meta templates - JoomShopping</code>.</p>
													</div>
												</div>";


			// Install JoomShopping data-provider pkugin
			$jshop_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_joomshopping';
			if (!$this->installDependencies($installer, $jshop_url))
			{
				$app->enqueueMessage(
					Text::sprintf('WT SEO Meta templates - JoomShopping not installed or updated',
						Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="' . $jshop_url . '" class="btn btn-small btn-primary">download</a> it and install/update manually.')
					), 'error'
				);
			}
		}

		/**
		 * Phoca Gallery
		 */

		$com_phocagallery_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_phoca_gallery';
		if (file_exists(JPATH_ADMINISTRATOR . '/components/com_phocagallery/phocagallery.xml'))
		{
			$phocagallery         = simplexml_load_file(JPATH_ADMINISTRATOR . '/components/com_phocagallery/phocagallery.xml');
			$thirdpartyextensions .= "<div class='thirdpartyintegration success'><img class='thirdpartyintegration-logo' src='" . Uri::root(true) . "/media/com_phocagallery/images/administrator/logo-phoca.png'/>
													<div class='media-body'><strong>" . $phocagallery->author . "'s</strong> <strong>" . $phocagallery->name . " v." . $phocagallery->version . "</strong> detected. <a href='" . $phocagallery->authorUrl . "' target='_blank'>" . $phocagallery->authorUrl . "</a> <a href='mailto:" . $phocagallery->authorEmail . "' target='_blank'>" . $phocagallery->authorEmail . "</a>
													<p>Use plugin <code>System - WT SEO Meta templates - Phoca Gallery</code>.</p>
													</div>
												</div>";


			if (!$this->installDependencies($installer, $com_phocagallery_url))
			{
				$app->enqueueMessage(
					Text::sprintf('WT SEO Meta templates - Phoca Gallery not installed or updated',
						Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="' . $com_phocagallery_url . '" class="btn btn-small btn-sm btn-primary">download</a> it and install/update manually.')
					), 'error'
				);
			}
		}


		/**
		 * My City Selector package
		 */

		if (file_exists(JPATH_SITE . "/administrator/manifests/packages/pkg_mycityselector.xml"))
		{

			$mcs = simplexml_load_file(JPATH_SITE . "/administrator/manifests/packages/pkg_mycityselector.xml");
			// Install My City Selector data-provider plugin
			$mcs_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_mcs';
			if (!$this->installDependencies($installer, $mcs_url))
			{
				$app->enqueueMessage(
					Text::sprintf('WT SEO Meta templates - My City Selector not installed or updated',
						Text::_('Cannot install or update the data-provider plugin for My City Selector. PLease, download it and install/update manually. ' . $mcs_url)
					), 'error'
				);

			}

			$mcs_min_version     = '3.0.77';
			$mcs_version_compare = version_compare($mcs_min_version, $mcs->version, '<=');
			if ($mcs_version_compare == true)
			{
				$bg_color_css_class = 'success';
				$note               = '';
			}
			else
			{
				$bg_color_css_class = 'warning';
				$note               = "Note, You can only use the names of countries, provinces, and cities in one case in versions earlier <strong>" . $mcs_min_version . "</strong>";
			}

			$thirdpartyextensions .= "<div class='thirdpartyintegration " . $bg_color_css_class . "'><span class='thirdpartyintegration-logo'>MyCitySelector</span>
											<div class='media-body'><strong>" . $mcs->author . "'s</strong> extension <strong>" . $mcs->name . " v." . $mcs->version . "</strong> detected. <a href='" . $mcs->authorUrl . "' target='_blank'>" . $mcs->authorUrl . "</a> <a href='mailto:" . $mcs->authorEmail . "' target='_blank'>" . $mcs->authorEmail . "</a><p>" . $note . "</p>
											<p>Use plugin <code>System - WT SEO Meta templates - My City Selector</code>.</p>
											</div>
										</div>";
		}


		echo "<h4>Supported third-party extensions was found</h4>" . $thirdpartyextensions;


		echo "</div>
		<div class='" . $class . "4' style='display:flex; flex-direction:column; justify-content:center;'>
		<img width='200px' src='https://web-tolk.ru/web_tolk_logo_wide.png'>
		<p>Joomla Extensions</p>
		<p class='btn-group'>
			<a class='btn btn-sm btn-outline-primary' href='https://web-tolk.ru' target='_blank'>" . $web_tolk_site_icon . " https://web-tolk.ru</a>
			<a class='btn btn-sm btn-outline-primary' href='mailto:info@web-tolk.ru'><i class='icon-envelope'></i> info@web-tolk.ru</a>
		</p>
		<p><a class='btn btn-info' href='https://t.me/joomlaru' target='_blank'>Joomla Russian Community in Telegram</a></p>
		" . Text::_("PLG_" . strtoupper($element) . "_MAYBE_INTERESTING") . "
		</div>
		";

	}
}