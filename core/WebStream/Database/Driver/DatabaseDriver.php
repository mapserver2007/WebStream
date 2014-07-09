<?php
namespace WebStream\Database\Driver;

use WebStream\Module\Logger;

/**
 * DatabaseDriver
 * @author Ryuichi TANAKA.
 * @since 2013/12/07
 * @version 0.4
 */
abstract class DatabaseDriver
{
    /** connection */
    protected $connection;

    /** host */
    protected $host;

    /** port */
    protected $port;

    /** dbname */
    protected $dbname;

    /** user name */
    protected $username;

    /** password */
    protected $password;

    /** dbfile */
    protected $dbfile;

    /**
     * constructor
     */
    public function __construct()
    {
        Logger::debug("Load driver: " . get_class($this));
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        Logger::debug("Release driver: " . get_class($this));
    }

    /**
     * 接続する
     */
    abstract public function connect();

    /**
     * 切断する
     */
    public function disconnect()
    {
        if ($this->connection !== null) {
            Logger::debug("Database disconnect.");
            $this->connection = null;
        }
    }

    /**
     * トランザクションを開始する
     * @return boolean トランザクション開始結果
     */
    public function beginTransaction()
    {
        return $this->connection !== null ? $this->connection->beginTransaction() : false;
    }

    /**
     * コミットする
     */
    public function commit()
    {
        if ($this->connection !== null) {
            $this->connection->commit();
        }
    }

    /**
     * ロールバックする
     */
    public function rollback()
    {
        if ($this->connection !== null) {
            $this->connection->rollback();
        }
    }

    /**
     * DB接続されているか
     * @param boolean 接続有無
     */
    public function isConnected()
    {
        return $this->connection !== null;
    }

    /**
     * トランザクション内かどうか
     * @return boolean トランザクション内かどうか
     */
    public function inTransaction()
    {
        return $this->connection !== null ? $this->connection->inTransaction() : false;
    }

    /**
     * SQLをセットしてステートメントを返却する
     * @param string SQL
     * @return object ステートメント
     */
    public function getStatement($sql)
    {
        return $this->connection !== null ? $this->connection->prepare($sql) : null;
    }

    /**
     * ホスト名を設定する
     * @param string ホスト名
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * ポート番号を設定する
     * @param string ポート番号
     */
    public function setPort($port)
    {
        $this->port = $port;
    }

    /**
     * DB名を設定する
     * @param string DB名
     */
    public function setDbname($dbname)
    {
        $this->dbname = $dbname;
    }

    /**
     * 接続ユーザ名を設定する
     * @param string 接続ユーザ名
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * パスワードを設定する
     * @param string パスワード
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * DBファイルを設定する
     * @param string DBファイル
     */
    public function setDbfile($dbfile)
    {
        $this->dbfile = $dbfile;
    }
}
