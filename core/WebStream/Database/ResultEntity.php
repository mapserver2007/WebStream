<?php
namespace WebStream\Database;

/**
 * ResultEntity
 * @author Ryuichi TANAKA.
 * @since 2015/01/11
 * @version 0.4
 */
class ResultEntity extends Result
{
    /**
     * @var EntityManager エンティティマネージャ
     */
    private $entityManager;

    /**
     * コンストラクタ
     * @param PDOStatement ステートメントオブジェクト
     * @param string エンティティクラスパス
     */
    public function __construct(\PDOStatement $stmt, $classpath)
    {
        parent::__construct($stmt);
        $this->entityManager = new EntityManager($classpath);
        $this->entityManager->setColumnMeta($this->getColumnMeta());
    }

    /**
     * Implements Iterator#current
     * 現在の要素を返却する
     * @return array<string> 列データ
     */
    public function current()
    {
        if ($this->stmt === null) {
            return array_key_exists($this->position, $this->rowCache) ? $this->rowCache[$this->position] : null;
        }

        return $this->entityManager->getEntity($this->row);
    }

    /**
     * テーブルのメタデータを返却する
     * @return array<string> メタデータ
     */
    private function getColumnMeta()
    {
        $columnMeta = [];
        for ($index = 0; $index < $this->stmt->columnCount(); $index++) {
            $column = $this->stmt->getColumnMeta($index);
            if (array_key_exists('sqlite:decl_type', $column)) {
                // sqlite
                $columnMeta[$column['name']] = $column['sqlite:decl_type'];
            } else {
                // mysql, postgresql
                $columnMeta[$column['name']] = $column['native_type'];
            }
        }

        return $columnMeta;
    }

    /**
     * {@inheritdoc}
     */
    public function toEntity()
    {
        return $this;
    }
}