# Filter Results

Plugin que gera `conditions` para métodos `find` no CakePHP 2.x a partir de um formulário de pesquisa.

## Compatibilidade

Compatível com CakePHP 2.x + Paginate (Component)
* Versão para CakePHP 1.3: [http://github.com/pedroelsner/filter_results/tree/1.3](http://github.com/pedroelsner/filter_results/tree/1.3 "FilterResults para CakePHP 1.3")

# Instalação

Faça o download do plugin e coloque seu conteúdo dentro de `/app/Plugin/FilterResults2` ou em outro diretório para plugins do CakePHP.

## Ativação

Ative o plugin adicionando ao arquivo __/app/Config/bootstrap.php__:

<pre>
CakePlugin::load('FilterResults');
</pre>

## Configurações

Edite o arquivo __/app/AppController.php__:

<pre>
var $components = array(
    'FilterResults.FilterResults' => array(
        'autoPaginate' => false
    )
);

var $helpers = array(
    'FilterResults.FilterForm'
);
</pre>

Parâmetros de configurações do componente:

*   __autoPaginate:__ Se você definir como TRUE, o Paginate será configurado automaticamente.

# Utilização

Para os exemplos contidos neste documento, vou utilizar como base o seguinte base de dados

<pre>
CREATE TABLE groups (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    created DATETIME NULL,
    modified DATETIME NULL,
    PRIMARY KEY(id),
    UNIQUE(name)
) ENGINE=INNODB;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    username VARCHAR(20) NOT NULL,
    password VARCHAR(100) NOT NULL,
    created DATETIME NULL,
    modified DATETIME NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY(id),
    FOREIGN KEY(group_id) REFERENCES groups(id),
    UNIQUE(username)
) ENGINE=INNODB;
</pre>

# Filtro Simples

Bom, após gerar as telas pelo Bake, vamos colocar um filtro sobre a grid(tabela) para pesquisar um usuário pelo nome(`User.name`). Então vamos configurar o Controller e a View, desta forma:

Arquivo __/app/Controller/UsersController.php__
<pre>
function index()
{
    $this->User->recursive = 0;

    // Filter Results
    $this->FilterResults->addFilters(
        array(
            'filter1' => array(
                'User.name' => array(
                    'operator'    => 'LIKE',
                    'beforeValue' => '%', // Opcional
                    'afterValue'  => '%'  // Opcional
                )
            )
        )
    );
    
    // Paginate
    $this->paginate['order'] = 'User.name ASC';
    $this->paginate['limit'] = 10;
    $this->paginate['conditions'] = $this->FilterResults->make();

    $this->set('users', $this->paginate());
}
</pre>

A configuração aqui é bem simples: Criamos um filtro chamado `filter1` que utilizará o campo `User.name`. Este filtro utiliza o operador `LIKE` e adiciona `%` antes e depois do conteudo a ser filtrado.

Agora temos apenas que fazer o formulário na View em cima da tabela.

Arquivo __/app/View/Users/index.ctp)__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Pronto! Temos um campo que filtra o usuário pelo nome e compatível com o Paginate.

# Filtro Simples + Regra Composta

Vamos agora fazer mais uma regra dentro do filtro `filter1`. Queremos que ele filtre pelo nome(`User.name`) ou pelo nome do usuário(`User.username`).

Alteramos então apenas o nosso Controller:

Arquivo __/app/Controller/UsersController.php__
<pre>
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'OR' => array( // REGRA "OU"
                'User.name' => array(
                    'operator'    => 'LIKE',
                    'beforeValue' => '%',
                    'afterValue'  => '%'
                ),
                'User.username' => array(
                    'operator'    => 'LIKE',
                    'beforeValue' => '%',
                    'afterValue'  => '%'
                )
            )
        )
    )
);
</pre>

A regra `OR` pode ser também `AND` ou `NOT`.

NOTA: Se você definir mais de uma condição sem especificar a regra, o plugin entenderá como `AND` automaticamente.

# Filtro Simples + Regra Fixa

Vamos supor agora que o nosso filtro `filter1` quando informado deva filtrar pelo nome(`User.name`) E somente usuários ativos.

Arquivo __/app/Controller/UsersController.php__
<pre>
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'User.name' => array(
                'operator'    => 'LIKE',
                'beforeValue' => '%',
                'afterValue'  => '%'
            ),
            'User.active' => array(
                'value' => '1'
            )
        )
    )
);
</pre>

# Filtro de Seleção

Vamos mudar nosso filtro. Além de filtrar pelo nome, queremos agora filtrar também pelo grupo do usuário(`Group.name`) desejado através de um campo de seleção.

Arquivo __/app/Controller/UsersController.php__
<pre>
function index()
{
    $this->User->recursive = 0;

    // Filter Results
    $this->FilterResults->addFilters(
        array(
            'filter1' => array(
                'User.name' => array(
                    'operator'    => 'LIKE',
                    'beforeValue' => '%',
                    'afterValue'  => '%'
                )
            ),
            'filter2' => array(
                'User.group_id' => array(
                    'value' => $this->FilterResults->merge('Grupo' ,$this->User->Group->find('list'))
                )
            )
        )
    );
    
    // Paginate
    $this->paginate['order'] = 'User.name ASC';
    $this->paginate['limit'] = 10;
    $this->paginate['conditions'] = $this->FilterResults->make();
    
    $this->set('users', $this->paginate());
}
</pre>

Arquivo __/app/View/Users/index.ctp__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->input('filter2', array('class' => 'select-box'));
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Pronto! Use e abuse de quantos filtros desejar.

Para deixar bem claro, veja está [imagem](http://pedroelsner.com/wp-content/uploads/2011/09/filterResults_1.png). Nela temos a View de Produtos, onde é possível filtrar por: Cor, Dimensão, Gramatura, Mateiral e Nome.

#  Filtro Avançado

Em alguns casos queremos algo diferente diferente, por exemplo, desejamos que o usuário possa escolher o campo e o operador para realizar o filtro.

Arquivo __/app/Controller/UsersController.php__
<pre>
function index()
{
    $this->User->recursive = 0;
    
    // Filter Results
    $this->FilterResults->addFilters(array('filter1'));
    
    // Paginate
    $this->paginate['order'] = 'User.name ASC';
    $this->paginate['limit'] = 10;
    $this->paginate['conditions'] = $this->FilterResults->make();

    $this->set('users', $this->paginate());
}
</pre>

NOTA: Perceba que desta vez criamos o filtro ´filter1´ sem nenhum parâmetro. Isto porque as regras serão selecionadas na View.

Arquivo __/app/View/Users/index.ctp__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->selectFields('filter1', null, array('class' => 'select-box'));
$this->FilterForm->selectOperators('filter1');
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Agora, primeiro você pode selecionar o campo (automaticamente o Filter Results lista os campos da tabela), depois o operador e informar o valor desejado para o filtro.

Haverá situações em que você precisará personalizar os campos e os operadores para seleção. Por exemplo, vamos deixar somente os campos `User.id`, `User.name` e `User.username` para seleção, e os operadores `LIKE` e `=`.

Para isso, mudamos somente a View.

Arquivo __/app/View/Users/index.ctp__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->selectFields(
    'filter1',
    array(
        'User.id'       => __('ID', true),
        'User.name'     => __('Nome', true),
        'User.username' => __('Usuário', true),
    ),
    array(
        'class' => 'select-box'
    )
);
$this->FilterForm->selectOperators(
    'filter1',
    array(
        'LIKE' => __('Contendo', true),
        '='    => __('Igual a', true)
    )
);
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

## Operadores

O Filter Resultes possuí operadores pré-definidos, abaixo você encontra todas as opções disponíveis para você utilizar em seus filtros avançados.

<pre>
array(
    'LIKE'      => __('contendo', true),
    'NOT LIKE'  => __('não contendo', true),
    'LIKEbegin' => __('começando com', true),
    'LIKEend'   => __('terminando com', true),
    '='  => __('iqual a', true),
    '!=' => __('diferente de', true),
    '>'  => __('maior que', true),
    '>=' => __('maior ou igual', true),
    '<'  => __('menor que', true),
    '<=' => __('menor ou igual', true)
);
</pre>

# Operador BETWEEN

## Por: Vinicius Arantes

Agora também é possível utilizar o operador `BETWEEN` para consulta entre valores numéricos ou de data. Configure o campo de filtro desta forma:

<pre>
// Filter Results
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'User.id' => array(
                'operator'    => 'BETWEEN'
            )
        )
    )
);
</pre>

Para campos de data, adicione a opção `'convertDate' => true` para converte a data informada para o formato `YYYY-MM-DD`:

<pre>
// Filter Results
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'User.modified' => array(
                'operator'    => 'BETWEEN',
                'convertDate' => true
            )
        )
    )
);
</pre>

# Copyright e Licença

Copyright 2012, Pedro Elsner (http://pedroelsner.com/)

Licenciado pela Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/br/)
