<?php

namespace yangguanghui\extFinal\generators\db;

use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

class Generator extends \yangguanghui\extFinal\Generator
{
    public $configFile = '@common/config/main-local.php';
    public $defaultDbClass = 'yii\db\Connection';
    public $defaultDbComponent = 'db';
    public $dbDriver = 'mysql';
    public $dbHost = '127.0.0.1';
    public $dbPort = '3306';
    public $dbName;
    public $username = 'root';
    public $password;
    public $charset = 'utf8';
    public $dsn;
    public $emptyDsn;
    /**
     * @var \yii\db\Connection a empty Connection object
     */
    public $emptyConnection;
    
    public function init() {
        parent::init();
        $this->configFile = Yii::getAlias($this->configFile);
    }
    
    public function save(&$result) {
        $hasError = false;
        $lines = ["Begin connection..."];
        try {
            $this->createEmptyConnection();
            $lines[] = "Begin create DB...";
            $this->executeCreateDB();
        } catch (Exception $e) {
            $lines[] = $e->getMessage();
            $hasError = true;
        }
        $lines[] = "Save to file... ";
        if (!$this->saveToFile()) {
            $lines[] = "Save error!";
        }
        $lines[] = "done!\n";
        $result = \implode('\n', $lines);
        return !$hasError;
    }
    
    public function saveToFile() {
        $dbConfig = $this->makeDbConfig();
        $oldConfig = require($this->configFile);
        $oldConfig = $oldConfig['components']['db'];
        $config = ArrayHelper::merge($oldConfig, $dbConfig);
        $data = $this->formatConfig($config);
        return \file_put_contents($this->configFile, $data);
    }
    
    public function formatConfig($config) {
        $string = \var_export($config, true);
        $arr = explode("\n",$string);
        $arr = array_slice($c, 1, count($arr) - 2);
        $arr = array_map(function($v) {return '         ' . $v;}, $arr);
        $string = \implode("\n", $arr);
        $content = \file_get_contents($this->configFile);
        $data = preg_replace('/(\'db\' => \[\n).*?(,\n\ *\])/s',
            '$1' . $string . '$2',
            $content);
        return $data;
    }
    
    public function makeDbConfig()
    {
        $this->makeDsnString();
        return [
            'class' => $this->defaultDbClass,
            'dsn' => $this->dsn,
            'username' => $this->username,
            'password' => $this->password,
            'charset' => $this->charset,
        ];
    }
    
    public function createDB(&$result) {
        
    }
    
    public function executeCreateDB() {
        $sql = "CREATE DATABASE `" . $this->dbName 
            . "` DEFAULT CHARACTER SET ". $this->charset 
            . "COLLATE " . $this->charset . "_general_ci";
        return $this->emptyConnection->createCommand($sql)->execute();
    }
    
    public function makeDsnEmptyString() {
        $this->emptyDsn = $this->dbDriver 
            . ":host=" . $this->dbHost 
            . ";port=" . $this->dbPort;
    }
    
    public function makeDsnString() {
        $this->dsn = $this->makeDsnEmptyString() . ";dbname=" . $this->dbName;
    }
    
    public function createEmptyConnection() {
        $con = new Connection();
        $con->dsn = $this->makeDsnEmptyString();
        $con->username = $this->username;
        $con->password = $this->username;
        $con->charset = $this->charset;
        $con->open();
        $this->emptyConnection = $con;
    }
    
    public function getValidDriver()
    {
        return ['mysql'=>'mysql'];
    }
    
    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'DB Config Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates a DB config file.';
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $files[] = new CodeFile(
            Yii::getAlias($this->viewPath) . '/' . $this->viewName . '.php',
            $this->render('form.php')
        );

        return $files;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dbDriver', 'dbHost', 'dbPort', 'dbName', 'username' , 'password', 'charset'], 'trim'],
            [['dbDriver', 'dbHost', 'dbName', 'username', 'charset'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'dbDriver' => 'Database Driver',
            'dbHost' => 'Database Host',
            'dbPort' => 'Database Port',
            'dbName' => 'Database Name',
            'username' => 'User Name',
            'password' => 'Password',
            'charset' => 'Charset',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['form.php', 'action.php'];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), 
            ['dbDriver', 'dbHost', 'dbPort', 'dbName', 'username', 'charset']);
    } 

    /**
     * @inheritdoc
     */
    public function successMessage()
    {
        $code = highlight_string($this->render('action.php'), true);

        return <<<EOD
<p>The form has been generated successfully.</p>
<p>You may add the following code in an appropriate controller class to invoke the view:</p>
<pre>$code</pre>
EOD;
    }

    /**
     * @return array list of safe attributes of [[modelClass]]
     */
    public function getModelAttributes()
    {
        /* @var $model Model */
        $model = new $this->modelClass();
        if (!empty($this->scenarioName)) {
            $model->setScenario($this->scenarioName);
        }

        return $model->safeAttributes();
    }
}
