<?php

/**
 * @package     WT SEO Meta templates
 * @version     2.0.3
 * @Author      Sergey Tolkachyov, https://web-tolk.ru
 * @copyright   Copyright (C) 2023 Sergey Tolkachyov
 * @license     GNU/GPL 3
 * @since       1.0.0
 */

namespace Joomla\Plugin\System\Wt_seo_meta_templates\Extension;

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Profiler\Profiler;
use Joomla\Event\SubscriberInterface;

class Wt_seo_meta_templates extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;
    protected $allowLegacyListeners = false;

    /**
     *
     * @return array
     *
     * @throws \Exception
     * @since 4.1.0
     *
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'onBeforeCompileHead' => 'onBeforeCompileHead'
        ];
    }

    public function onBeforeCompileHead() : void
    {
        //Работаем только на фронте
        $app = $this->getApplication();
        if (!$app->isClient('site')) {
            return;
        }
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: start');
        $doc = $app->getDocument();
        // получаем переменные от сторонних плагинов
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: Before additional plugins import');
        $results = $app->triggerEvent('onWt_seo_meta_templatesAddVariables', array());
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After additional plugins import');

        $allVariables = array();

        if (is_array($results)) {
            foreach ($results as $result) {
                if (is_array($result)) {
                    // Загружаем переменные из плагинов
                    if (is_array($result['variables'])) {

                        foreach ($result['variables'] as $variable_array) {
                            $allVariables[] = (object)$variable_array;
                        }
                    }

                    // Загружаем тайтл, если пришёл
                    if (is_array($result['seo_tags_templates']) && array_key_exists('title', $result['seo_tags_templates'])) {
                        $title = $result['seo_tags_templates']['title'];
                    }
                    // Загружаем дескрипшн, если пришёл
                    if (is_array($result['seo_tags_templates']) && array_key_exists('description', $result['seo_tags_templates'])) {
                        $description = $result['seo_tags_templates']['description'];
                    }

                }
            }
        }
        $allVariables = (object)$allVariables;

        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: Before getHeadData');
        $head = $doc->getHeadData();

        // Если есть формулы тайтлов и дескрипшнов из плагинов - заменяем их.
        if (!empty($title)) {
            $head['title'] = $title;
        }
        if (!empty($description)) {
            $head['description'] = $description;
        }

        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After getHeadData');
        //Обрабатываем шорт-коды
        foreach ($allVariables as $variable) {
            $head['title'] = str_replace('{' . strtoupper($variable->variable) . '}', (!empty($variable->value) ? $variable->value : ''), $head['title']);
            if (!empty($head['description'])) {
                $head['description'] = str_replace('{' . strtoupper($variable->variable) . '}', (!empty($variable->value) ? $variable->value : ''), $head['description']);
            }
        }

        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: After variables replace');
        $doc->setHeadData($head);
        !JDEBUG ?: Profiler::getInstance('Application')->mark('<strong>plg WT SEO Meta templates</strong>: end');

    }

    /**
     * Show debug info for WT SEO Meta templates plugins
     * on frontend
     * @return void
     * @throws \Exception
     * @since 2.0.0
     */
    public function onAfterRender(): void
    {
        $app = $this->getApplication();
        if (!$app->isClient('site')) {
            return;
        }

        $doc = $app->getDocument();
        if (!($doc instanceof \Joomla\CMS\Document\HtmlDocument)) {
            return;
        }

        $session = $app->getSession();
        $debug_info = $session->get("wtseometatemplatesdebugoutput");
        if (empty($debug_info)) {
            return;
        }
        $buffer = $app->getBody();
        $html = [];
        if ($this->params->get('show_debug') == 0) {
            return;
        }

        $html[] = "<details style='border:1px solid #0FA2E6; margin-bottom:5px; padding:10px;'>";
        $html[] = "<summary style='background-color:#384148; color:#fff; padding:10px;'>WT SEO Meta templates debug information</summary>";
        $html[] = $debug_info;
        $html[] = '</details>';
        $session->clear("wtseometatemplatesdebugoutput");

        if (!empty($html)) {
            $buffer = preg_replace('/(<body.*>)/Ui', '$1' . implode('', $html), $buffer);
            $app->setBody($buffer);
        }
    }
}