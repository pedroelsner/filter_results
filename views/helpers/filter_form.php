<?php
/**
 * Helper para criação de campos de formulário para o Componente 'Filter Results'
 *
 * Compatível com PHP 4 e 5
 *
 * Licenciado pela Creative Commons 3.0
 *
 * @filesource
 * @copyright  Copyright 2011, Pedro Elsner (http://pedroelsner.com/)
 * @author     Pedro Elsner <pedro.elsner@gmail.com>
 * @license    Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/br/)
 * @since      v 1.0
 */


/**
 * Filter Form Helper
 *
 * @use        AppHelper
 * @package    filter_results
 * @subpackage filter_results.filter_form
 * @link       http://www.github.com/pedroelsner/filter_results
 */
class FilterFormHelper extends AppHelper
{

/**
 * Helper auxiliar
 *
 * @var array
 * @access public
 */
    var $helpers = array(
        'Form'
    );
    
    
/**
 * Guarda referencia para o Componente 'Filter Results'
 *
 * @var object
 * @access private
 */
    private $_component;
    
    
/**
 * Setup
 * 
 * Recebe referencia do Componente 'Filter Results'
 *
 * @params object $component Referencia ao componente 'Filter Results'
 * @access protected
 */
    function _setup(&$component)
    {
        if ( !(is_object($component)) )
        {
            debug('Erro: FilterResults::Helper::Load()');
            return;
        }
        
        $this->_component = $component;
    }
    
    
/**
 * Has Component
 *
 * Verifica se componente foi inicializado
 *
 * @return boolean
 * @access protected
 */
    function _hasComponent()
    {
        return (is_object($this->_component)) ? true : false;
    }
    
    
/**
 * Create
 *
 * @param object $controller
 * @param array $settings
 * @return string
 * @access public
 */
    function create(&$controller, $settings = array())
    {
        
        $this->_setup($controller);
        
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        if ( !(is_array($settings)) )
        {
            $settings = array();
        }
        
        
        $default = array(
            'inputDefaults' => array(
                'label' => false,
                'div'   => false
            )
        );
        
        $settings = array_merge($settings, $default);
        $settings = array_merge($settings, $this->_component->getFormOptions());
        echo $this->Form->create(null, $settings);
        
    }
    
    
/**
 * End
 *
 * @param string $submit
 * @param array $settings
 * @return string
 * @access public
 */
    function end($submit = null, $settings = array())
    {
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        echo $this->Form->end($submit, $settings);
    }
    
    
/**
 * Submit
 *
 * @param string $name
 * @param array $settings
 * @return string
 * @access public
 */
    function submit($name, $settings = array())
    {
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        echo $this->Form->submit($name, $settings);
        
    }
    
    
/**
 * Input
 *
 * @param string $name
 * @param array $settings
 * @result string
 * @access public
 */
    function input($name, $settings = array())
    {
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        if ( !($this->_component->hasField($name)) )
        {
            return;
        }
        
        
        $settings['options'] = $this->_component->getFieldValues($name);
        
        echo $this->Form->input(sprintf('%s.%s', $this->_component->getPrefix(), $name), $settings);    
        
    }
    
    
/**
 * Select Operators
 *
 * Exibe campo de seleção de operações do filtro
 *
 * @param string $name
 * @param array $options
 * @param array $settings
 * @result string
 * @access public
 */
    function selectOperators($name, $options = null, $settings = array())
    {    
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        if ( !($this->_component->hasField($name)) )
        {
            return;
        }
        
        
        if ( !(is_array($options)) )
        {
            $options = array(
                'LIKE' => __('contendo', true),
                'NOT LIKE' => __('não contendo', true),
                'LIKEbegin' => 'começando com',
                'LIKEend'   => 'terminando com',
                '='    => __('iqual a', true),
                '!='   => __('diferente de', true),
                '>'    => __('maior que', true),
                '>='   => __('maior ou igual', true),
                '<'    => __('menor que', true),
                '<='   => __('menor ou igual', true)
            );
        }
        
        if ( !(is_array($settings)) )
        {
            $settings = array();
        }
        
        
        $settings['options'] = $options;
        echo $this->Form->input(sprintf('%s.%s.%s', $this->_component->getPrefix(), $this->_component->getOperator(), $name), $settings);
        
    }
    
/**
 * Select Fields
 *
 * Exibi campo para selação da coluna da tabela do banco de dados
 *
 * @param string $name
 * @param array $options
 * @param array $settings
 * @result string
 * @access public
 */
    function selectFields($name, $options = null, $settings = null)
    {
        if ( !($this->_hasComponent()) )
        {
            return;
        }
        
        if ( !($this->_component->hasField($name)) )
        {
            return;
        }
        
        
        if ( !(is_array($options)) )
        {
            $options = $this->_component->getModelFields();
        }
        
        if ( !(is_array($settings)) )
        {
            $settings = array();
        }
        
        $settings['options'] = $options;
        echo $this->Form->input(sprintf('%s.%s.%s', $this->_component->getPrefix(), $this->_component->getFieldModel(), $name), $settings);
        
    }
    
}
