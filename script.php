<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Script file of HelloWorld component.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_helloWorldInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>script.php</scriptfile>
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class plgSystemWt_seo_meta_templatesInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function install($parent)
    {

    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $parent - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($parent) 
    {

		
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function update($parent) 
    {

    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $parent) 
    {

    }
	/**
	 * @param $parent
	 *
	 * @return bool
	 * @throws Exception
	 *
	 *
	 * @since 1.0.0
	 */
	protected function installDependencies($parent,$url)
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
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $parent - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $parent) 
    {

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
		<div class='row' style='margin:25px auto; border:1px solid rgba(0,0,0,0.125); box-shadow:0px 0px 10px rgba(0,0,0,0.125); padding: 10px 20px;'>
		<div class='span8 control-group' id='wt_download_id_form_wrapper'>
		<h2>".JText::_("PLG_".strtoupper($parent->get("element"))."_AFTER_".strtoupper($type))." <br/>".JText::_("PLG_".strtoupper($parent->get("element")))."</h2>
		".Text::_("PLG_".strtoupper($parent->get("element"))."_DESC");
		
		
			echo JText::_("PLG_".strtoupper($parent->get("element"))."_WHATS_NEW");


		    $thirdpartyextensions="";
			
			


			$thirdpartyextensions .=  "<div class='thirdpartyintegration success'><span class='thirdpartyintegration-logo'>Joomla Content</span>
										<div class='media-body'>
										<p>Plugin <code>System - WT SEO Meta templates - Content</code> for Joomla Content categories and articles.</p>
										</div>
									</div>";

		    // Install Virtuemart data-provider pkugin
		    $com_content_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_content';
		    if (!$this->installDependencies($parent,$com_content_url))
		    {
			    $app = Factory::getApplictaion();
			    $app->enqueueMessage(
				    Text::sprintf('WT SEO Meta templates - Content not installed or updated',
					    Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="'.$com_content_url.'" class="btn btn-small btn-primary">download</a> it and install/update manually.')
				    ), 'error'
			    );

		    }

	
			

	   if(file_exists(JPATH_SITE."/components/com_virtuemart/virtuemart.php")){

		    if(file_exists(JPATH_ADMINISTRATOR.'/manifests/packages/pkg_virtuemart.xml')){
			    $virtuemart = simplexml_load_file(JPATH_SITE.'/administrator/manifests/packages/pkg_virtuemart.xml');
			    $thirdpartyextensions .=  "<div class='thirdpartyintegration success'><img class='thirdpartyintegration-logo' src='".JUri::root(true)."/administrator/components/com_virtuemart/assets/images/vm_menulogo.png'/>
											<div class='media-body'><strong>".$virtuemart->author."'s</strong> <strong>".$virtuemart->name." v.".$virtuemart->version."</strong> detected. <a href='".$virtuemart->authorUrl."' target='_blank'>".$virtuemart->authorUrl."</a> <a href='mailto:".$virtuemart->authorEmail."' target='_blank'>".$virtuemart->authorEmail."</a>
											<p>Use plugin <code>System - WT SEO Meta templates - Virtuemart</code>.</p>
											</div>
										</div>";
		    }

		    // Install Virtuemart data-provider pkugin
		    $virtuemart_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_virtuemart';
		    if (!$this->installDependencies($parent,$virtuemart_url))
		    {
			    $app = Factory::getApplictaion();
			    $app->enqueueMessage(
				    Text::sprintf('WT SEO Meta templates - Virtuemart not installed or updated',
					    Text::_('Cannot install or update the data-provider plugin for Virtuemart. PLease, <a href="'.$virtuemart_url.'" class="btn btn-small btn-primary">download</a> it and install/update manually.')
				    ), 'error'
			    );

		    }

	    }

	    if(file_exists(JPATH_SITE."/administrator/manifests/packages/pkg_mycityselector.xml")){

		    $mcs = simplexml_load_file(JPATH_SITE."/administrator/manifests/packages/pkg_mycityselector.xml");



		    // Install My City Selector data-provider plugin
		    $mcs_url = 'https://web-tolk.ru/get.html?element=wt_seo_meta_templates_mcs';
		    if (!$this->installDependencies($parent,$mcs_url))
		    {
			    $app = Factory::getApplictaion();
			    $app->enqueueMessage(
				    Text::sprintf('WT SEO Meta templates - My City Selector not installed or updated',
					    Text::_('Cannot install or update the data-provider plugin for My City Selector. PLease, download it and install/update manually. '.$mcs_url)
				    ), 'error'
			    );

		    }

		    $mcs_min_version = '3.0.77';
		    $mcs_version_compare = version_compare($mcs_min_version,$mcs->version,'<=');
		    if($mcs_version_compare  == true){
			    $bg_color_css_class = 'success';
				$note = '';
		    }else{
			    $bg_color_css_class = 'warning';
			    $note="Note, You can only use the names of countries, provinces, and cities in one case in versions earlier <strong>".$mcs_min_version."</strong>";
		    }

		    $thirdpartyextensions .=  "<div class='thirdpartyintegration ".$bg_color_css_class."'><span class='thirdpartyintegration-logo'>MyCitySelector</span>
											<div class='media-body'><strong>".$mcs->author."'s</strong> extension <strong>".$mcs->name." v.".$mcs->version."</strong> detected. <a href='".$mcs->authorUrl."' target='_blank'>".$mcs->authorUrl."</a> <a href='mailto:".$mcs->authorEmail."' target='_blank'>".$mcs->authorEmail."</a><p>".$note."</p>
											<p>Use plugin <code>System - WT SEO Meta templates - My City Selector</code>.</p>
											</div>
										</div>";
	    }


			echo "<h4>Supported third-party extensions was found</h4>".$thirdpartyextensions;



		echo "</div>
		<div class='span4' style='display:flex; flex-direction:column; justify-content:center;'>
		<img width='200px' src='https://web-tolk.ru/web_tolk_logo_wide.png'>
		<p>Joomla Extensions</p>
		<p><a class='btn' href='https://web-tolk.ru' target='_blank'><i class='icon-share-alt'></i> https://web-tolk.ru</a> <a class='btn' href='mailto:info@web-tolk.ru'><i class='icon-envelope'></i>  info@web-tolk.ru</a></p>
		".JText::_("PLG_".strtoupper($parent->get("element"))."_MAYBE_INTERESTING")."
		</div>


		";		
	
    }
}