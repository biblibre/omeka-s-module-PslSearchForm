<?php

/*
 * Copyright BibLibre, 2016
 *
 * This software is governed by the CeCILL license under French law and abiding
 * by the rules of distribution of free software.  You can use, modify and/ or
 * redistribute the software under the terms of the CeCILL license as circulated
 * by CEA, CNRS and INRIA at the following URL "http://www.cecill.info".
 *
 * As a counterpart to the access to the source code and rights to copy, modify
 * and redistribute granted by the license, users are provided only with a
 * limited warranty and the software's author, the holder of the economic
 * rights, and the successive licensors have only limited liability.
 *
 * In this respect, the user's attention is drawn to the risks associated with
 * loading, using, modifying and/or developing or reproducing the software by
 * the user in light of its specific status of free software, that may mean that
 * it is complicated to manipulate, and that also therefore means that it is
 * reserved for developers and experienced professionals having in-depth
 * computer knowledge. Users are therefore encouraged to load and test the
 * software's suitability as regards their requirements in conditions enabling
 * the security of their systems and/or data to be ensured and, more generally,
 * to use and operate it in the same conditions as regards security.
 *
 * The fact that you are presently reading this means that you have had
 * knowledge of the CeCILL license and that you accept its terms.
 */

namespace PslSearchForm;

use Omeka\Module\AbstractModule;
use Zend\EventManager\Event;
use Zend\EventManager\SharedEventManagerInterface;

class Module extends AbstractModule
{
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function attachListeners(SharedEventManagerInterface $sharedEventManager)
    {
        // TODO How to attach all public events only?
        $sharedEventManager->attach(
            '*',
            'view.layout',
            [$this, 'publicViewLayout']
        );
    }

    /**
     * Preload the search form styles and scripts.
     *
     * @param Event $event
     */
    public function publicViewLayout(Event $event)
    {
        $view = $event->getTarget();
        // Some pages may be neither site nor admin.
        if (!$view->status()->isSiteRequest()) {
            return;
        }

        if (!$view->getHelperPluginManager()->has('searchForm')) {
            return;
        }

        $searchMainPage = $view->siteSetting('search_main_page');
        if (!$searchMainPage) {
            return;
        }

        /** @var \Search\Api\Representation\SearchPageRepresentation $searchPage */
        $searchPage = $view->api()->searchOne('search_pages', ['id' => $searchMainPage])->getContent();
        if ($searchPage && $searchPage->form() instanceof \PslSearchForm\Form\PslForm) {
            // No echo: it's just a preload.
            $view->partial('psl-search-form/psl-search-form-layout');
        }
    }
}
