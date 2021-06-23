<?php
/**
 * @package     WT SEO Meta templates
 * @version     1.0.0
 * @Author 		Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2021 Sergey Tolkachyov
 * @license     GNU/GPL 3
 * @since 		1.0.0
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Language\Text;
use \Joomla\CMS\Factory;
FormHelper::loadFieldClass('spacer');

class JFormFieldPlugininfo extends JFormFieldSpacer
{

	protected $type = 'plugininfo';

	/**
	 * Method to get the field input markup for a spacer.
	 * The spacer does not have accept input.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.7.0
	 */
	protected function getInput()
	{
		return ' ';
	}

	/**
	 * @return  string  The field label markup.
	 *
	 * @since   1.7.0
	 */
	protected function getLabel()
	{
		$info="";
		$doc = Factory::getDocument();
		$doc->addStyleDeclaration("
			.webtolk-plugin-info{
				box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); 
				padding:1rem; 
				margin-bottom:2 rem;
			}
		");

		$wt_plugin_info = simplexml_load_file(JPATH_SITE."/plugins/system/wt_seo_meta_templates/wt_seo_meta_templates.xml");


		?>
        <div class="media webtolk-plugin-info">
			<?php if(file_exists(JPATH_SITE.'/plugins/system/wt_seo_meta_templates/img/web_tolk_logo_wide.png')):?>
                <img class="media-object pull-left" src="../plugins/system/wt_seo_meta_templates/img/web_tolk_logo_wide.png"/>
			<?php endif;?>
            <div class="media-body">
                <span class="label label-success">v.<?php echo $wt_plugin_info->version; ?></span>
				<?php echo Text::_("PLG_WT_SEO_META_TEMPLATES_DESC"); ?>
            </div>
        </div>
		<?php

	}

	/**
	 * Method to get the field title.
	 *
	 * @return  string  The field title.
	 *
	 * @since   1.7.0
	 */
	protected function getTitle()
	{
		return $this->getLabel();
	}

	/**
	 * Method to get a control group with label and input.
	 *
	 * @param   array  $options  Options to be passed into the rendering of the field
	 *
	 * @return  string  A string containing the html for the control group
	 *
	 * @since   3.7.3
	 */
	public function renderField($options = array())
	{
		$options['class'] = empty($options['class']) ? 'field-spacer' : $options['class'] . ' field-spacer';

		return parent::renderField($options);
	}
}
?>