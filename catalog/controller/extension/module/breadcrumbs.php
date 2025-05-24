<?php
/**
 * Генерирует базовые хлебные крошки с возможностью добавления дополнительных пунктов
 * 
 * @param array $additional Массив дополнительных элементов крошек
 * @param bool $includeAccount Включать ли пункт "Аккаунт" (по умолчанию true)
 * @return array
 */
class ControllerExtensionModuleBreadcrumbs extends Controller {
    public function getBasicBreadcrumbs(array $additional = []) {
        {
            $breadcrumbs = [
                [
                    'text' => $this->language->get('text_home'),
                    'href' => $this->url->link('common/home')
                ]
            ];

            if ($this->customer->isLogged()) {
                $breadcrumbs[] = [
                    'text' => $this->language->get('text_account'),
                    'href' => $this->url->link('account/account', '', true)
                ];
            }

            return array_merge($breadcrumbs, $additional);
        }
    }
}
