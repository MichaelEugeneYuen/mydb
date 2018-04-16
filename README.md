<h3>How to use</h3>
<p>Edit mydb.php and update database credentials</p>

```html
	const DBHOST = 'localhost';
	const DBNAME = 'test';
	const DBUSER = 'root';
	const DBPASS = '';
```	

<p>Test your first query result</p>

```html
<?php
	include 'mydb.php'
	$db = new DB();
	$results = $db->table('table_name')->select('*')->results();
	print_r($results);
?>
```
<h3>select</h3>
<p>returns rows object on success / false on failure or no result</p>

```html
$db->table('table_name')->select('*')->results();

$db->table('table_name')->select(
  'column1,column2'
)->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE,'%value 6%'],
  )
)->results();
```

<h3>update</h3>
<p>returns number of affected rows on success</p>

```html
$db->table('table_name')->update(
	array(
		'column1' => 'new value 1',
		'column2' => 'new value 2',
	)
)->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE,'%value 6%'],
	)
)->rows();
```

<h3>insert</h3>
<p>returns last insert id on success / false on failure</p>

```html
$db->table('table_name')->insert(
	array(
		'column1' => 'value 1',
		'column2' => 'value 2',
	)
)->insertId();
```

<h3>select</h3>
<p>returns rows object on success / false on failure or no result</p>

```html
$db->table('table_name')->select('*')->results();

$db->table('table_name')->select(
	'column1,column2'
)->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE,'%value 6%'],
	)
)->results();
```

<h3>update</h3>
<p>returns number of affected rows on success</p>

```html
$db->table('table_name')->update(
	array(
		'column1' => 'new value 1',
		'column2' => 'new value 2',
	)
)->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE,'%value 6%'],
	)
)->rows();
```

<h3>insert</h3>
<p>returns last insert id on success / false on failure</p>

```html
$db->table('table_name')->insert(
	array(
		'column1' => 'value 1',
		'column2' => 'value 2',
		'column3' => 'value 3',
	)
)->insertId();
```

<h3>delete</h3>
<p>returns number of affected rows on success</p>

```html
$db->table('table_name')->delete()->wheres(
	array(
		['column1','=','value 1'],
		['column2','=','value 2'],
	)
)->rows();
```

<h3>Other where conditions</h3>
<h3>where in</h3>

```html
$db->table('table_name')->select('column1')->whereIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column1')->whereNotIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column2')->whereBetweens(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column2')->whereOrs(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE','%value 6%'],
	)
)->results();
```

<h3>combine where conditions</h3>

```html
$db->table('table_name')->select('column1,column2')->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE','%value 6%'],
	)
)->whereIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->whereBetweens(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->options(
	'ORDER by column1 DESC, column2 ASC, LIMIT 10'
)->results();
```

<h3>statement inspection</h3>
<p>Method check()</p>

```html
	$db->table(
		'table_name'
	)->select('column1,column2')->whereCols(
		array(
			['column1','LIKE','%value1%'],
		)
	)->whereIns(
		array(
			['id','1,2,3'],
		)
	)->whereBetweens(
		array(
			['column2','1,5000'],
		)
	)->whereOrs(
		array(
			['column2','=','750'],
		)
	)->options(
		'ORDER by column1 DESC, column2 ASC, LIMIT 10'
	)->check();
```
<p>This will return mysql statement, values and statment errors in array</p>

```html
Array
(
	[0] => SELECT column1,column2 FROM table_name WHERE (column1 LIKE ? AND (id IN (?,?,?)) AND (column2 BETWEEN  ? AND ?)) OR (column2 = ?)
	[1] => Array
		(
			[0] => %value1%
			[1] => 1
			[2] => 2
			[3] => 3
			[4] => 1
			[5] => 5000
			[6] => 750
		)

	[2] => Array
		(
		)
)

		'column3' => 'value 3',
	)
)->insertId();
```

<h3>delete</h3>
<p>returns number of affected rows on success</p>

```html
$db->table('table_name')->delete()->wheres(
	array(
		['column1','=','value 1'],
		['column2','=','value 2'],
	)
)->rows();
```

<h3>Other where conditions</h3>
<h3>where in</h3>

```html
$db->table('table_name')->select('column1')->whereIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column1')->whereNotIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column2')->whereBetweens(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->results();

$db->table('table_name')->select('column2')->whereOrs(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE','%value 6%'],
	)
)->results();
```

<h3>combine where conditions</h3>

```html
$db->table('table_name')->select('column1,column2')->whereCols(
	array(
		['column1','=','value 1'],
		['column2','>','value 2'],
		['column3','<','value 3'],
		['column4','>=','value 4'],
		['column5','<=','value 5'],
		['column6','LIKE','%value 6%'],
	)
)->whereIns(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->whereBetweens(
	array(
		['column1','value 1,value 2'],
		['column2','value 3,value 4'],
	)
)->options(
	'ORDER by column1 DESC, column2 ASC, LIMIT 10'
)->results();
```

<h3>statement inspection</h3>
<p>Method check()</p>

```html
$db->table(
	'table_name'
)->select('column1,column2')->whereCols(
	array(
		['column1','LIKE','%value1%'],
	)
)->whereIns(
	array(
		['id','1,2,3'],
	)
)->whereBetweens(
	array(
		['column2','1,5000'],
	)
)->whereOrs(
	array(
		['column2','=','750'],
	)
)->options(
	'ORDER by column1 DESC, column2 ASC, LIMIT 10'
)->check();
```

<p>This will return mysql statement, values and statment errors in array</p>

```html
Array
(
	[0] => SELECT column1,column2 FROM table_name WHERE (column1 LIKE ? AND (id IN (?,?,?)) AND (column2 BETWEEN  ? AND ?)) OR (column2 = ?)
	[1] => Array
		(
			[0] => %value1%
			[1] => 1
			[2] => 2
			[3] => 3
			[4] => 1
			[5] => 5000
			[6] => 750
		)

	[2] => Array
		(
		)
)
```
