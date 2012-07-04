<?php
/**
 * Componente que filtra resultados através de parâmetros enviado
 * por campos de formulário. Compatível com o componente 'Paginate'
 *
 * Compatível com PHP 5.2+
 *
 * Licenciado pela Creative Commons 3.0
 *
 * @filesource
 * @copyright  Copyright 2012, Pedro Elsner (http://pedroelsner.com/)
 * @author     Pedro Elsner <pedro.elsner@gmail.com>
 * @license    Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/br/)
 * @version    2.0
 */


App::uses('Component', 'Controller/Component');

/**
 * Filter Results Component
 *
 * @use        Component
 * @package    filter_results
 * @subpackage filter_results.filter_results
 * @link       http://www.github.com/pedroelsner/FilterResults2
 */
class FilterResultsComponent extends Component {
    
/**
 * Controle do número de instâncias
 *
 * @var int
 * @access public
 * @static
 * @since  1.0
 */
    public static $instances = 0;
    
/**
 * Idencificação da instância
 *
 * @var int
 * @access private
 * @since  1.0
 */
    private $_instance;
    
/**
 * Configurações do Component
 *
 * @var array
 * @access private
 * @since  1.0
 */
    private $_options = array(
        'autoPaginate'    => false,
        'autoLikeExplode' => true,
        'explodeChar'        => ' ',
        'explodeConcatenate' => 'AND',
        'form' => array(
            'id' => 'formFilterResults',
        ),
        'prefix'     => 'filter',
        'fieldModel' => 'field',
        'operator'   => 'operator'
    );
    
/**
 * Recebe 'conditions' gerado pelo self::make()
 *
 * @var array
 * @access private
 * @since  1.0
 */
    private $_conditions;

/**
 * Recebe 'params' sem criptografia
 *
 * @var array
 * @access private
 * @since  1.0
 */
    private $_params = array();
    
    
/**
 * Construct
 *
 * No carregamento da classe, grava e acrescenta números de instâncias
 *
 * @access public
 * @since  1.0
 */
    public function __construct(ComponentCollection $collection, $settings = array()) {
        $this->_instance = ++self::$instances;
        $this->_options = array_merge($this->_options, $settings);
    }
    
    
/**
 * Clone
 *
 * Ao clonar a classe, grava e acrescenta números de instâncias
 *
 * @access public
 * @since  1.0
 */
    public function __clone() {
        $this->_instance = ++self::$instances;
        $this->_options['prefix'] .= sprintf('_%s', $this->_instance);
    }
    
    
/**
 * Initialize
 *
 * Executado antes do Controller::beforeFiler()
 *
 * @param object $controller Passa por referencia o Controller
 * @param array $fields Passa as configurações dos campos para pesquisa
 * @access public
 * @since  1.0
 */
    public function initialize(Controller $controller) {
        $this->controller = $controller;
    }
    
    
/**
 * Startup
 *
 * Executado depois do Controller::beforeFiler() mas antes de executar a Action solicitada
 *
 * @param object $controller Passa por referencia o Controller
 * @access public
 * @since  1.0
 */
    public function startup(Controller $controller) {

    }
    
    
/**
 * Before Render
 *
 * Executado antes do Controller:beforeRender()
 *
 * @param object $controller Passa por referencia o Controller 
 * @access public
 * @since  1.0
 */
    public function beforeRender(Controller $controller) {
        
    }
    
    
/**
 * Shutdown
 *
 * Executado depois do Controller:render()
 *
 * @param object $controller Passa por referencia o Controller 
 * @access public
 * @since  1.0
 */
    public function shutdown(Controller $controller) {
        
    }
    
    
/**
 * Before Redirect
 *
 * Executado antes do Controller:redirect()
 *
 * @param object $controller Passa por referencia o Controller
 * @param array $url
 * @param string $status
 * @param boolean $exit
 * @access public
 * @since  1.0
 */
    public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
        
    }
    
    
/**
 * Encripty
 *
 * @param string $string
 * @return string
 * @access protected
 * @since  1.0
 */
    protected function _encrypt($string) {
        return (!is_string($string)) ? '' : base64_encode($string);
    }
    
    
/**
 * Decripty
 *
 * @param string $string
 * @return string
 * @access protected
 * @since  1.0
 */
    protected function _decrypt($string) {
        return (!is_string($string)) ? '' : base64_decode($string);
    }
    
    
/**
 * Get Form Options
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getFormOptions() {
        return $this->_options['form'];
    }
    
    
/**
 * Get Field Model
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getFieldModel() {
        return $this->_options['fieldModel'];
    }
    
    
/**
 * Get Operator
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getOperator() {
        return $this->_options['operator'];
    }
    
    
/**
 * Get Conditions
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getConditions() {
        return $this->_conditions;
    }
    
    
/**
 * Get Auto Paginate
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getAutoPaginate() {
        return $this->_options['autoPaginate'];
    }
    
    
/**
 * Set Auto Paginate
 *
 * @param string $autoPaginate
 * @throws Exception Quando $autoPaginate não for boleana
 * @access public
 * @since  1.0
 */
    public function setAutoPaginate($autoPaginate) {

        if (!is_bool($autoPaginate)) {
            throw new Exception('$autoPaginate type must be boolean');
        }

        $this->_options['autoPaginate'] = $autoPaginate;
    }


/**
 * Get Auto Like Explode
 *
 * @return string
 * @access public
 * @since  2.0
 */
    public function getAutoLikeExplode() {
        return $this->_options['autoLikeExplode'];
    }
    
    
/**
 * Set Auto Like Explode
 *
 * @param string $autoLikeExplode
 * @throws Exception Quando $autoLikeExplode não for boleana
 * @access public
 * @since  2.0
 */
    public function setAutoLikeExplode($autoLikeExplode) {

        if (!is_bool($autoLikeExplode)) {
            throw new Exception('$autoLikeExplode type must be boolean');
        }

        $this->_options['autoLikeExplode'] = $autoLikeExplode;
    }

/**
 * Get Explode Char
 *
 * @return string
 * @access public
 * @since  2.0
 */
    public function getExplodeChar() {
        return $this->_options['explodeChar'];
    }
    
    
/**
 * Set Explode Char
 *
 * @param string $autoLikeExplode
 * @throws Exception Quando $autoLikeExplode não for string
 * @access public
 * @since  2.0
 */
    public function setExplodeChar($explodeChar) {

        if (!is_string($explodeChar)) {
            throw new Exception('$autoLikeExplode type must be string');
        }

        $this->_options['explodeChar'] = $explodeChar;
    }


/**
 * Get Explode Concatenate
 *
 * @return string
 * @access public
 * @since  2.0
 */
    public function getExplodeConcatenate() {
        return $this->_options['explodeConcatenate'];
    }


/**
 * Set Explode Concatenate
 *
 * @param string $autoLikeExplode
 * @throws Exception Quando $autoLikeExplode não for string
 * @throws Exception Quando $autoLikeExplode deve ser: 'AND' ou 'OR'
 * @access public
 * @since  2.0
 */
    public function setExplodeConcatenate($explodeConcatenate) {

        if (!is_string($explodeConcatenate)) {
            throw new Exception('$autoLikeExplode type must be string');
        }

        if (mb_strtolower($explodeConcatenate, 'utf-8') != 'AND' &&  mb_strtolower($explodeConcatenate, 'utf-8') != 'OR') {
            throw new Exception("$autoLikeExplode must be: 'AND' or 'OR'");
        }

        $this->_options['explodeConcatenate'] = $explodeConcatenate;
    }


/**
 * Get Prefix
 *
 * @return string
 * @access public
 * @since  1.0
 */
    public function getPrefix() {
        return $this->_options['prefix'];
    }
    
    
/**
 * Set Prefix
 *
 *
 * @param string $prefix
 * @return string
 * @throws Exception Quando $prefix não for uma string
 * @throws Exception Quando $prefix estiver vazia
 * @access public
 * @since  1.0
 */
    public function setPrefix($prefix) {

        if (!is_string($prefix)) {
            throw new Exception('$prefix type must be a string');
        }
        
        if (empty($prefix)) {
            throw new Exception("$prefix can't be empty");
        }
        
        $this->_options['prefix'] = $prefix;
    }
    
    
/**
 * Has Field
 *
 * @param string $field
 * @return boolean
 * @access public
 * @since  1.0
 */
    public function hasField($field) {

        if (isset($this->_options['filters'][$field])) {
            return true;
        } else {
            foreach ($this->_options['filters'] as $key => $value) {
                if (is_string($value)) {
                    if ($value == $field) {
                        return true;
                    }
                }
            }
        }
        
        return false;
    }
    
    
/**
 * Get Field Values
 *
 * @param string $field
 * @return array
 * @access public
 * @since  1.0
 */
    public function getFieldValues($field) {
        
        $values = array();
        
        if (isset($this->_options['filters'][$field])) {
            if (is_array($this->_options['filters'][$field])) {
                $values += $this->_foreachFieldForValues($this->_options['filters'][$field]);
            }
        }
        
        return (count($values) == 0) ? null : $values;
    }
    
    
/**
 * Foreach Field For Values
 *
 * @param array $array
 * @return array
 * @access public
 * @since  1.0
 */
    public function _foreachFieldForValues($array) {

        $result = array();
        
        foreach ($array as $key => $value) {
            if (isset($array[$key]['value'])) {
                if (is_array($array[$key]['value'])) {
                    $result += $array[$key]['value'];
                }
            } else {
                if (is_array($value)) {
                    $result += $this->_foreachFieldForValues($value);
                }
            }
        }
        
        return $result;
    }
    
    
/**
 * Set Filters
 *
 * @param array $filters
 * @throws Exception Quando $filters não for um array
 * @access public
 * @since  1.0
 */
    public function setFilters($filters = null) {

        if (!is_array($filters)) {
            throw new Exception('$filters type must be array');
        }
        
        $this->_options['filters'] = $filters;
    }
    

/**
 * Merge
 *
 * @param string $default
 * @param array $options
 * @return array
 * @access public
 * @since  1.0
 */
    public function merge($default, $options = null) {
        $return   = array('' => $default);
        $return[] = $options;
        return $return;
    }

    
/**
 * Add Filters
 *
 * Diferente do método setFilters(), este adiciona esta condição
 *
 * @param array $filters
 * @throws Exception Quando $filters não for um array
 * @access public
 * @since  1.0
 */
    public function addFilters($filters = null) {

        if (!is_array($filters)) {
            throw new Exception('$filters type must be a array');
        }
        
        if (isset($this->_options['filters'])) {
            $this->_options['filters'] += $filters;
        } else {
            $this->_options['filters'] = $filters;
        }
    }
    
    
/**
 * Make
 *
 * Gera o array 'conditions' para o componente 'Paginator' do Controller
 *
 * @return array Conditions for method find() or pagiante()
 * @access public
 * @since  1.0
 */
    public function make() {
        
        /**
         * Verifica parâmetros enviados via POST
         */
        if (isset($this->controller->request->data[$this->_options['prefix']])) {
            /**
             * Monta a url com elementos enviados pelo formulário,
             * onde o resultador será algo parecido como:
             * example.com/cake/posts/index/Search.keywords:mykeyword/Search.tag_id:3
             */

            $url = array();
            $get = array();
            
            foreach ($this->controller->request['url'] as $key => $value) {
                if ($key != 'url') {
                    $get += array($key => $value);
                }
            }
            
            if (count($get) > 0) {
                $url['?'] = $get;
            }
            
            
            foreach ($this->controller->request->data[$this->_options['prefix']] as $key => $value) {

                if (!is_array($value)) {
                    $url[$this->_encrypt(sprintf('%s.%s', $this->_options['prefix'], $key))] = $this->_encrypt($value);
                } else {
                    foreach ($value as $k => $v) {
                        $url[$this->_encrypt(sprintf('%s.%s.%s', $this->_options['prefix'], $key, $k))] = $this->_encrypt($v);
                    }
                }
            }
            
            $this->controller->redirect($url, null, true);
        }
        
        
        
        /**
         * Envia componente para a View
         */
        $varName = 'FilterResults';
        if ($this->_instance > 1) {
            $varName = sprintf('%s_%s', $varName, $this->_instance);
        }
        $this->controller->set($varName, $this);
        
        

        /**
         * Se houver parametros gera 'conditions' para o filtro
         */
        if (count($this->controller->params['named']) == 0) {
            return array();
        }
        
        if ($this->_check() > 0) {
            $this->_conditions = $this->_filterFields();
            if ($this->_options['autoPaginate']) {
                $this->controller->paginate['conditions'][] = $this->_conditions;
            }
            return $this->_conditions;
        }
        
    }
    
    
/**
 * Check
 *
 * Verifica se algum parâmetro foi enviado, tira criptografia,
 * grava parâmetros em uma variável local e retorna o número
 * de parâmetros encontrados
 *
 * @return int
 * @access protected
 * @since  1.0
 */
    protected function _check() {
        
        // Tira criptografia de todos os parametros
        // e grava em variavel local
        foreach ($this->controller->params['named'] as $key => $value) {
            $this->_params[$this->_decrypt($key)] = $this->_decrypt($value);
        }
        
        // Variável privada que conta campos encontrados
        $count = 0;
        
        foreach ($this->_params as $key => $value) {
            if (strpos($key, $this->_options['prefix']) > -1) {
                $count++;
            }
        }
        
        return $count;
    }
    
    
/**
 * Filter Fields
 *
 * Veritica todos os parametros enviados e chama função
 * para gerar as 'conditions' do campo
 *
 * @return array
 * @access protected
 * @since  1.0
 */
    protected function _filterFields() {

        $result = array();
        
        foreach ($this->_options['filters'] as $key => $value) {
            $result += (is_array($value))
                     ? $this->_makeConditions($key, $value)
                     : $this->_makeConditions($value);
        }
        
        return $result;
    }
    

/**
 * Make Conditions
 *
 * Gera a 'condition' para cada parâmetro enviado
 *
 * @param array $field
 * @param array $options
 * @access protected
 * @since  1.0
 */
    protected function _makeConditions($field, $options = null) {
        
        // Array privado da função
        $condition = array();
        
        
        /**
         * Campo sem nenhum parâmetro
         */
        if (!isset($options)) {
            return $this->_makeConditionsWithoutOptions($field);
        }
        
        
        /**
         * Campos com parâmetros
         */
        foreach ($options as $key => $option) {

            switch (strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $condition += array(
                        $key => $this->_makeConditions($field, $option)
                    );
                    break;
                
                default:
                    
                    $fieldModel = (is_array($option)) ? $fieldModel = $key : $fieldModel = $option;
                    
                    /**
                     * Verifica se parametros do fieldModel foram enviados
                     */
                    if (isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)])) {

                        $operator = (isset($options[$fieldModel]['operator'])) ? $options[$fieldModel]['operator'] : '=';
                        
                        if (isset($options[$fieldModel]['value'])) {
                            $value = (is_array($options[$fieldModel]['value'])) ? $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)] : $options[$fieldModel]['value'];
                        } else {
                            $value = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                        }

                        // Sai se não houver valor
                        if (empty($value)) {
                            return array();
                        }


                        if ($operator == 'BETWEEN') {
                            $condition += $this->_conditionsForOperatorBetween($field, $options, $fieldModel, $operator, $value);
                        } else {

                            $beforeValue = $this->_defaultOptionsValue($operator, $options, $fieldModel, 'beforeValue');
                            $afterValue  = $this->_defaultOptionsValue($operator, $options, $fieldModel, 'afterValue');

                            $explodeChar        = $this->_defaultOptionsExplode($operator, $options, $fieldModel, 'explodeChar');
                            $explodeConcatenate = $this->_defaultOptionsExplode($operator, $options, $fieldModel, 'explodeConcatenate');

                            if ($this->__isMayExplodeValue($operator, $options, $fieldModel)) {
                                $condition += $this->_valueConcatenate($fieldModel, $operator, $explodeChar, $explodeConcatenate, $value, $beforeValue, $afterValue);
                            } else {
                                $condition += array(
                                    sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value, $afterValue)
                                );
                            }

                            $this->controller->request->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                        }

                    }
                    

            }
        }
        
        return $condition;
    }


/**
 * Default Options Value
 * 
 * @param string $operator
 * @param array $options
 * @param string $fieldModel
 * @param string $optionValue
 * @return string
 * @access protected
 * @since 2.0
 */
    protected function _defaultOptionsValue($operator, $options, $fieldModel, $optionValue) {

        $default = (isset($options[$fieldModel][$optionValue])) ? $options[$fieldModel][$optionValue] : '';

        if (!$default) {
            if (mb_strtolower($operator, 'utf-8') == 'like' || mb_strtolower($operator, 'utf-8') == 'not like') {
                $default = '%';
            }
        }

        return $default;
    }


/**
 * Default Options Explode
 * 
 * @param string $operator
 * @param array $options
 * @param string $fieldModel
 * @param string $optionExplode
 * @return string
 * @access protected
 * @since 2.0
 */
    protected function _defaultOptionsExplode($operator, $options, $fieldModel, $optionExplode) {

        return (isset($options[$fieldModel][$optionExplode]))
             ? $options[$fieldModel][$optionExplode]
             : $this->_options[$optionExplode];
    }


/**
 * Is May Explode Value
 * 
 * Função que verifica a possíbilidade de "explodir" o valor
 * 
 * @param string $operator
 * @param array $options
 * @return boolean
 * @access private
 * @since 2.0
 */
    private function __isMayExplodeValue($operator, $options, $fieldModel) {

        $fieldOption = (isset($options[$fieldModel]['explode']))
                     ? $options[$fieldModel]['explode']
                     : null;

        $fieldOption = (is_bool($fieldOption))
                     ? $fieldOption
                     : null;

        if (is_null($fieldOption)) {
            if (mb_strtolower($operator, 'utf-8') == 'like' || mb_strtolower($operator, 'utf-8') == 'not like') {
                if ($this->_options['autoLikeExplode']) {
                    return true;
                }
            }
        }

        if($fieldOption) {
            return true;
        }

        return false;
    }


/**
 * Condition For Operator Between
 * 
 * @param  string $field
 * @param  array $options
 * @param  string $fieldModel
 * @param  string $operator
 * @param  string $value
 * @return array
 * @author Vinícius Arantes <vinicius.big@gmail.com>
 */
    protected function _conditionsForOperatorBetween($field, $options, $fieldModel, $operator, $value) {

        // Verifica a existencia dos dois parâmetros
        if(!isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)]) || !isset($this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)])) {
                                
            if (isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)])) {
                $$this->controller->request->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
            }

            if (isset($this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)])) {
                $$this->controller->request->data[$this->_options['prefix']][$field.'2'] = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];
            }

            return array();
        }
                            
                            
        $value2 = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];
                           
        // Altera o formato da data para formato de banco
        if(isset($options[$fieldModel]['convertDate']) && $options[$fieldModel]['convertDate']) {
            $value = implode(preg_match("~\/~", $value) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $value) == 0 ? "-" : "/", $value)));
            $value2 = implode(preg_match("~\/~", $value2) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $value2) == 0 ? "-" : "/", $value2)));
        }
                            
                            
        $value     = array($value, $value2);
        $operator  = 'BETWEEN ? AND ?';
        $condition = array(sprintf('%s %s', $fieldModel, $operator) => $value);
                            
        $$this->controller->request->data[$this->_options['prefix']][$field]     = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
        $$this->controller->request->data[$this->_options['prefix']][$field.'2'] = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];

        return $condition;
    }


/**
 * Make Conditions Whitout Options
 *
 * Gera a 'condition' para cada campos sem parametros
 *
 * @param array $field
 * @return array
 * @access protected
 * @since  2.0
 */
    protected function _makeConditionsWithoutOptions($field) {

        if (!isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)])) {
            return array();
        }
            
        if (!isset($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)])) {
            return array();
        }
        

        /**
         * Define valores padrões
         */
        $fieldModel  = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)];
        $value       = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
        $beforeValue = '';
        $afterValue  = '';

        $explodeChar        = $this->_options['explodeChar'];
        $explodeConcatenate = $this->_options['explodeConcatenate'];

                
        if (!isset($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)])) {
            $operator    = 'like';
            $beforeValue = $this->_defaultOptionsValue($operator, $this->_options, $fieldModel, 'beforeValue');
            $afterValue  = $this->_defaultOptionsValue($operator, $this->_options, $fieldModel, 'afterValue');
            $beforeValue = '%';
            $afterValue  = '%';
        } else {

            $operator = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)];
            
            switch(mb_strtolower($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)], 'utf-8')) {
                case 'like':
                case 'not like':
                    $beforeValue = '%';
                    $afterValue  = '%';
                    break;
                    
                case 'likebegin':
                    $operator   = 'LIKE';
                    $afterValue = '%';
                    break;
                    
                case 'likeend':
                    $operator    = 'LIKE';
                    $beforeValue = '%';
            }
            
            $this->controller->request->data[$this->_options['prefix']][$this->_options['operator']][$field] = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)];
        }

            
        if ($this->__isMayExplodeValue($operator)) {
            $condition = $this->_valueConcatenate($fieldModel, $operator, $value, $beforeValue, $afterValue);
        } else {
            $condition = array(
                sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value, $afterValue)
            );
        }
        
        
        $this->controller->request->data[$this->_options['prefix']][$this->_options['fieldModel']][$field] = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)];
        $this->controller->request->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
        
        return $condition;
    }


/**
 * Value Concatenate Like
 * 
 * Make concatenate conditions LIKE
 * 
 * @param string $fieldModel
 * @param string $operator
 * @param string $explodeChar
 * @param string $explodeConcatenate
 * @param string $value
 * @param string $beforeValue
 * @param string $afterValue 
 * @return array
 * @since 2.0
 */
    protected function _valueConcatenate($fieldModel, $operator, $explodeChar, $explodeConcatenate, $value, $beforeValue = '', $afterValue = '') {

        $values = explode($explodeChar, $value);

        foreach ($values as $key => $value) {
            $condition[$explodeConcatenate][$key] = array(
                sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value, $afterValue)
            );
        }

        return $condition;
    }

    
/**
 * Get Model Fields
 *
 * Pega automaticamente os campos do Model padrão
 *
 * @return array
 * @access protected
 * @since  1.0
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
 * Get Operation
 * 
 * @param type $name
 * @return type
 * @access public
 * @since  1.1
 * @author Vinícius Arantes <vinicius.big@gmail.com>
 */
    public function getOperation($name) {
        
        foreach ($this->_options['filters'][$name] as $key => $value) {
            if (isset($value['operator'])) {
                return $value['operator'];
            }
        }

        return '';
    }


/**
 * Set Paginate
 * 
 * Define opções do Component Paginator para CakePHP2.2+
 * 
 * @param string $option Opção
 * @param type   $value  Valor
 * @access public
 * @since 2.0
 */
    public function setPaginate($option, $value) {
        $paginate                   = $this->controller->paginate;
        $paginate[$option]          = $value;
        $this->controller->paginate = $paginate;
    }
    
}
