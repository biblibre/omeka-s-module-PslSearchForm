<?php

/*
 * Copyright BibLibre, 2016
 * Copyright Daniel Berthereau 2018
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

namespace PslSearchForm\Form;

use Search\Query;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\I18n\Translator\TranslatorAwareInterface;
use Zend\I18n\Translator\TranslatorAwareTrait;

class PslFormConfigFieldset extends Fieldset implements TranslatorAwareInterface
{
    use TranslatorAwareTrait;

    public function init()
    {
        $translator = $this->getTranslator();

        $this->add($this->getAdvancedFieldsFieldset());

        $this->add([
            'name' => 'is_public_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => $translator->translate('Is Public field'), // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => $translator->translate('None'), // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'date_range_field',
            'type' => Element\Select::class, // @translate
            'options' => [
                'label' => $translator->translate('Date range field'), // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => $translator->translate('None'),
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'item_set_id_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => $translator->translate('Item set id field'), // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => $translator->translate('None'), // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'creation_year_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => $translator->translate('Creation year field'), // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => $translator->translate('None'), // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add([
            'name' => 'spatial_coverage_field',
            'type' => Element\Select::class,
            'options' => [
                'label' => $translator->translate('Spatial coverage field'), // @translate
                'value_options' => $this->getFieldsOptions(),
                'empty_option' => $translator->translate('None'), // @translate
            ],
            'attributes' => [
                'required' => true,
            ],
        ]);

        $this->add($this->getLocationsFieldset());
    }

    protected function getAdvancedFieldsFieldset()
    {
        $translator = $this->getTranslator();

        $advancedFieldsFieldset = new Fieldset('advanced-fields');
        $advancedFieldsFieldset->setLabel($translator->translate('Advanced search fields')); // @translate
        $advancedFieldsFieldset->setAttribute('data-sortable', '1');

        $fields = $this->getAvailableFields();
        $weights = range(0, count($fields));
        $weight_options = array_combine($weights, $weights);
        $weight = 0;
        foreach ($fields as $field) {
            $fieldset = new Fieldset($field['name']);
            $fieldset->setLabel($this->getFieldLabel($field));

            $displayFieldset = new Fieldset('display');
            $displayFieldset->add([
                'name' => 'label',
                'type' => Element\Text::class,
                'options' => [
                    'label' => $translator->translate('Label'), // @translate
                ],
            ]);
            $fieldset->add($displayFieldset);

            $fieldset->add([
                'name' => 'enabled',
                'type' => Element\Checkbox::class,
                'options' => [
                    'label' => $translator->translate('Enabled'), // @translate
                ],
            ]);

            $fieldset->add([
                'name' => 'weight',
                'type' => Element\Select::class,
                'options' => [
                    'label' => $translator->translate('Weight'), // @translate
                    'value_options' => $weight_options,
                ],
                'attributes' => [
                    'value' => $weight++,
                ],
            ]);

            $advancedFieldsFieldset->add($fieldset);
        }

        return $advancedFieldsFieldset;
    }

    protected function getAvailableFields()
    {
        $searchPage = $this->getOption('search_page');
        $searchAdapter = $searchPage->index()->adapter();
        return $searchAdapter->getAvailableFields($searchPage->index());
    }

    protected function getFieldsOptions()
    {
        $options = [];
        foreach ($this->getAvailableFields() as $name => $field) {
            if (isset($field['label'])) {
                $options[$name] = sprintf('%s (%s)', $field['label'], $name);
            } else {
                $options[$name] = $name;
            }
        }
        return $options;
    }

    protected function getLocationsFieldset()
    {
        $translator = $this->getTranslator();

        $fieldset = new Fieldset('locations');

        $locations = $this->getLocations();
        if (!empty($locations)) {
            $fieldset->setLabel($translator->translate('Locations')); // @translate

            foreach ($this->getLocations() as $location) {
                $fieldset->add([
                    'name' => $location,
                    'type' => Element\Text::class,
                    'options' => [
                        'label' => $location,
                    ],
                    'attributes' => [
                        'placeholder' => $translator->translate('Latitude, Longitude'), // @translate
                    ],
                ]);
            }
        }

        return $fieldset;
    }

    protected function getLocations()
    {
        /** @var \Search\Api\Representation\SearchPageRepresentation $searchPage */
        $searchPage = $this->getOption('search_page');
        $searchQuerier = $searchPage->index()->querier();
        $settings = $searchPage->settings();
        $spatialCoverageField = isset(['form']['spatial_coverage_field'])
            ? ['form']['spatial_coverage_field']
            : '';

        $locations = [];
        if ($spatialCoverageField) {
            $query = new Query;
            $query->setResources(['items']);
            $query->addFacetField($spatialCoverageField);

            $response = $searchQuerier->query($query);

            $facetCounts = $response->getFacetCounts();
            if (isset($facetCounts[$spatialCoverageField])) {
                foreach ($facetCounts[$spatialCoverageField] as $facetCount) {
                    $locations[] = $facetCount['value'];
                }
            }
        }

        return $locations;
    }

    protected function getFieldLabel($field)
    {
        $searchPage = $this->getOption('search_page');
        $settings = $searchPage->settings();

        $name = $field['name'];
        $label = isset($field['label']) ? $field['label'] : null;
        if (isset($settings['form']['advanced-fields'][$name])) {
            $fieldSettings = $settings['form']['advanced-fields'][$name];

            if (isset($fieldSettings['display']['label'])
                && $fieldSettings['display']['label']) {
                $label = $fieldSettings['display']['label'];
            }
        }
        $label = $label ? sprintf('%s (%s)', $label, $field['name']) : $field['name'];

        return $label;
    }
}
