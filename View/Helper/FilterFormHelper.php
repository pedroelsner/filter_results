<?php
/**
 * Helper to create form fields.
 *
 * Licenciado pela Creative Commons 3.0
 *
 * @filesource
 * @copyright  Copyright 2012, Pedro Elsner (http://pedroelsner.com/)
 * @author     Pedro Elsner <pedro.elsner@gmail.com>
 * @license    Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/)
 * @since      2.0
 */

App::uses('FormHelper', 'View/Helper');

/**
 * Create object´s form with setting for component.
 *
 * @use        FormHelper
 * @package    filter_results
 * @subpackage filter_results.filter_form
 * @link       http://www.github.com/pedroelsner/filter_results/tree/2.0/View/Helper/FilterFormHelper.php
 */
class FilterFormHelper extends FormHelper {  

/**
 * Save controller's reference
 *
 * @var object
 * @access protected
 * @since 1.0
 */
    protected $_component;

/**
 * Setup
 *
 * @param object $component Referencia ao componente 'Filter Results'
 * @access protected
 * @since 1.0
 */
    protected function _setup(FilterResultsComponent $component) {
        $this->_component = $component;
    }

/**
 * Check if component has been inicialized
 *
 * @return boolean
 * @access protected
 * @since 1.0
 */
    protected function _hasComponent() {
        return is_object($this->_component);
    }
    
/**
 * Create
 *
 * @param object $component
 * @param array $settings
 * @return string
 * @access public
 * @since  1.0
 */
    public function create(FilterResultsComponent $component, $settings = array()) {
        
        self::_setup($component);
        
        if (!self::_hasComponent()) {
            return '';
        }
        
        if (!is_array($settings)) {
            $settings = array();
        }
        
        $default = array(
            'inputDefaults' => array(
                'label' => false,
                'div'   => false
            )
        );
        
        $settings = array_merge($settings, $default);
        $settings = array_merge($settings, $this->_component->getOption('form'));
        
        return parent::create(null, $settings);
    }

/**
 * End
 *
 * @param string $submit
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
    public function end($submit = null, $settings = array()) {

        if (!self::_hasComponent()) {
            return '';
        }
        
        return parent::end($submit, $settings);
    }

/**
 * Submit
 *
 * @param string $name
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
    public function submit($name, $settings = array()) {

        if (!self::_hasComponent()) {
            return '';
        }
        
        return parent::submit($name, $settings);
        
    }

/**
 * Input
 *
 * @param string $name
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
    public function input($name, $settings = array()) {

        if (!self::_hasComponent()) {
            return '';
        }
        
        if (!$this->_component->hasField($name)) {
            return '';
        }
        
        
        $settings['options'] = $this->_component->getFieldSelect($name);

        $input = parent::input(sprintf('%s.%s', $this->_component->getOption('label', 'prefix'), $name), $settings);

        if($this->_component->getFieldOperator($name) == 'between') {
            $input .= (isset($setting['between']['text'])) ? $setting['between']['text'] : ' ';
            $input .= parent::input(sprintf('%s.%s2', $this->_component->getOption('label', 'prefix'), $name), $settings);
        }
        
        return $input;
    }

/**
 * Return selection field of operators
 *
 * @param string $name
 * @param array $options
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
    public function selectOperators($name, $options = null, $settings = array()) {

        if (!self::_hasComponent()) {
            return '';
        }
        
        if (!$this->_component->hasField($name)) {
            return '';
        }
        
        
        if (!is_array($options)) {
            $options = array(
                'LIKE'       => __('contendo', true),
                'NOT LIKE'   => __('não contendo', true),
                'LIKE BEGIN' => __('começando com', true),
                'LIKE END'   => __('terminando com', true),
                '='    => __('iqual a', true),
                '!='   => __('diferente de', true),
                '>'    => __('maior que', true),
                '>='   => __('maior ou igual', true),
                '<'    => __('menor que', true),
                '<='   => __('menor ou igual', true)
            );
        }
        
        if (!is_array($settings)) {
            $settings = array();
        }
        
        
        $settings['options'] = $options;
        
        return parent::input(sprintf('%s.%s.%s', $this->_component->getOption('label', 'prefix'), $this->_component->$this->_component->getOption('label', 'operator'), $name), $settings);
        
    }
    
/**
 * Select Fields
 *
 * Exibi campo para selação da coluna da tabela do banco de dados
 *
 * @param string $name
 * @param array $options
 * @param array $settings
 * @return string
 * @access public
 * @since 1.0
 */
    public function selectFields($name, $options = null, $settings = null) {

        if (!$this->_hasComponent()) {
            return '';
        }
        
        if (!$this->_component->hasField($name)) {
            return '';
        }
        
        
        if (!is_array($options)) {
            $options = $this->_component->getModelFields();
        }
        
        if (!is_array($settings)) {
            $settings = array();
        }
        
        $settings['options'] = $options;

        return self::input(sprintf('%s.%s.%s', $this->_component->getOption('label', 'prefix'), $this->_component->$this->_component->getOption('label', 'fieldModel'), $name), $settings);
    }
    
}
