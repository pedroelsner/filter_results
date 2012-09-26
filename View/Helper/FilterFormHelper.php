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
 * Create form's inputs with setting for component.
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
 * Default settings
 *
 * @var array
 * @access protected
 * @since 1.0
 */
    protected $_options = array(
        'operators' => array(
            'LIKE'       => 'contendo',
            'NOT LIKE'   => 'não contendo',
            'LIKE BEGIN' => 'começando com',
            'LIKE END'   => 'terminando com',
            '='  => 'iqual a',
            '!=' => 'diferente de',
            '>'  => 'maior que',
            '>=' => 'maior ou igual',
            '<'  => 'menor que',
            '<=' => 'menor ou igual'
        )
    );

/**
 * Construct
 * 
 * @param object $view
 * @param array $settings
 * @access public
 * @since 2.0
 */
public function __construct(View $view, $settings = array()) {
    parent::__construct($view, $settings);

    foreach ($view->viewVars as $key => $value) {
        if (strpos($key, 'FilterResults') > -1 && is_object($value)) {
            $this->_component = $value;
        }
    }

    $this->_options = array_merge($this->_options, $settings);
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
 * @param mixed $model
 * @param array $settings
 * @return string
 * @access public
 * @since  1.0
 */
    public function create($model = null, $settings = array()) {
        
        if (!$this->_hasComponent()) {
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
        
        $settings = array_merge($default, $settings);
        $settings = array_merge($this->_component->getOption('form'), $settings);
        
        return parent::create($model, $settings);
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

        if (!$this->_hasComponent()) {
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

        if (!$this->_hasComponent()) {
            return '';
        }
        
        return parent::submit($name, $settings);
        
    }

/**
 * Input
 *
 * @param string $name
 * @param array  $settings
 * @param array  $between
 * @return string
 * @access public
 * @since 1.0
 */
    public function input($name, $settings = array(), $between = array()) {

        if (!$this->_hasComponent()) {
            return '';
        }
        
        if (!$this->_component->hasField($name)) {
            return '';
        }
        
        
        $settings['options'] = $this->_component->getFieldSelect($name);
        $input = parent::input(sprintf('%s.%s', $this->_component->getOption('label', 'prefix'), $name), $settings);

        
        if($this->_component->getFieldOperator($name) == 'between') {
            $between['options'] = $this->_component->getFieldSelect($name);

            $input .= (isset($setting['between']['text'])) ? $setting['between']['text'] : ' ';
            $input .= parent::input(sprintf('%s.%s-between', $this->_component->getOption('label', 'prefix'), $name), $between);
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

        if (!$this->_hasComponent()) {
            return '';
        }
        
        if (!$this->_component->hasField($name)) {
            return '';
        }
        
        
        if (!is_array($options)) {
            $options = $this->_options['operators'];
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

        return $this->input(sprintf('%s.%s.%s', $this->_component->getOption('label', 'prefix'), $this->_component->getOption('label', 'fieldModel'), $name), $settings);
    }
    
}
