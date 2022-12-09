<?php
/**
 * @package     WT SEO Meta templates
 * @version     1.4.2
 * @Author 		Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2022 Sergey Tolkachyov
 * @license     GNU/GPL 3
 * @since 		1.0.0
 */
// No direct access
defined( '_JEXEC' ) or die;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Profiler\Profiler;

class plgSystemWt_seo_meta_templates extends CMSPlugin
{
	protected $autoloadLanguage = true;

	public function onBeforeCompileHead(){
		//Работаем только на фронте
		$app = Factory::getApplication();
		if(!$app->isClient('site')){
			return;
		}
		!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: start');
		$doc = Factory::getApplication()->getDocument();
			// получаем переменные от сторонних плагинов
			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: Before additional plugins import');
				$results = $app->triggerEvent('onWt_seo_meta_templatesAddVariables',array());
			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After additional plugins import');

			$allVariables = array();

			if (is_array($results))
			{
				foreach ($results as $result)
				{
					if (is_array($result))
					{
						// Загружаем переменные из плагинов
						if (is_array($result['variables'])){

							foreach ($result['variables'] as $variable_array){
								$allVariables[] = (object)$variable_array;
							}
						}

						// Загружаем тайтл, если пришёл
						if (is_array($result['seo_tags_templates']) && array_key_exists('title',$result['seo_tags_templates'])){
							$title = $result['seo_tags_templates']['title'];
						}
						// Загружаем дескрипшн, если пришёл
						if (is_array($result['seo_tags_templates']) && array_key_exists('description',$result['seo_tags_templates'])){
							$description = $result['seo_tags_templates']['description'];
						}

					}
				}
			}
			$allVariables = (object)$allVariables;

			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: Before getHeadData');
			$head = $doc->getHeadData();

			// Если есть формулы тайтлов и дескрипшнов из плагинов - заменяем их.
			if(!empty($title)){
				$head['title'] = $title;
			}
			if(!empty($description)){
				$head['description'] = $description;
			}

			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After getHeadData');
			//Обрабатываем шорт-коды
			foreach ($allVariables as $variable){
				$head['title'] = str_replace('{' . strtoupper($variable->variable) . '}', $variable->value, $head['title']);
				$head['description'] = str_replace('{' . strtoupper($variable->variable) . '}', $variable->value, $head['description']);
				$head['metaTags']['name']['keywords'] = str_replace('{' . strtoupper($variable->variable) . '}', $variable->value, $head['metaTags']['name']['keywords']);
			}

			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After variables replace');
			$doc->setHeadData($head);
			!JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: end');

	}




}//plgSystemWt_seo_meta_templates