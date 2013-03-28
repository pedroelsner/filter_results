# Filter Results

Plugin que gera `conditions` para métodos `find` no CakePHP 2.3+ a partir de um formulário de pesquisa.

## Compatibilidade

Compatível com CakePHP 2.3 + Paginate (Component)
* Versão para CakePHP 1.3: [http://github.com/pedroelsner/filter_results/tree/1.3](http://github.com/pedroelsner/filter_results/tree/1.3 "Filter para CakePHP 1.3")
* Versão para CakePHP 2.0: [http://github.com/pedroelsner/filter_results/tree/2.0](http://github.com/pedroelsner/filter_results/tree/2.0 "Filter para CakePHP 2.0")

# Mudanças da versão 2.0 para 2.3

* Componente `FilterResults` foi alterado para apenas `Filter`;
* Helper `FilterForm` foi alterado para apenas `Search`;
* Armazena em `Session` ultimo filtro utilizado e restaura na volta para a `Action`;
* Operadores `IS NULL` e `NOT IS NULL` foram implementados;
* Método `explode` disponível para qualquer combinação de filtros;
* Reformulação na estrutura das funções;

# Instalação

Faça o download do plugin e coloque seu conteúdo dentro de `/app/Plugin/FilterResults` ou em outro diretório para plugins do CakePHP.

## Ativação

Ative o plugin adicionando ao arquivo __/app/Config/bootstrap.php__:

<pre>
CakePlugin::load('FilterResults');
</pre>

## Configurações

Edite o arquivo __/app/AppController.php__:

<pre>
var $components = array(
    'FilterResults.Filter' => array(
        'auto' => array(
            'paginate' => false,
            'explode'  => true,  // recomendado
        ),
        'explode' => array(
            'character'   => ' ',
            'concatenate' => 'AND',
        )
    )
);

var $helpers = array(
    'FilterResults.Search'
);
</pre>

Parâmetros de configurações do componente:

*   __auto->paginate:__ Se você definir como TRUE, o Paginate será configurado automaticamente.
*   __auto->explode:__ Se você definir como TRUE, o valor de pesquisa será quebrado pelo `explode->character` e concatenado pela condição `explode->concatenate`.

# Utilização

Para os exemplos contidos neste documento, vou utilizar como base o seguinte base de dados

<pre>
CREATE TABLE groups (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    PRIMARY KEY(id),
    UNIQUE(name)
) ENGINE=INNODB;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    group_id INT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    username VARCHAR(20) NOT NULL,
    active TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY(id),
    FOREIGN KEY(group_id) REFERENCES groups(id),
    UNIQUE(username)
) ENGINE=INNODB;

INSERT INTO groups(name) VALUES('Admin');
INSERT INTO groups(name) VALUES('Guest');

INSERT INTO users(group_id, name, username) VALUES(1, 'Pedro Elsner', 'pelsner');
INSERT INTO users(group_id, name, username) VALUES(1, 'Petter Morato', 'pmorato');
INSERT INTO users(group_id, name, username, active) VALUES(2, 'Lucas Pedro Mariano Elsner', 'lpmelsner', 0);
INSERT INTO users(group_id, name, username, active) VALUES(2, 'Rebeca Moraes Silva', 'rebeca_moraes', 0);
INSERT INTO users(group_id, name, username, active) VALUES(2, 'Silvia Morato Moraes', 'silvia22', 0);
</pre>

# Filtro Simples

Bom, após gerar as telas pelo Bake, vamos colocar um filtro sobre a grid(tabela) para pesquisar um usuário pelo nome(`User.name`). Então vamos configurar o Controller e a View, desta forma:

Arquivo __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Adiciona filtro
    $this->Filter->addFilters(
        array(
            'filter1' => array(
                'User.name' => array(
                    'operator' => 'LIKE',
                    'value' => array(
                        'before' => '%', // opcional
                        'after'  => '%'  // opcional
                    )
                )
            )
        )
    );

    $this->Filter->setPaginate('order', 'User.name ASC'); // opcional
    $this->Filter->setPaginate('limit', 10);              // opcional

    // Define conditions
    $this->Filter->setPaginate('conditions', $this->Filter->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

A configuração aqui é bem simples: Criamos um filtro chamado `filter1` que utilizará o campo `User.name`. Este filtro utiliza o operador `LIKE` e adiciona `%` antes e depois do conteudo a ser filtrado.

Agora temos apenas que fazer o formulário na View em cima da tabela.

Arquivo __/app/View/Users/index.ctp)__
<pre>
echo $this->Search->create();
echo $this->Search->input('filter1');
echo $this->Search->end(__('Filtrar', true));
</pre>

Pronto! Temos um campo que filtra o usuário pelo nome e compatível com o Paginate.

E mais, o Filter Results automaticamente divide o valor de pesquisa para obter um melhor resultado. Por exemplo: se realizarmos um filtro por 'Pedro Elsner', a condição será: `WHERE ((User.name LIKE '%Pedro%') AND (User.name LIKE '%Elsner%'))`

## Configurações de Quebra (Explode)

A opção `explode` para os operadores `LIKE` e `NOT LIKE` estam ativadas por padrão nas configurações do Filter Results. Mas, como você sabe, você pode desativar na declaração dos components no controller. Se você fizer isto, você pode ativar a função `explode` para um filtro determinado:

<pre>
$this->Filter->addFilter(
    array(
        'filter1' => array(
            'User.name' => array(
                'operator' => 'LIKE',
                'explode'  => true
            )
        )
    )
);
</pre>

Também é possível mudar as opções de quebra para cada um.

<pre>
$this->Filter->addFilter(
    array(
        'filter1' => array(
            'User.name' => array(
                'operator' => 'LIKE',
                'explode' => array(
                    'character'   = '-',
                    'concatenate' = 'OR'
                )
            )
        )
    )
);
</pre>

Além disso, você também pode usar a função de quebra junto com outro opedaroes (como `=`). Veja:

<pre>
$this->Filter->addFilter(
    array(
        'filter1' => array(
            'User.name' => array(
                'operator' => '=',
                'explode'  => true
            )
        )
    )
);
</pre>


# Filtro Simples + Regra Composta

Vamos agora fazer mais uma regra dentro do filtro `filter1`. Queremos que ele filtre pelo nome(`User.name`) ou pelo nome do usuário(`User.username`).

Portanto teremos apenas um campo de filtro que obedece a duas condições, logo, alteramos apenas as configurações da action:

Arquivo __/app/Controller/UsersController.php__
<pre>
$this->Filter->addFilters(
    array(
        'filter1' => array(
            'OR' => array(
                'User.name'     => array('operator' => 'LIKE'),
                'User.username' => array('operator' => 'LIKE')
            )
        )
    )
);
</pre>

A regra `OR` pode ser também `AND` ou `NOT`.

__NOTA__: Se você definir mais de uma condição sem especificar a regra, o plugin entenderá como `AND` automaticamente.

# Filtro Simples + Regra Fixa

Vamos supor agora que o nosso filtro `filter1` quando informado deva filtrar pelo nome(`User.name`) __E__ somente usuários ativos.

Arquivo __/app/Controller/UsersController.php__
<pre>
$this->Filter->addFilters(
    array(
        'filter1' => array(
            'User.name'   => array('operator' => 'LIKE'),
            'User.active' => array('value'    => '1')
        )
    )
);
</pre>

# Agregação de Filtros

Automáticamente o Filter Results concatena todos os filtros pela regra `AND`. Usando o exemplo a seguir, informando 'Pedro' no `filter1` e 'elsner' no `filter2` teremos a condição: `WHERE (User.name LIKE '%Pedro%') AND (User.usernname LIKE '%elsner%')`

<pre>
$this->Filter->addFilters(
    array(
        'filter1' => array(
            'User.name' => array('operator' => 'LIKE')
        )
        'filter2' => array(
            'User.username' => array('operator' => 'LIKE')
        )
    )
);
</pre>

__NOTA__: Podemos concatenar os filtros também pelas regras `OR` ou `NOT`.

Vamos alterar nosso exemplo para concatenar os filtros pela regra `OR`, e, se o `filtro1` for informado queremos apenas usuários ativos. Desta vamos obter a condição:  `WHERE ((User.name LIKE '%Pedro%') AND (User.active = 1)) OR (User.usernname LIKE '%elsner%')`

<pre>
$this->Filter->addFilters(
    array(
        'OR' => array(
            'filter1' => array(
                'User.name'   => array('operator' => 'LIKE'),
                'User.active' => array('value'    => '1')
            )
            'filter2' => array(
                'User.username' => array('operator' => 'LIKE')
            )
        )
    )
);
</pre>

# Filtro de Seleção

Vamos mudar nosso filtro. Além de filtrar pelo nome, queremos agora filtrar também pelo grupo do usuário(`Group.name`) desejado através de um campo de seleção.

Arquivo __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Adiciona filtro
    $this->Filter->addFilters(
        array(
            'filter1' => array(
                'User.name' => array('operator' => 'LIKE')
            ),
            'filter2' => array(
                'User.group_id' => array(
                    'select' => $this->Filter->select('Grupo', $this->User->Group->find('list'))
                )
            )
        )
    );
    
    // Define conditions
    $this->Filter->setPaginate('conditions', $this->Filter->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

Arquivo __/app/View/Users/index.ctp__
<pre>
echo $this->Search->create();
echo $this->Search->input('filter2', array('class' => 'select-box'));
echo $this->Search->input('filter1');
echo $this->Search->end(__('Filtrar', true));
</pre>

Pronto! Use e abuse de quantos filtros desejar.

Para deixar bem claro, veja está [imagem](http://pedroelsner.com/wp-content/uploads/2011/09/Filter_1.png). Nela temos a View de Produtos, onde é possível filtrar por: Cor, Dimensão, Gramatura, Mateiral e Nome.

#  Filtro Avançado

Em alguns casos queremos algo diferente diferente, por exemplo, desejamos que o usuário possa escolher o campo e o operador para realizar o filtro.

Arquivo __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Adiciona filtro
    $this->Filter->addFilters('filter1');
    
    // Define conditions
    $this->Filter->setPaginate('conditions', $this->Filter->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

__NOTA__: Perceba que desta vez criamos o filtro ´filter1´ sem nenhum parâmetro. Isto porque as regras serão selecionadas na View.

Arquivo __/app/View/Users/index.ctp__
<pre>
echo $this->Search->create();
echo $this->Search->selectFields('filter1', null, array('class' => 'select-box'));
echo $this->Search->selectOperators('filter1');
echo $this->Search->input('filter1');
echo $this->Search->end(__('Filtrar', true));
</pre>

Agora, primeiro você pode selecionar o campo (automaticamente o Filter Results lista os campos da tabela), depois o operador e informar o valor desejado para o filtro.

Haverá situações em que você precisará personalizar os campos e os operadores para seleção. Por exemplo, vamos deixar somente os campos `User.id`, `User.name` e `User.username` para seleção, e os operadores `LIKE` e `=`.

Para isso, mudamos somente a View.

Arquivo __/app/View/Users/index.ctp__
<pre>
echo $this->Search->create();

echo $this->Search->selectFields('filter1',
        array(
            'User.id'       => __('ID', true),
            'User.name'     => __('Nome', true),
            'User.username' => __('Usuário', true),
        ),
        array(
            'class' => 'select-box'
        )
    );

echo $this->Search->selectOperators('filter1',
        array(
            'LIKE' => __('contendo', true),
            '='    => __('igual', true)
        )
    );

echo $this->Search->input('filter1');
echo $this->Search->end(__('Filtrar', true));
</pre>

# Operadores

O Filter Resultes possuí operadores pré-definidos, abaixo você encontra todas as opções disponíveis para você utilizar em seus filtros avançados.

<pre>
array(
    'LIKE'       => __('contendo', true),
    'NOT LIKE'   => __('não contendo', true),
    'LIKE BEGIN' => __('começando com', true),
    'LIKE END'   => __('terminando com', true),
    '='  => __('iqual a', true),
    '!=' => __('diferente de', true),
    '>'  => __('maior que', true),
    '>=' => __('maior ou igual', true),
    '&lt;'  => __('menor que', true),
    '&lt;=' => __('menor ou igual', true)
);
</pre>

## Between

Também é possível utilizar o operador `BETWEEN` para consulta entre valores numéricos ou de data. Configure o campo de filtro desta forma:

<pre>
$this->Filter->addFilters(
    array(
        'filter1' => array(
            'User.id' => array(
                'operator' => 'BETWEEN'
                'between' => array(
                    'text' => __(' e ', true)
                )
            )
        )
    )
);
</pre>

Para campos de data, adicione a opção `'date' => true` para converte a data informada para o formato `YYYY-MM-DD`:

<pre>
$this->Filter->addFilters(
    array(
        'filter1' => array(
            'User.modified' => array(
                'operator' => 'BETWEEN',
                'between' => array(
                    'text' => __(' e ', true),
                    'date' => true
                )
            )
        )
    )
);
</pre>

# Relacionamentos HABTM

Por padrão, nos relacionamentos HABTM o CakePHP realiza várias consultas a parte e depois faz um `merge` nos resultados em um unico `array`. Você já deve ter percebido isso, mas se não, verifique a janela de debug enquanto você faz `find()` em uma relação HABTM.

Para filtramos nessas relações, precisamos criar alguns "hacks" para que o CakePHP gere um único `select`, assim o plugin filtra o resultado com comandos `where` simples.

Todas as informações e explicações necessárias para criar os "hacks" você encontra neste tutorial: [http://pedroelsner.com/2012/09/pesquisando-em-associacoes-habtm-no-cakephp/](http://pedroelsner.com/2012/09/pesquisando-em-associacoes-habtm-no-cakephp/ "Pesquisando em relações HABTM no CakePHP")

# Copyright e Licença

Copyright 2012, Pedro Elsner (http://pedroelsner.com/)

Licenciado pela Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/br/)

## Agradecimentos

* Vinícius Arantes (vinicius.big@gmail.com)
* Francys Reymer (francys.reymer@gmail.com)
* Geazi El-Hanã de Oliveira (geazi.oliveira@jacotei.com.br)
