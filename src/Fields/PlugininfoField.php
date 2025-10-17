<?php
/**
 * @package     WT SEO Meta templates
 * @version     2.0.4
 * @Author 		Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2023 Sergey Tolkachyov
 * @license     GNU/GPL 3
 * @since 		1.0.0
 */

namespace Joomla\Plugin\System\Wt_seo_meta_templates\Fields;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\NoteField;
use Joomla\CMS\Language\Text;

\defined('_JEXEC') or die;


class PlugininfoField extends NoteField
{

	protected $type = 'Plugininfo';


    /**
     * @return  string  The field label markup.
     *
     * @throws \Exception
     * @since   1.7.0
     */
    protected function getInput(): string
    {
        $data = $this->form->getData();
        $element = $data->get('element');
        $folder = $data->get('folder');
        $wt_plugin_info = simplexml_load_file(JPATH_SITE . "/plugins/" . $folder . "/" . $element . "/" . $element . ".xml");

        /* @var $wa \Joomla\CMS\WebAsset\WebAssetManager */
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        $wa->addInlineStyle('
            #web_tolk_link {
			text-align: center;
			}
			#web_tolk_link::before{
				content: "";
			}
        ');

        return '
        <div class="d-flex shadow p-4">
			<div class="flex-shrink-0">
				<a href="https://web-tolk.ru" target="_blank">
					<svg class="plugin-info-img-svg" width="200" height="50" xmlns="http://www.w3.org/2000/svg">
						<g>
							<title>Go to https://web-tolk.ru</title>
							<text font-weight="bold" xml:space="preserve" text-anchor="start"
							      font-family="Helvetica, Arial, sans-serif" font-size="32" id="svg_3" y="36.085949"
							      x="8.152073" stroke-opacity="null" stroke-width="0" stroke="#000"
							      fill="#0fa2e6">Web</text>
							<text font-weight="bold" xml:space="preserve" text-anchor="start"
							      font-family="Helvetica, Arial, sans-serif" font-size="32" id="svg_4" y="36.081862"
							      x="74.239105" stroke-opacity="null" stroke-width="0" stroke="#000"
							      fill="#384148">Tolk</text>
						</g>
					</svg>
				</a>
			</div>
			<div class="flex-grow-1 ms-3">
				<span class="badge bg-success text-white">v.' . $wt_plugin_info->version . '</span>
				' . Text::_("PLG_" . strtoupper($element) . "_DESC") . '
			</div>
		</div>';
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

}
