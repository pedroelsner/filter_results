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
 * @since      v 2.0
 */


/**
 * Filter Results Component
 *
 * @use        Component
 * @package    filter_results
 * @subpackage filter_results.filter_results
 * @link       http://www.github.com/pedroelsner/FilterResults2
 */
class FilterResultsComponent extends Component
{
    
/**
 * Controle do número de instâncias
 *
 * @var int
 * @access public
 * @static
 */
    public static $instances = 0;
    
/**
 * Idencificação da instância
 *
 * @var int
 * @access private
 */
    private $_instance;
    
/**
 * Configurações do Component
 *
 * @var array
 * @access private
 */
    private $_options = array(
        'autoPaginate' => false,
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
 */
    private $_conditions;

/**
 * Recebe 'params' sem criptografia
 *
 * @var array
 * @access private
 */
    private $_params = array();
    
    
/**
 * Construct
 *
 * No carregamento da classe, grava e acrescenta números de instâncias
 *
 * @access public
 */
    public function __construct(ComponentCollection $collection, $settings = array())
    {
        $this->_instance = ++self::$instances;
        $this->_options = array_merge($this->_options, $settings);
    }
    
    
/**
 * Clone
 *
 * Ao clonar a classe, grava e acrescenta números de instâncias
 *
 * @access public
 */
    public function __clone()
    {
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
 */
    public function initialize(Controller $controller)
    {
        $this->controller = $controller;
        $this->params = $controller->request;
    }
    
    
/**
 * Startup
 *
 * Executado depois do Controller::beforeFiler() mas antes de executar a Action solicitada
 *
 * @param object $controller Passa por referencia o Controller
 * @access public
 */
    public function startup(Controller $controller)
    {
    
    }
    
    
/**
 * Before Render
 *
 * Executado antes do Controller:beforeRender()
 *
 * @param object $controller Passa por referencia o Controller 
 * @access public
 */
    public function beforeRender(Controller $controller)
    {
    
    }
    
    
/**
 * Shutdown
 *
 * Executado depois do Controller:render()
 *
 * @param object $controller Passa por referencia o Controller 
 * @access public
 */
    public function shutdown(Controller $controller)
    {
    
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
 */
    public function beforeRedirect(Controller $controller, $url, $status=null, $exit=true)
    {
    
    }
    
    
/**
 * Encripty
 *
 * @param string $string
 * @return string
 * @access protected
 */
    protected function _encrypt($string)
    {
        if ( !(is_string($string)) )
        {
            return '';
        }
        
        return base64_encode($string);
    }
    
    
/**
 * Decripty
 *
 * @param string $string
 * @return string
 * @access protected
 */
    protected function _decrypt($string)
    {
        if ( !(is_string($string)) )
        {
            return '';
        }
        
        return base64_decode($string);
    }
    
    
/**
 * Get Form Options
 *
 * @return string
 * @access public
 */
    public function getFormOptions()
    {
        return $this->_options['form'];
    }
    
    
/**
 * Get Field Model
 *
 * @return string
 * @access public
 */
    public function getFieldModel()
    {
        return $this->_options['fieldModel'];
    }
    
    
/**
 * Get Operator
 *
 * @return string
 * @access public
 */
    public function getOperator()
    {
        return $this->_options['operator'];
    }
    
    
/**
 * Get Conditions
 *
 * @return string
 * @access public
 */
    public function getConditions()
    {
        return $this->_conditions;
    }
    
    
/**
 * Get Auto Paginate
 *
 * @return string
 * @access public
 */
    public function getAutoPaginate()
    {
        return $this->_options['autoPaginate'];
    }
    
    
/**
 * Set Auto Paginate
 *
 * @param string $autoPaginate
 * @return boolean
 * @access public
 */
    public function setAutoPaginate($autoPaginate)
    {
        
        if ( !(is_bool($autoPaginate)) )
        {
            return false;
        }
        
        
        $this->_options['autoPaginate'] = $autoPaginate;
        return true;
        
    }
    
    
/**
 * Get Prefix
 *
 * @return string
 * @access public
 */
    public function getPrefix()
    {
        return $this->_options['prefix'];
    }
    
    
/**
 * Set Prefix
 *
 *
 * @param string $prefix
 * @return string
 * @access public
 */
    public function setPrefix($prefix)
    {
        if ( !(is_string($prefix)) )
        {
            return false;
        }    
        
        if (empty($prefix))
        {
            return false;
        }
        
        
        $this->_options['prefix'] = $prefix;
        return true;
    }
    
    
/**
 * Has Field
 *
 * @param string $field
 * @return boolean
 * @access public
 */
    public function hasField($field)
    {
        if (isset($this->_options['filters'][$field]))
        {
            return true;
        }
        else
        {
            foreach ($this->_options['filters'] as $key => $value)
            {
                if (is_string($value))
                {
                    if ($value == $field)
                    {
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
 */
    public function getFieldValues($field)
    {
        $values = array();
        
        if (isset($this->_options['filters'][$field]))
        {
            if (is_array($this->_options['filters'][$field]))
            {
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
 */
    public function _foreachFieldForValues($array)
    {
        $result = array();
        
        foreach ($array as $key => $value)
        {
            if (isset($array[$key]['value']))
            {
                if (is_array($array[$key]['value']))
                {
                    $result += $array[$key]['value'];
                }
            }
            else
            {
                if (is_array($value))
                {
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
 * @return boolean
 * @access public
 */
    public function setFilters($filters = null)
    {
        
        if ( !(is_array($filters)) )
        {
            return false;
        }
        
        
        $this->_options['filters'] = $filters;
        return true;
        
    }
    

/**
 * Merge
 *
 * @param string $default
 * @param array $options
 * @return boolean
 * @access public
 */
    public function merge($default, $options)
    {
        $return = array('' => $default);
        $return[] = $options;
        return $return;
    }

    
/**
 * Add Filters
 *
 * Diferente do método setFilters(), este adiciona esta condição
 *
 * @param array $filters
 * @return boolean
 * @access public
 */
    public function addFilters($filters = null)
    {
        
        if ( !(is_array($filters)) )
        {
            return false;
        }
        
        if (isset($this->_options['filters']))
        {
            $this->_options['filters'] += $filters;
        }
        else
        {
            $this->_options['filters'] = $filters;
        }
        return true;
        
    }
    
    
/**
 * Make
 *
 * Gera o array 'conditions' para o componente 'Paginator' do Controller
 *
 * @access public
 */
    public function make()
    {
        
        /**
         * Verifica parâmetros enviados via POST
         */
        if (isset($this->params->data[$this->_options['prefix']]))
        {
            /**
             * Monta a url com elementos enviados pelo formulário,
             * onde o resultador será algo parecido como:
             * example.com/cake/posts/index/Search.keywords:mykeyword/Search.tag_id:3
             */

            $url = array();
            $get = array();
            
            foreach ($this->params['url'] as $key => $value)
            {
                if ($key != 'url')
                {
                    $get += array($key => $value);
                }
            }
            
            if (count($get) > 0)
            {
                $url['?'] = $get;
            }
            
            
            foreach ($this->params->data[$this->_options['prefix']] as $key => $value)
            {
                if ( !(is_array($value)) )
                {
                    $url[$this->_encrypt(sprintf('%s.%s', $this->_options['prefix'], $key))] = $this->_encrypt($value);
                }
                else
                {
                    foreach ($value as $k => $v)
                    {
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
        if ($this->_instance > 1)
        {
            $varName = sprintf('%s_%s', $varName, $this->_instance);
        }
        $this->controller->set($varName, $this);
        
        

        /**
         * Se houver parametros gera 'conditions' para o filtro
         */
        if (count($this->controller->params['named']) == 0)
        {
            return array();
        }
        
        if ($this->_check() > 0)
        {
            // Armazena conditions no Componente
            $this->_conditions = $this->_filterFields();
            
            if ($this->_options['autoPaginate'])
            {
                $this->controller->paginate['conditions'][] = $this->_conditions;
            }
            
            // Retorna conditions
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
 */
    protected function _check()
    {
        
        // Tira criptografia de todos os parametros
        // e grava em variavel local
        foreach ($this->controller->params['named'] as $key => $value)
        {
            $this->_params[$this->_decrypt($key)] = $this->_decrypt($value);
        }
        
        
        // Variável privada que conta campos encontrados
        $count = 0;
        
        foreach ($this->_params as $key => $value)
        {
            if (strpos($key, $this->_options['prefix']) > -1)
            {
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
 */
    protected function _filterFields()
    {
        $result = array();
        
        foreach ($this->_options['filters'] as $key => $value)
        {
            $result += (is_array($value)) ? $this->_makeConditions($key, $value) : $this->_makeConditions($value);
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
 */
    protected function _makeConditions($field, $options = null)
    {
        
        // Array privado da função
        $condition = array();
        
        
        /**
         * Campo sem nenhum parâmetro
         */
        if ( !(isset($options)) )
        {
            if ( !(isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)])) )
            {
                return $condition;
            }
            
            if ( !(isset($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)])) )
            {
                return $condition;
            }
            
            
            $fieldModel = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)];
            $value = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
            $beforeValue = '';
            $afterValue = '';
                
            if ( !(isset($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)])) )
            {
                $operator = 'LIKE';
                $beforeValue = '%';
                $afterValue = '%';
            }
            else
            {
                $operator = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)];
                
                switch(mb_strtolower($this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)], 'UTF-8'))
                {
                    case 'like':
                    case 'not like':
                        $beforeValue = '%';
                        $afterValue = '%';
                        break;
                        
                    case 'likebegin':
                        $operator = 'LIKE';
                        $afterValue = '%';
                        break;
                        
                    case 'likeend':
                        $operator = 'LIKE';
                        $beforeValue = '%';
                        break;
                }
                
                $this->params->data[$this->_options['prefix']][$this->_options['operator']][$field] = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['operator'], $field)];
            }

            
            if (mb_strtolower($operator, 'utf-8') == 'like' || mb_strtolower($operator, 'utf-8') == 'not like')
            {
                $values = explode(" ", $value);
                foreach ($values as $key2 => $value2)
                {
                    $condition['AND'][$key2] = array(
                        sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value2, $afterValue)
                    );
                }
            }
            else
            {
                $condition = array(
                    sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value, $afterValue)
                );
            }
            
            
            $this->params->data[$this->_options['prefix']][$this->_options['fieldModel']][$field] = $this->_params[sprintf('%s.%s.%s', $this->_options['prefix'], $this->_options['fieldModel'], $field)];
            $this->params->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
            
            return $condition;
        }
        
        
        /**
         * Campos com parâmetros
         */
        foreach ($options as $key => $value)
        {
            switch (strtolower($key))
            {
                case 'not':
                case 'and':
                case 'or':
                    $condition += array(
                        $key => $this->_makeConditions($field, $value)
                    );
                    break;
                
                default:
                    
                    $fieldModel = (is_array($value)) ? $fieldModel = $key : $fieldModel = $value;
                    
                    /**
                     * Verifica se parametros do fieldModel foram enviados
                     */
                    if (isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)]))
                    {
                        $operator = (isset($options[$fieldModel]['operator'])) ? $options[$fieldModel]['operator'] : '';
                        
                        if (isset($options[$fieldModel]['value']))
                        {
                            $value = (is_array($options[$fieldModel]['value'])) ? $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)] : $options[$fieldModel]['value'];
                        }
                        else
                        {
                            $value = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                        }

                        if ( empty($value) )
                        {
                            return $condition;
                        }


                        /**
                         * POR VINICIUS ARANTES
                         * Conditions para operador 'BETWEEN'
                         */
                        if ($operator == 'BETWEEN')
                        {
                            // Verifica a existencia dos dois parâmetros
                            if( !isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)]) || !isset($this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)]) ) 
                            {
                                
                                if (isset($this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)]))
                                {
                                    $this->controller->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                                }

                                if (isset($this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)]))
                                {
                                    $this->controller->data[$this->_options['prefix']][$field.'2'] = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];
                                }

                                $this->controller->Session->setFlash(__('Informe os dois valores do intervalo.', true), 'alert', array('class' => 'alert-error'));
                                break;

                            }
                            else
                            {
                                $value2 = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];
                            }
                            
                            
                            // Altera o formato da data para formato de banco
                            if(isset($options[$fieldModel]['convertDate']) && $options[$fieldModel]['convertDate'])
                            {
                                $value = implode(preg_match("~\/~", $value) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $value) == 0 ? "-" : "/", $value)));
                                $value2 = implode(preg_match("~\/~", $value2) == 0 ? "/" : "-", array_reverse(explode(preg_match("~\/~", $value2) == 0 ? "-" : "/", $value2)));
                            }
                            
                            // Cria conditions de between em formato cake
                            $value = array($value, $value2);
                            $operator = 'BETWEEN ? AND ?';
                            $condition += array(sprintf('%s %s', $fieldModel, $operator) => $value);
                            
                            // Deixa o form preenchido para os dois campos
                            $this->controller->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                            $this->controller->data[$this->_options['prefix']][$field.'2'] = $this->_params[sprintf('%s.%s2', $this->_options['prefix'], $field)];
                            
                        }
                        else
                        {

                            $beforeValue = (isset($options[$fieldModel]['beforeValue'])) ? $options[$fieldModel]['beforeValue'] : '';
                            $afterValue = (isset($options[$fieldModel]['afterValue'])) ? $options[$fieldModel]['afterValue'] : '';

                            if (mb_strtolower($operator, 'utf-8') == 'like' || mb_strtolower($operator, 'utf-8') == 'not like')
                            {
                                $values = explode(" ", $value);
                                foreach ($values as $key2 => $value2)
                                {
                                    $condition['AND'][$key2] = array(
                                       sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value2, $afterValue)
                                    );
                                }
                            }
                            else
                            {
                                $condition += array(
                                    sprintf('%s %s', $fieldModel, $operator) => sprintf('%s%s%s', $beforeValue, $value, $afterValue)
                                );
                            }

                            $this->params->data[$this->_options['prefix']][$field] = $this->_params[sprintf('%s.%s', $this->_options['prefix'], $field)];
                            
                        }

                    }
                    break;
            }
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
 */
    protected function getModelFields()
    {
        
        if( !(isset($this->controller->modelNames[0])) )
        {
            return array();
        }
        
        $fields = array();
        foreach($this->controller->{$this->controller->modelNames[0]}->_schema as $key => $value)
        {
            $fields[sprintf('%s.%s', $this->controller->modelNames[0], $key)] = $key;
        }
        
        return $fields;
    }


    /**
     * FUNÇÃO CRIADA POR VINICIUS ARANTES (vinicius.big@gmail.com)
     * @param type $name
     * @return type 
     */
    function getOperation($name)
    {
        foreach ($this->_options['filters'][$name] as $key => $value)
        {
            if (isset($value['operator']))
            {
                return $value['operator'];
            }
        }
        return '';
    }
    
}
