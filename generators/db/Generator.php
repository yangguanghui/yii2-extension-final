<?php

namespace yangguanghui\extFinal\generators\db;

use Yii;
use yii\base\Model;

class Generator extends \yangguanghui\extFinal\Generator
{
    public $configFile = '@common/config/main-local.php';
    public $dbDriver = 'mysql';
    public $dbHost = '127.0.0.1';
    public $dbPort = '3306';
    public $dbName;
    
    public function getValidDriver()
    {
        return ['mysql'];
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
            [['dbDriver', 'dbHost', 'dbPort', 'dbName'], 'trim'],
            [['dbDriver', 'dbHost', 'dbName'], 'required'],
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
            ['dbDriver', 'dbHost', 'dbPort', 'dbName']);
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
