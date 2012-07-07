# Filter Results

Generates `conditions` to `find` methods in CakePHP 2.x from a search form.

## Compatibility

Compatible with CakePHP 2.x + Paginate (Component)
* Version for CakePHP 1.3: [http://github.com/pedroelsner/filter_results/tree/1.3](http://github.com/pedroelsner/filter_results/tree/1.3 "FilterResults para CakePHP 1.3")

# Installation

Download the plugin and place its contents inside `/app/Plugin/filter_results` or other directory plugins for CakePHP.

## Activation

Activate the plugin by adding the file __/app/Config/bootstrap.php__:

<pre>
CakePlugin::load('FilterResults');
</pre>

## Configuration

Edit the file __/app/AppController.php__:

<pre>
var $components = array(
    'FilterResults.FilterResults' => array(
        'auto' => array(
            'paginate' => false,
            'explode'  => true,  // recommended
        ),
        'explode' => array(
            'character'   => ' ',
            'concatenate' => 'AND',
        )
    )
);

var $helpers = array(
    'FilterResults.FilterForm' => array(
        'operators' => array(
            'LIKE'       => 'containing',
            'NOT LIKE'   => 'not containing',
            'LIKE BEGIN' => 'starting with',
            'LIKE END'   => 'ending with',
            '='  => 'equal to',
            '!=' => 'different',
            '>'  => 'greater than',
            '>=' => 'greater or equal to',
            '<'  => 'less than',
            '<=' => 'less or equal to'
        )
    )
);
</pre>

Settings parameters:

*   __auto->paginate:__ If you set TRUE, the Paginate will be configured automatically.
*   __auto->explode:__ If you set TRUE, the filter value  will be explode by the `explode->character` and concatenate by the `explode->concatenate`.

# Using the Component

For the examples contained herein, will build on the following database

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

# Simple Filter

Well, after generating the screens at Bake, we put a filter on the grid(table) to search a user by name(`User.name `). So let's set the Controller and View, as follows:

File __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Add filter
    $this->FilterResults->addFilters(
        array(
            'filter1' => array(
                'User.name' => array(
                    'operator' => 'LIKE',
                    'value' => array(
                        'before' => '%', // optional
                        'after'  => '%'  // optional
                    )
                )
            )
        )
    );

    $this->FilterResults->setPaginate('order', 'User.name ASC'); // optional
    $this->FilterResults->setPaginate('limit', 10);              // optional

    // Define conditions
    $this->FilterResults->setPaginate('conditions', $this->FilterResults->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

The setting here is quite simple: We create a filter called `filter1` that will use the field `User.name`. This filter uses the `LIKE` operator add `%` before and after the content to be filtered.

Now we just have to make the form on View at the top of the table.

File __/app/View/Users/index.ctp)__
<pre>
echo $this->FilterForm->create();
echo $this->FilterForm->input('filter1');
echo $this->FilterForm->end(__('Filter', true));
</pre>

Ready! We have a field that filters the user by name and compatible with the Paginate.

And more, the Filter Results automaticaly explode the filter value to gain a better results. For example: if we filter by 'Pedro Elsner', the condition will be: `WHERE ((User.name LIKE '%Pedro%') AND (User.name LIKE '%Elsner%'))`

## Explode Settings

The option `explode` for operators `LIKE` and `NOT LIKE` is always enabled in the settings of the Filter Results. But, how do you know, you can disable it into components declaration in controller. If you do, you can enable the `explode` function for only the specified filter:

<pre>
$this->FilterResults->addFilter(
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

Too is possible to change the explode parameters for each filter.

<pre>
$this->FilterResults->addFilter(
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

Also, you can to use the explode function with any operator (like `=`). See:

<pre>
$this->FilterResults->addFilter(
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


# Simple Filter + Composite Rule

Let us now make another rule within the filter `filter1`. We want to filter it by name (`User.name`) or by username(`User.username`).

Then just changed our Controller:

File __/app/Controller/UsersController.php__
<pre>
$this->FilterResults->addFilters(
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

The rule `OR` can also be `AND` or `NOT`.

__NOTE__: If you define more than one condition without the specific rule, the plugin will understand  automatically how `AND`.

# Simple Filter + Fixed Rules

Suppose now that our filter `filter1` when informed should filter by name (`User.name`) __AND__ only active users.

File __/app/Controller/UsersController.php__
<pre>
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'User.name'   => array('operator' => 'LIKE'),
            'User.active' => array('value'    => '1')
        )
    )
);
</pre>

# Filters Aggregation

The Filter Results automatically concatenates all filter by the rule `AND`. See in the follow example, that if informe 'Pedro' in `filter1` and 'elsner' in `filter2` we going to get the condition: `WHERE (User.name LIKE '%Pedro%') AND (User.usernname LIKE '%elsner%')`

<pre>
$this->FilterResults->addFilters(
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

__NOTE__: We too can concatenate the filters by rules `OR` and `NOT`.

Now, we going to change the example for concatenate the `filter1` and `filter2` by rule `OR`, and, if `filter1` is not empty, only the active users. So, we going to get this condition: `WHERE ((User.name LIKE '%Pedro%') AND (User.active = 1)) OR (User.usernname LIKE '%elsner%')`

<pre>
$this->FilterResults->addFilters(
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

# Selection Filter

Let's change our filter. In addition to filtering by name, we now also filter by user group(`Group.name`) through a desired selection field.

File __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Add filter
    $this->FilterResults->addFilters(
        array(
            'filter1' => array(
                'User.name' => array('operator' => 'LIKE')
            ),
            'filter2' => array(
                'User.group_id' => array(
                    'select' => $this->FilterResults->select('Grupo', $this->User->Group->find('list'))
                )
            )
        )
    );
    
    // Define conditions
    $this->FilterResults->setPaginate('conditions', $this->FilterResults->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

File __/app/View/Users/index.ctp__
<pre>
echo $this->FilterForm->create();
echo $this->FilterForm->input('filter2', array('class' => 'select-box'));
echo $this->FilterForm->input('filter1');
echo $this->FilterForm->end(__('Filter', true));
</pre>

Ready! Use and abuse of those filters you want.

To be clear, see this [image](http://pedroelsner.com/wp-content/uploads/2011/09/filterResults_1.png). Here we have the Product View, where you can filter by: Color, Size, Weight, Mateiral and Product Name.

#  Advanced Filter

In some cases we want something different than, for example, we want the user to choose the field and the operator to perform the filter.

File __/app/Controller/UsersController.php__
<pre>
function index() {
    
    // Add filter
    $this->FilterResults->addFilters('filter1');
    
    // Define conditions
    $this->FilterResults->setPaginate('conditions', $this->FilterResults->getConditions());

    $this->User->recursive = 0;
    $this->set('users', $this->paginate());
}
</pre>

__NOTE__: Note that this time we filter `filter1` without any parameters. This is because the rules are selected in the View.

File __/app/View/Users/index.ctp__
<pre>
echo $this->FilterForm->create();
echo $this->FilterForm->selectFields('filter1', null, array('class' => 'select-box'));
echo $this->FilterForm->selectOperators('filter1');
echo $this->FilterForm->input('filter1');
echo $this->FilterForm->end(__('Filter', true));
</pre>

Now, you can first select the field (automatically the Filter Results table lists the fields), then inform the operator and the desired value for the filter.

There will be situations where you need to customize the fields and operators for selection. For example, let's just leave the fields `User.id`, `User.name` and `User.username` for selection, and the operators `LIKE` and `=`.

For this, we change only the View.

Arquivo __/app/View/Users/index.ctp__
<pre>
echo $this->FilterForm->create();

echo $this->FilterForm->selectFields('filter1',
        array(
            'User.id'       => __('ID', true),
            'User.name'     => __('Name', true),
            'User.username' => __('Username', true),
        ),
        array(
            'class' => 'select-box'
        )
    );

echo $this->FilterForm->selectOperators('filter1',
        array(
            'LIKE' => __('containing', true),
            '='    => __('equal to', true)
        )
    );

echo $this->FilterForm->input('filter1');
echo $this->FilterForm->end(__('Filter', true));
</pre>

# Operators

The Filter results have pre-defined operators, below you will find all the options available for you to use in their advanced filters.

<pre>
array(
    'LIKE'       => __('containing', true),
    'NOT LIKE'   => __('not containing', true),
    'LIKE BEGIN' => __('starting with', true),
    'LIKE END'   => __('ending with', true),
    '='  => __('equal to', true),
    '!=' => __('different', true),
    '>'  => __('greater than', true),
    '>=' => __('greater or equal to', true),
    '<'  => __('less than', true),
    '<=' => __('less or equal to', true)
);
</pre>

## Between

For to use the operator `BETWEEN` in FilterResults:

<pre>
$this->FilterResults->addFilters(
    array(
        'filter1' => array(
            'User.id' => array(
                'operator'    => 'BETWEEN',
                'between' => array(
                    'text' => __(' and ', true)
                )
            )
        )
    )
);
</pre>


# Copyright e License

Copyright 2012, Pedro Elsner (http://pedroelsner.com/)

Licensed under Creative Commons 3.0 (http://creativecommons.org/licenses/by/3.0/)

## Thanks

* Vinícius Arantes (vinicius.big@gmail.com)
* Francys Reymer (francys.reymer@gmail.com)
