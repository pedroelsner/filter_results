<?php
/**
 * Component to filter results by fields of the form sent.
 * Compatibility with Paginator Component.
 *
 * Licencied by Creative Commons 3.0
 *
 * @filesource
 * @copyright  Copyright 2012, Pedro Elsner (http://pedroelsner.com/)
 * @author     Pedro Elsner <pedro.elsner@gmail.com>
 * @license    Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/)
 * @version    2.0
 */

/**
 * Application component class for return conditions array to cake model.
 * Provides basic functionality and encrypt or decrypt fields of the form sent.
 *
 * @use        Component
 * @package    filter_results
 * @subpackage filter_results.filter_results
 * @link       http://www.github.com/pedroelsner/filter_results/tree/2.0/Controller/Component/FilterResultsComponent.php
 */
class FilterResultsComponent extends Component {
    
/**
 * Control the number of instances
 *
 * @var int
 * @access public
 * @static
 * @since 1.0
 */
    public static $instances = 0;

/**
 * Receive actual filter field for function self::_makeConditions()
 *
 * @var array
 * @access protected
 * @since 2.0
 */
    protected $_filter = array();
    
/**
 * The instance number of component
 *
 * @var int
 * @access private
 * @since 1.0
 */
    private $_instance;
    
/**
 * Default settings
 *
 * @var array
 * @access protected
 * @since 1.0
 */
    protected $_options = array(
        'auto' => array(
            'paginate' => false,
            'explode'  => true,
        ),
        'explode' => array(
            'character'   => ' ',
            'concatenate' => 'AND',
        ),
        'label' => array(
            'prefix'     => 'filter',
            'fieldModel' => 'field',
            'operator'   => 'operatior',
        ),
        'form' => array(
            'id' => 'form-filter-results',
        )
    );

/**
 * Save controller's reference
 *
 * @var object
 * @access protected
 * @since 2.0
 */
    protected $_controller;

/**
 * Save results of $this->make()
 *
 * @var array
 * @access protected
 * @since 1.0
 */
    protected $_conditions = array();

/**
 * Save $controller->request->params without encrypt
 *
 * @var array
 * @access protected
 * @since 1.0
 */
    protected $_params = array(); 
   
/**
 * Construct
 *
 * @access public
 * @since 1.0
 */
    public function __construct(ComponentCollection $collection, $settings = array()) {
        $this->_instance = ++self::$instances;
        $this->_options = array_merge($this->_options, $settings);
    }

/**
 * Clone
 *
 * @access public
 * @since 1.0
 */
    public function __clone() {
        $this->_instance = ++self::$instances;
        $this->_options['label']['prefix'] .= sprintf('_%s', $this->_instance);
    }   

/**
 * Executed before the Controller::beforeFiler()
 *
 * @param object $controller
 * @access public
 * @since 1.0
 */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }
    
/** 
 * Executed after the Controller::beforeFiler(), but before execute the requested action
 *
 * @param object $controller
 * @access public
 * @since 1.0
 */
    public function startup(Controller $controller) {

    }

/**
 * Executed before the Controller:beforeRender()
 *
 * @param object $controller
 * @access public
 * @since 1.0
 */
    public function beforeRender(Controller $controller) {
    }

/**
 * Executed before the Controller:render()
 *
 * @param object $controller
 * @access public
 * @since 1.0
 */
    public function shutdown(Controller $controller) {
        
    }

/**
 * Executed before the Controller:redirect()
 *
 * @param object  $controller
 * @param array   $url
 * @param string  $status
 * @param boolean $exit
 * @access public
 * @since 1.0
 */
    public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
        
    }

/**
 * Encripty the string sent
 *
 * @param string $string
 * @return string
 * @access protected
 * @since 1.0
 */
    protected function _encrypt($string) {
        return (!is_string($string)) ? '' : base64_encode($string);
    }

/**
 * Decrypty the string sent
 *
 * @param  string $string
 * @return string
 * @access protected
 * @since  1.0
 */
    protected function _decrypt($string) {
        return (!is_string($string)) ? '' : base64_decode($string);
    }

/**
 * Give the requested setting
 *
 * @param string $key
 * @param mixed  $option
 * @return mixed
 * @access public
 * @since 2.0
 */
    public function getOption($key, $option = null) {

        if (is_null($option)) {
            return (isset($this->_options[$key])) ? $this->_options[$key] : '';
        }

        return (isset($this->_options[$key][$option])) ? $this->_options[$key][$option] : '';
    }

/**
 * Set the valid value for setting sent
 *
 * @param string $key
 * @param string $option
 * @param mixed  $value
 * @return boolean
 * @access public
 * @since 2.0
 */
    public function setOption($key, $option, $value) {

        switch ($key) {
            case 'filters':
                return false;
                break;

            case 'auto':
            case 'explode':
            case 'label':
                $this->_options[$key][$option] = $value;
                return true;
                break;
            
            default:
                return false;
                break;
        }

    }

/**
 * Return conditions created by the function $this->make()
 *
 * @return array
 * @access public
 * @since 1.0
 */
    public function getConditions() {

        if (!is_array($this->_conditions)) {
            return $this->make();
        }

        if (count($this->_conditions) == 0) {
            return $this->make();
        }

        return $this->_conditions;
    }   

/**
 * HasField
 *
 * @param string $field
 * @return boolean
 * @access public
 * @since 1.0
 */
    public function hasField($field) {
        return $this->_searchField($field, $this->getOption('filters'));
    }

/**
 * Search the specified field
 * 
 * @param string $field  
 * @param type   $filters
 * @return boolean
 * @access protected
 * @since 2.0
 */
    protected function _searchField($field, $filters) {

        $hasField = false;

        foreach($filters as $key => $value) {

            switch (mb_strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $hasField = $this->_searchField($field, $value);
                    break;

                default:
                    if (is_string($value)) {
                       if ($value == $field) {
                            $hasField = true;
                        }
                    }
                    
                    if ($key == $field) {
                        $hasField = true;
                    }
            }
        }

        return $hasField;
    }

/**
 * Return array select of the specified field
 *
 * @param string $field
 * @return mixed
 * @access public
 * @since 1.0
 */
    public function getFieldSelect($field) {
        
        $values = array();
        
        if ($this->hasField($field)) {
            $options = $this->_getFilterOptions($field, $this->getOption('filters'));
            $values += $this->_searchSelectOption($options);
        }
        
        return (count($values) == 0) ? null : $values;
    }

/**
 * Search for possible values of specified field
 *
 * @param array $array
 * @return mixed
 * @access public
 * @since 2.0
 */
    public function _searchSelectOption($options) {

        $result = array();
        
        foreach ($options as $key => $value) {
            if (isset($options[$key]['select'])) {
                if (is_array($options[$key]['select'])) {
                    $result += $options[$key]['select'];
                }
            } else {
                if (is_array($value)) {
                    $result += $this->_searchSelectOption($value);
                }
            }
        }
        
        return $result;
    }   

/**
 * Composite array values for selection in the form
 *
 * @param string $label
 * @param array  $values
 * @return array
 * @since 2.0
 */
    public function select($label, $values) {
        return array('' => $label) + $values;
    }
/**
 * @deprecated 08-04-2012
 */
    public function values($label, $values) {
        return $this->values($label, $values);
    }
/**
 * @deprecated 07-04-2012
 */
    public function merge($label, $values) {
        return $this->values($label, $values);
    }

/**
 * Define one or more filters
 *
 * @param array $filters
 * @throws Exception When $filters is not be a array or string
 * @access public
 * @since 1.0
 */
    public function addFilters($filters = null) {

        if (!is_array($filters)) {
            if (!is_string($filters)) {
                throw new Exception(__('$filters type must be a array or string', true));
            }

            $filters = array($filters);
        }
        
        if ($this->getOption('filters')) {
            $this->_options['filters'] += $filters;
        } else {
            $this->_options['filters'] = $filters;
        }
    }

/**
 * Main method
 * Do everiting to create conditions
 *
 * @return array 
 * @access public
 * @since 1.0
 */
    public function make() {
        
        if (isset($this->controller->request->data[$this->getOption('label', 'prefix')])) {
            $this->_redirectToNamedUrl();
            return;
        }
        
        
        /**
         * Sent component to view
         */
        $name = 'FilterResults';
        if ($this->_instance > 1) {
            $name = $name . $this->_instance;
        }
        $this->controller->set($name, $this);
        
        if (count($this->controller->request->params['named']) == 0) {
            return array();
        }
        
        if ($this->_check() > 0) {
            
            $this->_conditions = $this->_filterFields($this->_options['filters']);

            if ($this->getOption('auto', 'paginate')) {
                $this->controller->Paginator->paginate['conditions'][] = $this->_conditions;
            }
            return $this->_conditions;
        }
        
    }

/**
 * Make the URL with NAMED fields.
 * Like this: example.com/cake/posts/index/Search.keywords:mykeyword/Search.tag_id:3
 * 
 * After, autoredirect to created URL
 * 
 * @param array $url
 * @param array $get
 * @access protected
 * @since 2.0
 */
    protected function _redirectToNamedUrl($url = array(), $get = array()) {
    
        foreach ($this->controller->request['url'] as $key => $value) {
            if ($key != 'url') {
                $get += array($key => $value);
            }
        }
        
        if (count($get) > 0) {
            $url['?'] = $get;
        }
        
        foreach ($this->controller->request->data[$this->getOption('label', 'prefix')] as $key => $value) {

            if (!is_array($value)) {
                $url[$this->_encrypt(sprintf('%s.%s', $this->getOption('label', 'prefix'), $key))] = $this->_encrypt($value);
            } else {
                foreach ($value as $k => $v) {
                    $url[$this->_encrypt(sprintf('%s.%s.%s', $this->getOption('label', 'prefix'), $key, $k))] = $this->_encrypt($v);
                }
            }
        }
        
        $this->controller->redirect($url, null, true);
    }


        
/**
 * Check if the URL contain some NAMED parameter.
 * If found then encrypt and store it.
 * Return count of NAMED parameters.
 *
 * @return int
 * @access protected
 * @since 1.0
 */
    protected function _check() {
        
        // Decrypt all NAMED parameters
        foreach ($this->controller->request->params['named'] as $key => $value) {
            $this->_params[$this->_decrypt($key)] = $this->_decrypt($value);
        }
        
        $count = 0;
        foreach ($this->_params as $key => $value) {
            if (strpos($key,$this->getOption('label', 'prefix')) > -1) {
                $count++;
            }
        }
        return $count;
    }

/**
 * FilterFields
 *
 * @param array $filters
 * @param array $condition
 * @return array
 * @access protected
 * @since 1.0
 */
    protected function _filterFields($filters, $condition = array()) {

        foreach ($filters as $key => $value) {

            switch (mb_strtolower($key, 'utf-8')) {
                case 'not':
                case 'and':
                case 'or':
                    
                    $conditionOfFilter = $this->_filterFields($value);
                    if (count($conditionOfFilter) > 0) {
                        if (!isset($condition[$key])) {
                            $condition[$key] = $this->_filterFields($value);
                        } else {
                            $condition[$key] += $this->_filterFields($value);
                        }    
                    }
                    break;
                
                default:
                    $condition += (is_array($value))
                         ? $this->_makeConditions($key, $value)
                         : $this->_makeConditions($value);
                    break;
            }

        }
        
        return $condition;
    }    

/**
 * Make conditions for the specified field sent
 *
 * @param array $field
 * @param array $options
 * @param array $condition
 * @access protected
 * @since 1.0
 */
    protected function _makeConditions($field, $options = null, $condition = array()) {
        
        if (!isset($options)) {
            return $this->_makeConditionsWithoutOptions($field);
        }
        
        foreach ($options as $key => $value) {

            switch (strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $condition += array(
                        $key => $this->_makeConditions($field, $value)
                    );
                    break;
                
                default:
                    
                    $this->_filter = array();
                    $this->_filter['field'] = $field;

                    /**
                     * Check the parameter's value, if empty break
                     */                    
                    if (!$this->_hasFieldParams()) {
                        break;
                    }


                    /**
                     * Define defaults
                     */
                    if (is_array($value)) {
                        $this->_filter['fieldModel'] = $key;
                        $this->_filter += $value;
                    } else {
                        $this->_filter['fieldModel'] = (is_array($value)) ? $key : $value;
                    }

                    if (!isset($this->_filter['operator'])) {
                        $this->_filter['operator'] = '=';
                    }

                    $this->_filter['value.before'] = $this->_defaultOptionsValue('before');
                    $this->_filter['value.after']  = $this->_defaultOptionsValue('after');

                    if (!isset($this->_filter['value'])) {
                        $this->_filter['value'] = (isset($this->_filter['select'])) ? $this->_getFieldParams() : '';
                    } else {
                        if (is_array($this->_filter['value'])) {
                            $this->_filter['select'] = $this->_filter['value'];
                            $this->_filter['value'] = $this->_getFieldParams();
                        }
                    }
                    
                    if (empty($this->_filter['value'])) {
                        break;
                    }
                    
                    $this->_filter['explode.concatenate'] = (isset($this->_filter['explode']['concatenate']))
                                                          ? $this->_filter['explode']['concatenate']
                                                          : $this->getOption('explode', 'concatenate');

                    $this->_filter['explode.character'] = (isset($this->_filter['explode']['character']))
                                                          ? $this->_filter['explode']['character']
                                                          : $this->getOption('explode', 'character');

                    if (!isset($this->_filter['explode'])) {
                        $this->_filter['explode'] = null;
                    } else {
                        if (is_array($this->_filter['explode'])) {
                            $this->_filter['explode'] = true;
                        }
                    }

                    switch (mb_strtolower($this->_filter['operator'], 'utf-8')) {
                        case 'between':
                            $condition += $this->_conditionsForOperatorBetween();    
                            break;
                        
                        default:

                            $condition += $this->_isMayExplodeValue()
                                        ? $this->_valueConcatenate()
                                        : $this->_value();
                            debug($condition);
                            $this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field']] = $this->_getFieldParams();
                            break;
                    }

            }
        }
        
        return $condition;
    }


protected function _hasFieldParams($more = null, $between = false) {
    
    if ($between) {
        return isset($this->_params[sprintf('%s.%s.between', $this->getOption('label', 'prefix'), $this->_filter['field'])]);
    }

    if (is_null($more)) {
        return isset($this->_params[sprintf('%s.%s', $this->getOption('label', 'prefix'), $this->_filter['field'])]);
    } else {
        return isset($this->_params[sprintf('%s.%s.%s', $this->getOption('label', 'prefix'), $this->getOption('label', $more), $this->_filter['field'])]);
    }
}

protected function _getFieldParams($more = null, $between = false) {
    
    if (!$this->_hasFieldParams($more, $between)) {
        return '';
    }
    
    if ($between) {
        return $this->_params[sprintf('%s.%s.between', $this->getOption('label', 'prefix'), $this->_filter['field'])];
    }

    if (is_null($more)) {
        return $this->_params[sprintf('%s.%s', $this->getOption('label', 'prefix'), $this->_filter['field'])];
    } else {
        return $this->_params[sprintf('%s.%s.%s', $this->getOption('label', 'prefix'), $this->getOption('label', $more), $this->_filter['field'])];
    }
}

/**
 * Make conditions for fileds without specified parameters
 *
 * @param array $field
 * @return array
 * @access protected
 * @since 2.0
 */
    protected function _makeConditionsWithoutOptions($field) {

        $this->_filter = array();
        $this->_filter['field'] = $field;

        if (!$this->_hasFieldParams()) {
            return array();
        }
        
        if (!$this->_hasFieldParams('fieldModel')) {
            return array();
        }
        

        /**
         * Define defaults
         */
        $this->_filter['fieldModel'] = $this->_getFieldParams('fieldModel');
        $this->_filter['value']      = $this->_getFieldParams();

        $this->_filter['operator'] = ($this->_hasFieldParams('operator')) ? $this->_getFieldParams('operator') : 'like';

        $this->_filter['explode.concatenate'] = $this->getOption('explode', 'concatenate');
        $this->_filter['explode.character']   = $this->getOption('explode', 'character');


        switch(mb_strtolower($this->_filter['operator'], 'utf-8')) {
            case 'like':
            case 'not like':
                $this->_filter['value.before'] = '%';
                $this->_filter['value.after']  = '%';
                break;
            
            case 'likebegin':
            case 'like begin':
                $operator = 'LIKE';
                $this->_filter['value.after'] = '%';
                break;
            
            case 'likeend':
            case 'like end':
                $operator = 'LIKE';
                $this->_filter['value.before'] = '%';
                break;

            default:
                $this->_filter['value.before'] = '';
                $this->_filter['value.after']  = '';
                break;
        }
        

        $condition = $this->_isMayExplodeValue()
                   ? $this->_valueConcatenate()
                   : $this->_value();
        
        $this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field']] = $this->_getFieldParams();

        $this->controller->request->data[$this->getOption('label', 'prefix')][$this->getOption('label', 'fieldModel')][$field] = $this->_getFieldParams('fieldModel');
        $this->controller->request->data[$this->getOption('label', 'prefix')][$this->getOption('label', 'operator')][$field]   = $this->_getFieldParams('operator');
        
        return $condition;
    }

/**
 * Make the condition when operator='between'
 * 
 * @return array
 * @access protected
 */
    protected function _conditionsForOperatorBetween() {

        if (count($this->_filter) == 0) {
            return array();
        }

        /**
         * Verifica a existencia dos dois parâmetros
         */ 
        if (!$this->_hasFieldParams() || !$this->_hasFieldParams(null, true))
        {
                                
            if ($this->_hasFieldParams()) {
                $$this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field']] = $this->_getFieldParams();
            }

            if ($this->_hasFieldParams(null, true)) {
                $$this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field'].'.between'] = $this->_getFieldParams(null, true);
            }

            return array();
        }

        $this->_filter['value.between'] = $this->_getFieldParams(null, true);
        

        /** 
         * Altera o formato da data para formato de banco
         */
        if (isset($this->_filter['between']['date'])) {
            if ($this->_filter['between']['date']) {
                $this->_filter['value']         = implode(preg_match("~\/~", $this->_filter['value'])         == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $this->_filter['value'])         == 0 ? "-" : "/", $this->_filter['value'])));
                $this->_filter['value.between'] = implode(preg_match("~\/~", $this->_filter['value.between']) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $this->_filter['value.between']) == 0 ? "-" : "/", $this->_filter['value.between'])));
            }
        }
                            
        $this->_filter['value']    = array($this->_filter['value'], $this->_filter['value.between']);
        $this->_filter['operator'] = 'BETWEEN ? AND ?';
        
        $condition = array(sprintf('%s %s', $this->_filter['fieldModel'], $this->_filter['operator']) => $this->_filter['value']);
        
        $this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field']]            = $this->_getFieldParams();
        $this->controller->request->data[$this->getOption('label', 'prefix')][$this->_filter['field'].'.between'] = $this->_getFieldParams(null, true);

        return $condition;
    }

/**
 * Return the default value of the specified option
 * 
 * @param string $option
 * @return string
 * @access protected
 * @since 2.0
 */
    protected function _defaultOptionsValue($option) {

        if (isset($this->_filter['value'])) {
            if (is_array($this->_filter['value'])) {
                $default = (isset($this->_filter['value'][$option])) ? $this->_filter['value'][$option] : '';
            } else {
                $default = '';
            }
        }


        if (!$default) {
            switch (mb_strtolower($this->_filter['operator'], 'utf-8')) {
                case 'like':
                case 'not like':
                    $default = '%';
                    break;
                
                default:
                    break;
            }
        }

        return $default;
    }

/**
 * Verify the permission to explode value
 * 
 * @return boolean
 * @access protected
 * @since 2.0
 */
    protected function _isMayExplodeValue() {

        if (count($this->_filter) == 0) {
            return false;
        }

        if (is_null($this->_filter['explode'])) {

            switch (mb_strtolower($this->_filter['operator'], 'utf-8')) {
                case 'like':
                case 'not like':
                    return $this->getOption('auto' ,'explode');
                    break;

                default:
                    break;
            }
        }

        if ($this->_filter['explode']) {
            return true;
        }

        return false;
    }

/**
 * Composite value of condition
 * 
 * @return array
 * @access protected
 * @since 2.0
 */
    protected function _value() {

        if (count($this->_filter) == 0) {
            return array();
        }

        return array(
            sprintf('%s %s', $this->_filter['fieldModel'], $this->_filter['operator']) =>
            sprintf('%s%s%s', $this->_filter['value.before'], $this->_filter['value'], $this->_filter['value.after'])
        );
    }

/**
 * Explode and concatenate the values
 * 
 * @return array
 * @access protected
 * @since 2.0
 */
    protected function _valueConcatenate() {

        $condition = array();
        $values = explode($this->_filter['explode.character'], $this->_filter['value']);

        if (count($values) > 1) {

            foreach ($values as $k => $v) {
                $condition[$this->_filter['explode.concatenate']][$k] = array(
                    sprintf('%s %s', $this->_filter['fieldModel'], $this->_filter['operator']) =>
                    sprintf('%s%s%s', $this->_filter['value.before'], $v, $this->_filter['value.after'])
                );
            }

        } else {
            $condition = $this->_value();
        }

        return $condition;
    }
    
/**
 * Get the fields of model automaticaly
 *
 * @return array
 * @access protected
 * @since 1.0
 */
    protected function getModelFields() {

        if (!isset($this->controller->modelNames[0])) {
            return array();
        }
        
        $fields = array();
        foreach($this->controller->{$this->controller->modelNames[0]}->_schema as $key => $value) {
            $fields[sprintf('%s.%s', $this->controller->modelNames[0], $key)] = $key;
        }
        return $fields;
    }

/**
 * getFieldOperator
 * 
 * @param string $fieldName
 * @return string
 * @access public
 * @since 1.1
 */
    public function getFieldOperator($fieldName) {

        $options = $this->_getFilterOptions($fieldName, $this->getOption('filters'));
        foreach ($options as $key => $value) {
            if (isset($value['operator'])) {
                return mb_strtolower($value['operator'], 'utf-8');
            }
        }

        return '';
    }

/**
 * Search Filter Options
 * 
 * Retorna as opções do filtro
 * 
 * @param string $field  
 * @param type   $filters
 * @return array
 * @access protected
 * @since 2.0
 */
    protected function _getFilterOptions($field, $filters) {

        $return = array();

        foreach($filters as $key => $value) {

            switch (mb_strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $return = $this->_getFilterOptions($field, $value);
                    break;
                
                default:
                   if ($key == $field) {
                        $return = $value;
                   }
            }
        }

        return $return;
    }

/**
 * Define paginator's options for CakePHP2.2+
 * 
 * @param mixed $option
 * @param mixed $value
 * @access public
 * @since 2.0
 */
    public function setPaginate($option, $value = null) {
        $setting = (is_array($option)) ? $option : array($option => $value);
        $this->controller->paginate = array_merge($this->controller->paginate, $setting);
    }
    
}
