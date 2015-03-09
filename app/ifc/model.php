<?php

/**
 * Class Model
 *
 * Interface de manipulação de banco de dados
 *
 * The connections will be stored in $connections parameter
 *
 * The encrypted connection files are stored in
 * app/ifc/data and the file names are hashed
 *
 * if you need a runtime connection, use createNewConnection()
 *
 * @author  Elvis D'Andrea
 * @email   <elvis.vista@gmail.com>
 */

define('MODELDIR', IFCDIR . '/data');

class Model {

    /**
     * The Model Instance ID
     *
     * @var string
     */
    private $id;

    /**
     * The structure of a SELECT query
     *
     * @var array
     */
    private $structure = array('select', 'from', 'where', 'group', 'order', 'limit');

    /**
     * Should we keep the query after running it?
     *
     * @var bool
     */
    private $keepquery = false;

    /**
     * The SELECT clause
     *
     * @var array
     */
    private $select = array();

    /**
     * The FROM clause
     *
     * @var array
     */
    private $from = array();

    /**
     * The WHERE clause
     *
     * @var array
     */
    private $where = array();

    /**
     * The GROUP clause
     *
     * @var array
     */
    private $group = array();

    /**
     * The ORDER clause
     *
     * @var array
     */
    private $order = array();

    /**
     * The LIMIT clause
     *
     * @var array
     */
    private $limit = array();

    /**
     * The SET clause for INSERT
     *
     * @var array
     */
    private $insertset = array();

    /**
     * The INTO table
     *
     * @var array
     */
    private $inserttable = array();

    /**
     * Subqueries and stuff
     *
     * @var array
     */
    private $insertspecials = array();

    /**
     * The SET clause for UPDATE
     *
     * @var array
     */
    private $updateset = array();

    /**
     * The UPDATE table
     *
     * @var string
     */
    private $updatetable = '';

    /**
     * The WHERE clause for UPDATE
     *
     * @var array
     */
    private $updatewhere = array();

    /**
     * Subqueries and stuff for UPDATE
     *
     * @var array
     */
    private $updatespecials = array();

    /**
     * The FROM clause for DELETE
     *
     * @var array
     */
    private $deletefrom = array();

    /**
     * The WHERE clause for DELETE
     *
     * @var array
     */
    private $deletewhere = array();

    /**
     * True | False if the query ran Ok
     *
     * @var bool
     */
    private $result;

    /**
     * Should we use UNION statement
     *
     * @var bool
     */
    private $union = false;

    /**
     * The result of a SELECT query itself
     *
     * Will be an array with the rows with row number on key
     * each row is an array with columns in keys and data in values
     *
     * @var array
     */
    public $dataset = array();

    /**
     * The last insert auto-increment ID
     *
     * @var int
     */
    private $lastId = 0;

    /**
     * The error code of the last query
     *
     * It's 0 when no error occurred
     *
     * @var
     */
    private $errorInfo = array(
        'code'      => 0,
        'message'   => ''
    );

    /**
     * The Result of a
     * query can be displayed in a grid
     *
     * Therefore, we need a template file for it
     *
     * @var string
     */
    private $dbGridTemplate = 'ifc/dbGrid';

    /**
     * We always need a custom grid, where
     * the titles are human readable and
     * we don't want to show all columns
     *
     * So, we can create a list of the columns
     * we're using in the grid and make a title
     * for them
     *
     * @var array
     */
    private $dbGridColumns = array();

    /**
     * Sometimes we don't need the
     * table titles, so we may want
     * to deactivate it
     *
     * @var bool
     */
    private $dbGridShowHeader = true;


    /**
     * Sometimes we want to add all
     * columns in the Grid, so we don't
     * want to add column by column
     *
     * @var bool
     */
    private $dbGridAutoHeader = false;

    /**
     * When rendering a table, we may need
     * to set an onclick event for the rows
     *
     * @var array
     */
    private $gridRowLink = array('action' => '', 'fieldId' => '');

    /**
     * Sometimes we already have an
     * entire style for tables but they
     * depend to set a specific class
     *
     * @var string
     */
    private $gridClass = '';

    /**
     * The Current Connection Resource Name
     *
     * @var string
     */
    private $connection = '';

    /**
     * The list of Connection Resources
     * indexed by their names and with all
     * connection data separated as index
     * inside an array
     *
     * @var array
     */
    private $connections = array();


    public function __construct($connection = DEFAULT_CONNECTION) {

        $this->id = uniqid();
        $this->setConnection($connection);
    }

    /**
     * Sets current connection
     *
     * @param   string      $name       - The connection name
     */
    public function setConnection($name) {
        isset($this->connections[$name]) || $this->loadConnectionFile($name);
        $this->connection = $name;
    }

    /**
     * Sets the template file to render
     * a grid with the dataset content
     *
     * @param   string      $name       - The template name
     */
    public function setDbGridTemplate($name) {
        $this->dbGridTemplate = $name;
    }

    /**
     * Adds a column in dbGrid columns list
     *
     * @param   string      $title      - The column title
     * @param   string      $field      - The column field name
     * @param   string      $type       - Text|Date|Input|Checkbox|Select|Image
     * @param   bool|string $subtitle   - A subtitle field: a text to be show under the line content
     */
    public function addGridColumn($title, $field, $type = 'Text', $subtitle = false) {
        $this->dbGridColumns[$field] = array(
            'field'     => $field,
            'title'     => $title,
            'type'      => $type,
            'subtitle'  => $subtitle
        );
    }

    /**
     * Clears previous column set
     */
    public function clearGridColumns() {

        $this->dbGridColumns = array();
    }

    /**
     * Sets the table class in dbGrid
     *
     * @param   string      $class      - The class name
     */
    public function setGridClass($class) {
        $this->gridClass = $class;
    }

    /**
     * Whenever we need to set an entire set
     * of columns at once, here's a method
     *
     * @param   array       $columnList     - An array containing all columns with title => field structure
     */
    public function addGridColumns(array $columnList) {
        $this->dbGridColumns = array_merge($this->dbGridColumns, $columnList);
    }

    /**
     * Deactivates the Table headers
     * when rendering a DbGrid
     *
     * @param   bool        $show           - True|False
     */
    public function showDbGridTitles($show) {
        $this->dbGridShowHeader = $show;
    }

    /**
     * Sets the Auto Header
     *
     * Set to True if you need to show
     * all columns in the grid
     *
     * @param   bool        $header         - True|False
     */
    public function setDbGridAutoHeader($header) {
        $this->dbGridAutoHeader = $header;
    }

    /**
     * Removes a dbGrid Column
     *
     * @param   string      $title      - The column title to be removed
     */
    public function removeGridColumn($title) {
        if (isset($this->dbGridColumns[$title])) unset($this->dbGridColumns[$title]);
    }

    /**
     * Adds a destination link for
     * rows in a DBGrid Table
     *
     * The link will be set as described below:
     *
     * Value set as action:     destination
     * Value set as fieldId:    id
     * Result URL:              http://siteurl/destination/{row['fieldId']}
     *
     * @param   string      $action
     * @param   string      $fieldId
     */
    public function setGridRowLink($action, $fieldId) {
        $this->gridRowLink['action'] = $action;
        $this->gridRowLink['fieldId'] = $fieldId;
    }

    /**
     * Returns the last insert auto_increment Id
     *
     * @return int
     */
    public function getLastInsertId() {
        return $this->lastId;
    }

    /**
     * Sets the Model ID
     *
     * @param $id
     */
    protected function setId($id) {
        $this->id = $id;
    }

    /**
     * Returns the Model ID
     *
     * @return string
     */
    protected function getId() {
        return $this->id;
    }

    /**
     * Sets the Keep Query flag
     *
     * @param $keep
     */
    protected function keepQuery($keep) {
        $this->keepquery = $keep;
    }

    /**
     * Deve ser informado um array com
     * a estrutura completa da select
     *
     * ex: array(
     *  'select' => array('field'),
     *  'from' => array('tabela')
     * );
     * @param $query
     */
    protected function setQuery($query) {
        $this->query = $query;
    }

    /**
     * Loads a connection from an encrypted
     * file into the Connection Resource List
     *
     * The encrypted files should be in the
     * data directory defined in app
     *
     * @param $name     - Connection Name
     */
    public function LoadConnectionFile($name) {

        $dataFile = MODELDIR . '/' . md5($name);
        if (is_file($dataFile)) {
            $data = file_get_contents($dataFile);
            $this->createNewConnection($name, $data);
        }
    }

    /**
     * * Whenever you need to create a connection file,
     * Just insert data and call this function
     * in any method you want
     *
     * @param   string      $host       - The Host Name
     * @param   string      $user       - The Database User Name
     * @param   string      $password   - The User Password
     * @param   string      $database   - The Database Name
     */
    public function generateConnectionFile($name, $host, $user, $password, $database) {

        $data = array(
            'host' => $host,
            'user' => $user,
            'pass' => $password,
            'db'   => $database
        );

        $data = json_encode($data);
        $data = CR::encrypt($data);

        file_put_contents(MODELDIR . '/' . md5($name), $data);
    }

    /**
     * Creates a new connection from
     * encrypted string in the
     * Connection Resource List
     *
     * @param $name     - Nome da Conexão
     * @param $data     - String criptografada com os dados
     */
    public function createNewConnection($name, $data) {
        $data = CR::decrypt($data);
        $json = json_decode($data, true);

        if ($json) {
            $this->connections[$name] = $json;
        }
    }

    /**
     * Creates the connection resource instance
     *
     * @param $name     - The Connection Name
     */
    private function connect($name) {

        $this->connections[$name]['conn'] =
            new PDO(
                'mysql:host=' . $this->connections[$name]['host'] . ';dbname=' . $this->connections[$name]['db'] . ';charset=utf8',
                $this->connections[$name]['user'],
                $this->connections[$name]['pass']);
    }

    /**
     * Mounts the data resultset in UTF-8
     *
     * @param $result       - The PDO Query Result
     * @return array
     */
    private function Mount(PDOStatement $result) {
        $obj = array();
        $row = 0;
        while ($res = $result->fetchObject()) {
            foreach ($res as $field => $value) {
                $obj[$row][$field] = utf8_encode($value);
            }
            $row++;
        }
        return $obj;
    }

    /**
     * Executes a Query
     *
     * @param   string  $query          - Query string
     * @return  array
     * @throws  Exception               - Only when on Dev Environment
     */
    private function Exec($query) {

        $this->connect($this->connection);
        $result = $this->connections[$this->connection]['conn']->prepare($query);

        $result->execute();
        $info = $result->errorInfo();
        $this->errorInfo = array(
            'code'      => intval($info[0]),
            'message'   => $info[2]
        );

        $this->lastId = $this->connections[$this->connection]['conn']->lastInsertId();

        $this->result = $this->errorInfo['code'] == 0;
        $dataset = $result->fetchAll();
        return $dataset;
    }

    /**
     * The query execution caller
     * for SELECT queries
     *
     */
    protected function runQuery() {
        $this->dataset = $this->Exec($this->GetQuery());
        $this->clearSelect();
    }

    /**
     * The query execution caller
     * for DELETE queries
     *
     * @param   bool    $safe       - Safe Delete: will not run if there's no WHERE clause
     */
    protected function runDelete($safe = true) {
        $this->dataset = $this->Exec($this->getDeleteQuery($safe));
        $this->clearDelete();
    }

    /**
     * The query execution caller
     * for INSERT queries
     *
     * @param   bool    $safe       - Safe Insert: will not run if there's no WHERE clause
     */
    protected function runInsert() {
        $this->dataset = $this->Exec($this->GetInsert());
        $this->clearInsert();
    }

    /**
     * The query execution caller
     * for UPDATE queries
     *
     * @param   bool    $safe       - Safe Update: will not run if there's no WHERE clause
     */
    protected function runUpdate($safe = true) {
        $this->dataset = $this->Exec($this->getUpdateQuery($safe), true);
        $this->clearUpdate();
    }

    /**
     * Adds a field in the SELECT
     * statement
     *
     * @param   string      $field      - The field name
     */
    protected function addField($field) {
        $this->select[] = $field;
    }

    /**
     * Adds a statement in the FROM
     * statement
     *
     * @param   string      $from       - The FROM statement
     */
    protected function addFrom($from) {
        $this->from[] = $from;
    }

    /**
     * Adds a condition in the WHERE
     * clause
     *
     * @param   string      $where      - The WHERE condition
     * @param   string      $operator   - AND | OR
     */
    protected function addWhere($where, $operator = 'AND') {
        $this->where[] = (count($this->where) > 0 ? $operator : '') . ' ' . $where;
    }

    /**
     * Adds a field in the GROUP
     * clause
     *
     * @param   string      $group      - The field name
     */
    protected function addGroup($group) {
        $this->group[] = $group;
    }

    /**
     * Adds a field in the ORDER
     * clause
     *
     * @param   string      $order      - The field name and direction "ASC|DESC"
     */
    protected function addOrder($order) {
        $this->order[] = $order;
    }

    /**
     * Adds a limit condition
     *
     * @param   string      $limit      - The limit condition
     */
    protected function addLimit($limit) {
        $this->limit[] = $limit;
    }

    /**
     * Sets the table to delete from
     *
     * @param   string      $from       - The table name
     */
    protected function setDeleteFrom($from) {
        $this->deletefrom = $from;
    }

    /**
     * Adds a condition in the WHERE
     * clause for DELETE queries
     *
     * @param   string      $where      - The WHERE condition
     * @param   string      $operator   - AND | OR
     */
    protected function addDeleteWhere($where, $operator = 'AND') {
        $this->deletewhere[] = $operator . ' ' . $where;
    }

    /**
     * Unites 2 queries with UNION clause
     *
     * @param   string      $query      - The query object to unite
     */
    protected function unite($query) {
        $this->union = ' UNION ' . $query->getQuery();
    }

    /**
     * Returns a SELECT statement
     *
     * @return      string      - The SELECT statement
     */
    protected function getQuery() {

        $query = 'SELECT ' . implode(',',  $this->select)
            . ' FROM ' . implode(' ', $this->from)
            . ' WHERE (1) ' . (count($this->where) > 0 ? ' AND ' . implode(' ', $this->where) : '')
            . (count($this->group) > 0 ? ' GROUP BY ' . implode(',', $this->group) : '')
            . (count($this->order) > 0 ? ' ORDER BY ' . implode(',', $this->order) : '')
            . (count($this->limit) > 0 ? ' LIMIT ' . implode(',', $this->limit) : '');

        if ($this->union) {
            $query .= $this->union;
        }

        if ($this->keepquery && isset($this->id) && $this->id != '') {
            $this->saveQuery($this->id);
        }

        return $query;

    }

    /**
     * Returns a DELETE statement
     *
     * @param   bool    $safe       - Safe Delete: will not run if there's no where clause
     * @return  string              - The statement
     */
    protected function getDeleteQuery($safe = true) {
        if ($safe && count($this->deletewhere) < 1) return '';
        $query = 'DELETE FROM ' . $this->deletefrom .
            ' WHERE (1) ' . implode(' ', $this->deletewhere);

        return $query;
    }

    /**
     * Saves a query in persistence
     *
     * @param   string      $id     - The query ID
     */
    private function saveQuery($id) {
        $query = array();
        foreach ($this->structure as $clause) {
            $query[$clause] = $this->$clause;
        }

        Html::WriteSession($id, $query);
    }

    /**
     * Loads a query from persistence
     *
     */
    protected function loadQuery() {
        if (isset($this->id) && $this->id != '') {
            $query = Html::ReadSession($this->id);
            foreach ($this->structure as $clause) {
                if (isset($query[$clause])) {
                    $this->$clause = $query[$clause];
                }
            }
        }
    }

    /**
     * Adds a SET in the INSERT satement
     *
     * @param   string      $set        - The SET field and value
     */
    protected function addInsertSet($field, $value, $quoted = true) {

        !$quoted || $value = '"' . $value . '"';
        $this->insertset[$field] = $value;

    }

    /**
     * Sets the table for INSERT statements
     *
     * @param   string      $table      - The table name
     */
    protected function setInsertTable($table) {
        $this->inserttable = $table;
    }

    /**
     * Adds a special condition in
     * INSERT statements (subqueries and stuff)
     *
     * @param   string      $special        - The special condition
     * @param   string      $separator      - If we have a special character for separating this special
     * @param   int         $offset         - If we must add the separator only after a certain offset
     */
    protected function addInsertSpecials($special, $separator = '', $offset = 1) {
        if (count($this->insertspecials) > $offset) $special = $separator . $special;
        $this->insertspecials[] = $special;
    }

    /**
     * Returns the INSERT statement
     *
     * @return  string      - The statement
     */
    protected function getInsert() {
        $insertSet = array();
        foreach ($this->insertset as $field => $value) $insertSet[] = $field . ' = ' . $value;
        $query = 'INSERT INTO ' . $this->inserttable . ' SET ' . implode(',', $insertSet);
        if (count($this->insertspecials) > 0) {
            $query .= ' ' . implode(' ', $this->insertspecials);
        }
        return $query;
    }

    /**
     * Sets the table for UPDATE
     * queries
     *
     * @param   string      $table      - The table name
     */
    protected function setUpdateTable($table) {
        $this->updatetable = $table;
    }

    /**
     * Returns the UPDATE query
     *
     * @param   bool        $safe       - Safe Update: will not run if there's no WHERE clause
     * @return  string                  - The statement
     */
    protected function getUpdateQuery($safe = true) {
        if ($safe && count($this->updatewhere) == 0) return '';
        $query = 'UPDATE ' . $this->updatetable . ' SET ' . implode(',', $this->updateset);
        if (count($this->updatewhere) > 0) {
            $query .= ' WHERE (1) ' . (count($this->updatewhere) > 0 ? ' AND ' . implode(' ', $this->updatewhere) : '');
        }
        if (count($this->updatespecials) > 0) {
            $query .= ' ' . implode(' ', $this->updatespecials);
        }
        return $query;
    }

    /**
     * Adds a condition to WHERE clause
     * for UPDATE statements
     *
     * @param   string      $where      - The condition
     * @param   string      $operator   - AND | OR
     */
    protected function addUpdateWhere($where, $operator = 'AND') {
        $this->updatewhere[] = (count($this->updatewhere) > 0 ? $operator : '') . ' ' . $where;
    }

    /**
     * Adds a SET for UPDATE queries
     *
     * @param   string      $set        - The field and value
     */
    protected function addUpdateSet($set) {
        if (is_array($set)) {
            foreach ($set as $field => $value) {
                $this->updateset[] = $field . '=' . utf8_encode($value);
            }
        } else {
            $this->updateset[] = $set;
        }
    }

    /**
     * Is the DataSet empty?
     *
     * @return  bool
     */
    protected function isEmpty() {
        return count($this->dataset) == 0;
    }

    /**
     * Clears sets in statements
     */
    protected function clear() {
        $args = func_get_args();
        if (count($args) > 0) {
            foreach ($args as $clause) {
                if (property_exists($this, $clause)) {
                    $this->$clause = array();
                }
            }
        } else {
            foreach ($this->structure as $clause) {
                $this->$clause = array();
            }
        }
    }

    /**
     * Clears all SELECT clauses
     */
    protected function clearSelect() {
        $this->select = array();
        $this->from = array();
        $this->where = array();
        $this->group = array();
        $this->order = array();
        $this->limit = array();
    }

    /**
     * Clears all INSERT clauses
     */
    protected function clearInsert() {
        $this->insertset = array();
        $this->inserttable = '';
        $this->insertspecials = array();
    }

    /**
     * Clears all UPDATE clauses
     */
    protected function clearUpdate() {
        $this->updateset = array();
        $this->updatetable = '';
        $this->updatewhere = array();
        $this->updatespecials = array();
    }

    /**
     * Clears all UPDATE clauses
     */
    protected function clearDelete() {
        $this->deletefrom = '';
        $this->deletewhere = array();
    }

    /**
     * Returns a specific result row number
     *
     * @param   int     $row        - The row number
     * @return  array|bool          - The row array or false if doesn't exist
     */
    public function getRow($row) {
        if (isset($this->dataset[$row])) {
            return $this->dataset[$row];
        }
        return false;
    }

    /**
     * Returns all result rows
     *
     * @return  array               - The result array
     */
    public function getRows() {
        return $this->dataset;
    }

    /**
     * Returns if a query executed successfully
     *
     * @return bool
     */
    public function queryOk() {
        return $this->errorInfo['code'] == 0;
    }

    /**
     * Returns the last error code
     * or 0 if no errors occurred
     *
     * @return  int
     */
    public function getErrorCode() {
        return $this->errorInfo['code'];
    }

    /**
     * Returns the last error message
     * or empty if no errors occurred
     *
     * @return mixed
     */
    public function getError() {
        return $this->errorInfo['message'];
    }


    /**
     * Display the Dataset in a grid
     */
    public function dbGrid() {

        $view = new View();

        $view->setVariable('id', $this->id);
        $view->setVariable('showTitles', $this->dbGridShowHeader);
        $view->setVariable('rowAction', $this->gridRowLink['action']);
        $view->setVariable('rowFieldId', $this->gridRowLink['fieldId']);

        if ($this->dbGridAutoHeader) {
            $this->dbGridColumns = array();
            foreach ($this->getRow(0) as $field => $value) {
                $this->addGridColumn(ucwords(String::decamelize($field)), $field);
            }
        }

        $view->setVariable('head', $this->dbGridColumns);
        $view->setVariable('gridClass', $this->gridClass);
        $view->setVariable('content', $this->dataset);
        $view->loadTemplate($this->dbGridTemplate);

        return $view->render();

    }


}

?>
