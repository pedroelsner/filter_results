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
 * @link       http://www.github.com/pedroelsner/filter_results
 */
class FilterResultsComponent extends Component {
    
/**
 * Controle do número de instâncias
 *
 * @var    int
 * @access public
 * @static
 * @since  1.0
 */
    public static $instances = 0;

/**
 * Recebe opções do filtro atual para função self::_makeConditions()
 *
 * @var    array
 * @access private
 * @static
 * @since  2.0
 */
    private static $_filter = array();
    
/**
 * Idencificação da instância
 *
 * @var    int
 * @access private
 * @since  1.0
 */
    private $_instance;
    
/**
 * Configurações do Component
 *
 * @var    array
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
 * @var    array
 * @access private
 * @since  1.0
 */
    private $_conditions = array();

/**
 * Recebe 'params' sem criptografia
 *
 * @var    array
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
 * @param  object $controller Passa por referencia o Controller
 * @param  array  $fields     Passa as configurações dos campos para pesquisa
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
 * @param  object $controller Passa por referencia o Controller
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
 * @param  object $controller Passa por referencia o Controller 
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
 * @param  object $controller Passa por referencia o Controller 
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
 * @param  object  $controller Passa por referencia o Controller
 * @param  array   $url
 * @param  string  $status
 * @param  boolean $exit
 * @access public
 * @since  1.0
 */
    public function beforeRedirect(Controller $controller, $url, $status = null, $exit = true) {
        
    }
    
    
/**
 * Encripty
 *
 * @param  string $string
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
 * @param  string $string
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

        if (!is_array($this->_conditions)) {
            return self::make();
        }

        if (count($this->_conditions) == 0) {
            return self::make();
        }

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
 * @param  string $autoPaginate
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
 * @param  string $autoLikeExplode
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
 * @param  string $autoLikeExplode
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
 * @param  string $autoLikeExplode
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
 * @param  string $prefix
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
 * @param  string $field
 * @return boolean
 * @access public
 * @since  1.0
 */
    public function hasField($field) {
        return self::_searchField($field, $this->_options['filters']);
    }


/**
 * Search Field
 * 
 * @param  string  $field  
 * @param  type    $filters
 * @return boolean
 * @access protected
 * @since  2.0
 */
    protected function _searchField($field, $filters) {

        $hasField = false;

        foreach($filters as $key => $value) {

            switch (mb_strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $hasField = self::_searchField($field, $value);
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
 * Get Field Values
 *
 * @param  string $field
 * @return array
 * @access public
 * @since  1.0
 */
    public function getFieldValues($field) {
        
        $values = array();
        
        if (self::hasField($field)) {
            $options = self::_getFilterOptions($field, $this->_options['filters']);
            $values += self::_foreachFieldForValues($options);
        }
        
        return (count($values) == 0) ? null : $values;
    }
    
    
/**
 * Foreach Field For Values
 *
 * @param  array $array
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
                    $result += self::_foreachFieldForValues($value);
                }
            }
        }
        
        return $result;
    }
    
    
/**
 * Set Filters
 *
 * @param  array $filters
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
 * Values
 *
 * @param  string $label
 * @param  array  $values
 * @return array
 * @since  2.0
 */
    public function values($label, $values) {
        return self::merge($label, $values);
    }


/**
 * Merge
 *
 * DEPRECATED IN 04/07/2012
 * 
 * @param      string $label
 * @param      array  $values
 * @return     array
 * @access     public
 * @deprecated 04/07/2012
 * @since      1.0
 */
    public function merge($label, $values) {
        return array('' => $label) + $values;
    }

    
/**
 * Add Filters
 *
 * Diferente do método setFilters(), este adiciona esta condição
 *
 * @param array $filters
 * @throws Exception Quando $filters não for um array ou string
 * @access public
 * @since  1.0
 */
    public function addFilters($filters = null) {

        if (!is_array($filters)) {
            if (!is_string($filters)) {
                throw new Exception('$filters type must be a array or string');    
            }

            $filters = array($filters);
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
 * DEPRECATED IN 04/07/2012
 * Gera o array 'conditions' para o componente 'Paginator' do Controller
 *
 * @return array Conditions para um método find() or paginate()
 * @access public
 * @deprecated 04/07/2012
 * @since  1.0
 */
    public function make() {
        
        /**
         * Verifica parâmetros enviados via POST
         */
        if (isset($this->controller->request->data[self::getPrefix()])) {
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
            
            
            foreach ($this->controller->request->data[self::getPrefix()] as $key => $value) {

                if (!is_array($value)) {
                    $url[self::_encrypt(sprintf('%s.%s', self::getPrefix(), $key))] = self::_encrypt($value);
                } else {
                    foreach ($value as $k => $v) {
                        $url[self::_encrypt(sprintf('%s.%s.%s', self::getPrefix(), $key, $k))] = self::_encrypt($v);
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
        if (count($this->controller->request->params['named']) == 0) {
            return array();
        }
        
        if (self::_check() > 0) {
            
            $this->_conditions = self::_filterFields($this->_options['filters']);

            if ($this->_options['autoPaginate']) {
                $this->controller->Paginator->paginate['conditions'][] = $this->_conditions;
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
        foreach ($this->controller->request->params['named'] as $key => $value) {
            $this->_params[self::_decrypt($key)] = self::_decrypt($value);
        }
        
        // Variável privada que conta campos encontrados
        $count = 0;
        
        foreach ($this->_params as $key => $value) {
            if (strpos($key,self::getPrefix()) > -1) {
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
    protected function _filterFields($filters) {

        $result = array();

        foreach ($filters as $key => $value) {

            switch (mb_strtolower($key, 'utf-8')) {
                case 'not':
                case 'and':
                case 'or':
                    
                    $conditionOfFilter = self::_filterFields($value);
                    if (count($conditionOfFilter) > 0) {
                        if (!isset($result[$key])) {
                            $result[$key] = self::_filterFields($value);
                        } else {
                            $result[$key] += self::_filterFields($value);
                        }    
                    }
                    break;
                
                default:
                    $result += (is_array($value))
                         ? self::_makeConditions($key, $value)
                         : self::_makeConditions($value);
            }

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
        
        $condition = array();
        
        
        /**
         * Campo sem nenhum parâmetro
         */
        if (!isset($options)) {
            return self::_makeConditionsWithoutOptions($field);
        }
        
        
        /**
         * Campos com parâmetros
         */
        foreach ($options as $key => $value) {

            switch (strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $condition += array(
                        $key => self::_makeConditions($field, $value)
                    );
                    break;
                
                default:
                    
                    /**
                     * Verifica se parametros do campo foram enviados
                     */                    
                    if (!isset($this->_params[sprintf('%s.%s', self::getPrefix(), $field)])) {
                        break;
                    }


                    self::$_filter = array();
                    self::$_filter['field'] = $field;

                    if (is_array($value)) {
                        self::$_filter['fieldModel'] = $key;
                        self::$_filter += $value;
                    } else {
                        self::$_filter['fieldModel'] = (is_array($value)) ? $key : $value;
                    }

                    if (!isset(self::$_filter['value'])) {
                        self::$_filter['value'] = $this->_params[sprintf('%s.%s', self::getPrefix(), $field)];
                    } else {
                        if (is_array(self::$_filter['value'])) {
                            self::$_filter['value'] = $this->_params[sprintf('%s.%s', self::getPrefix(), $field)];
                        }
                    }

                    /**
                     * Verifica se ha valor
                     */                    
                    if (empty(self::$_filter['value'])) {
                        break;
                    }

                    if (!isset(self::$_filter['operator'])) {
                        self::$_filter['operator'] = '=';
                    }

                    self::$_filter['beforeValue'] = self::_defaultOptionsValue('beforeValue');
                    self::$_filter['afterValue']  = self::_defaultOptionsValue('afterValue');

                    if (!isset(self::$_filter['explode'])) {
                        self::$_filter['explode'] = null;
                    }

                    if (!isset(self::$_filter['explodeConcatenate'])) {
                        self::$_filter['explodeConcatenate'] = self::getExplodeConcatenate();
                    }

                    if (!isset(self::$_filter['explodeChar'])) {
                        self::$_filter['explodeChar'] = self::getExplodeChar();
                    }
                    

                    if (mb_strtolower(self::$_filter['operator'], 'utf-8') == 'between') {
                        $condition += self::_conditionsForOperatorBetween();
                    
                    } else {

                        if (self::_isMayExplodeValue()) {
                            $condition += self::_valueConcatenate();
                        } else {
                            $condition += self::_value();
                        }

                        /**
                         * Alimenta formulario com valor filtrado
                         */
                        $this->controller->request->data[self::getPrefix()][$field] = $this->_params[sprintf('%s.%s', self::getPrefix(), self::$_filter['field'])];
                    }

            }
        }
        
        return $condition;
    }


/**
 * Default Options Value
 * 
 * @param string $option
 * @return string
 * @access protected
 * @since 2.0
 */
    protected function _defaultOptionsValue($option) {

        $default = (isset(self::$_filter[$option])) ? self::$_filter[$option] : '';

        if (!$default) {
            
            switch (mb_strtolower(self::$_filter['operator'], 'utf-8')) {
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
 * Is May Explode Value
 * 
 * Função que verifica a possíbilidade de "explodir" o valor
 * 
 * @return boolean
 * @access private
 * @since 2.0
 */
    private function _isMayExplodeValue() {

        if (count(self::$_filter) == 0) {
            return false;
        }

        if (is_null(self::$_filter['explode'])) {

            switch (mb_strtolower(self::$_filter['operator'], 'utf-8')) {
                case 'like':
                case 'not like':
                    return self::getAutoLikeExplode();
                    break;

                default:
                    break;
            }
        }

        if (self::$_filter['explode']) {
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
    protected function _conditionsForOperatorBetween() {

        if (count(self::$_filter) == 0) {
            return array();
        }

        /**
         * Verifica a existencia dos dois parâmetros
         */ 
        if (!isset($this->_params[sprintf('%s.%s', self::getPrefix(), self::$_filter['field'])])
         || !isset($this->_params[sprintf('%s.%s2', self::getPrefix(), self::$_filter['field'])]))
        {
                                
            if (isset($this->_params[sprintf('%s.%s', self::getPrefix(), self::$_filter['field'])])) {
                $$this->controller->request->data[self::getPrefix()][self::$_filter['field']] = $this->_params[sprintf('%s.%s', self::getPrefix(), self::$_filter['field'])];
            }

            if (isset($this->_params[sprintf('%s.%s2', self::getPrefix(), self::$_filter['field'])])) {
                $$this->controller->request->data[self::getPrefix()][self::$_filter['field'].'2'] = $this->_params[sprintf('%s.%s2', self::getPrefix(), self::$_filter['field'])];
            }

            return array();
        }

        self::$_filter['value_between'] = $this->_params[sprintf('%s.%s2', self::getPrefix(), self::$_filter['field'])];
        

        /** 
         * Altera o formato da data para formato de banco
         */
        if (isset(self::$_filter['convertDate'])) {
            if (self::$_filter['convertDate']) {
                self::$_filter['value']         = implode(preg_match("~\/~", self::$_filter['value'])         == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", self::$_filter['value'])         == 0 ? "-" : "/", self::$_filter['value'])));
                self::$_filter['value_between'] = implode(preg_match("~\/~", self::$_filter['value_between']) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", self::$_filter['value_between']) == 0 ? "-" : "/", self::$_filter['value_between'])));
            }
        }
                            
        self::$_filter['value']    = array(self::$_filter['value'], self::$_filter['value_between']);
        self::$_filter['operator'] = 'BETWEEN ? AND ?';
        
        $condition = array(sprintf('%s %s', self::$_filter['fieldModel'], self::$_filter['operator']) => self::$_filter['value']);
        
        $this->controller->request->data[self::getPrefix()][self::$_filter['field']]     = $this->_params[sprintf('%s.%s', self::getPrefix(), self::$_filter['field'])];
        $this->controller->request->data[self::getPrefix()][self::$_filter['field'].'2'] = $this->_params[sprintf('%s.%s2', self::getPrefix(), self::$_filter['field'])];

        return $condition;
    }


/**
 * Make Conditions Whitout Options
 *
 * Gera a 'condition' para cada campos sem parametros
 *
 * @param  array $field
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
            $beforeValue = self::_defaultOptionsValue($operator, $this->_options, $fieldModel, 'beforeValue');
            $afterValue  = self::_defaultOptionsValue($operator, $this->_options, $fieldModel, 'afterValue');
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
                case 'like begin':
                    $operator   = 'LIKE';
                    $afterValue = '%';
                    break;
                    
                case 'likeend':
                case 'like end':
                    $operator    = 'LIKE';
                    $beforeValue = '%';
            }
            
            $this->controller->request->data[$this->_options['prefix']][$this->_options['operator']][$field] = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)];
        }

            
        if (self::_isMayExplodeValue($operator)) {
            $condition = self::_valueConcatenate($fieldModel, $operator, $value, $beforeValue, $afterValue);
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
 * Value
 * 
 * @return array
 * @access protected
 * @since  2.0
 */
    protected function _value() {

        if (count(self::$_filter) == 0) {
            return array();
        }

        return array(
            sprintf('%s %s', self::$_filter['fieldModel'], self::$_filter['operator']) =>
            sprintf('%s%s%s', self::$_filter['beforeValue'], self::$_filter['value'], self::$_filter['afterValue'])
        );
    }


/**
 * Value Concatenate
 * 
 * Concatena os valores explodidos
 * 
 * @return array
 * @access protected
 * @since  2.0
 */
    protected function _valueConcatenate() {

        $condition = array();
        $values = explode(self::$_filter['explodeChar'], self::$_filter['value']);

        if (count($values) > 1) {

            foreach ($values as $k => $v) {
                $condition[self::$_filter['explodeConcatenate']][$k] = array(
                    sprintf('%s %s', self::$_filter['fieldModel'], self::$_filter['operator']) =>
                    sprintf('%s%s%s', self::$_filter['beforeValue'], $v, self::$_filter['afterValue'])
                );
            }

        } else {
            $condition = self::_value();
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
 * @param  type $field
 * @return type
 * @access public
 * @since  1.1
 */
    public function getOperation($field) {

        $options = self::_getFilterOptions($field, $this->_options['filters']);
        foreach ($options as $key => $value) {
            if (isset($value['operator'])) {
                return $value['operator'];
            }
        }

        return '';
    }


/**
 * Search Filter Options
 * 
 * Retorna as opções do filtro
 * 
 * @param  string  $field  
 * @param  type    $filters
 * @return array
 * @access protected
 * @since  2.0
 */
    protected function _getFilterOptions($field, $filters) {

        $return = array();

        foreach($filters as $key => $value) {

            switch (mb_strtolower($key)) {
                case 'not':
                case 'and':
                case 'or':
                    $return = self::_getFilterOptions($field, $value);
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
 * Set Paginate
 * 
 * Define opções do Component Paginator para CakePHP2.2+
 * 
 * @param  string $option Opção
 * @param  type   $value  Valor
 * @access public
 * @since  2.0
 */
    public function setPaginate($option, $value) {
        $paginate                   = $this->controller->paginate;
        $paginate[$option]          = $value;
        $this->controller->paginate = $paginate;
    }
    
}
