# Filter Results

Generates `conditions` to `find` methods in CakePHP from a search form.

## Compatibility

Compatible with CakePHP v 1.3 + Paginate (Component)

# Installation

Download the plugin and place its contents inside `/app/plugins/filter_results` or other directory plugins for CakePHP.

## Configuration

Edit the file __/app/app_controller.php__:

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

Settings parameters:

*   __autoPaginate:__ If you set TRUE, the Paginate will be configured automatically.

# Using the Component

For the examples contained herein, will build on the following database

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

# Simple Filter

Well, after generating the screens at Bake or [Bake utf8] (http://www.github.com/pedroelsner/bake_utf8), we put a filter on the grid(table) to search a user by name(`User.name `). So let's set the Controller and View, as follows:

File __/app/controller/users_controller.php__
<pre>
function admin_index()
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

The setting here is quite simple: We create a filter called `filter1` that will use the field `User.name`. This filter uses the `LIKE` operator add `%` before and after the content to be filtered.

Now we just have to make the form on View at the top of the table.

File __/app/view/users/admin_index.ctp)__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Ready! We have a field that filters the user by name and compatible with the Paginate.

# Simple Filter + Composite Rule

Let us now make another rule within the filter `filter1`. We want to filter it by name (`User.name`) or by username(`User.username`).

Then just changed our Controller:

File __/app/controller/users_controller.php__
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

The rule `OR` can also be `AND` or `NOT`.

NOTE: If you define more than one condition without the specific rule, the plugin will understand  automatically how `AND`.

# Simple Filter + Fixed Rules

Suppose now that our filter `filter1` when informed should filter by name (`User.name`) And only active users.

File __/app/controller/users_controller.php__
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

# Selection Filter

Let's change our filter. In addition to filtering by name, we now also filter by user group(`Group.name`) through a desired selection field.

File __/app/controller/users_controller.php__
<pre>
function admin_index()
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

File __/app/view/users/admin_index.ctp__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->input('filter2', array('class' => 'select-box'));
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Ready! Use and abuse of those filters you want.

To be clear, see this [image](http://pedroelsner.com/wp-content/uploads/2011/09/filterResults_1.png). Here we have the Product View, where you can filter by: Color, Size, Weight, Mateiral and Product Name.

#  Advanced Filter

In some cases we want something different than, for example, we want the user to choose the field and the operator to perform the filter.

File __/app/controller/users_controller.php__
<pre>
function admin_index()
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

NOTE: Note that this time we filter `filter1` without any parameters. This is because the rules are selected in the View.

File __/app/view/users/admin_index.ctp__
<pre>
$this->FilterForm->create($FilterResults);
$this->FilterForm->selectFields('filter1', null, array('class' => 'select-box'));
$this->FilterForm->selectOperators('filter1');
$this->FilterForm->input('filter1');
$this->FilterForm->submit(__('Filtrar', true));
$this->FilterForm->end();
</pre>

Now, you can first select the field (automatically the Filter Results table lists the fields), then inform the operator and the desired value for the filter.

There will be situations where you need to customize the fields and operators for selection. For example, let's just leave the fields `User.id`, `User.name` and `User.username` for selection, and the operators `LIKE` and `=`.

For this, we change only the View.

Arquivo __/app/view/users/admin_index.ctp__
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

## Operators

The Filter results have pre-defined operators, below you will find all the options available for you to use in their advanced filters.

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

# Operator BETWEEN

## By: Vinicius Arantes

Now is possible too use the operator `BETWEEN` in FilterResults:

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

# Copyright e License

Copyright 2011, Pedro Elsner (http://pedroelsner.com/)

Licensed under Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/)