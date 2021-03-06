<?php

    namespace thebuggenie\modules\publish\entities\b2db;

    use TBGUsersTable,
        TBGScopesTable;

    /**
     * @Table(name="articleviews")
     */
    class ArticleViews extends \TBGB2DBTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'articleviews';
        const ID = 'articleviews.id';
        const ARTICLE_ID = 'articleviews.article_id';
        const USER_ID = 'articleviews.user_id';
        const SCOPE = 'articleviews.scope';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addForeignKeyColumn(self::USER_ID, TBGUsersTable::getTable(), TBGUsersTable::ID);
            parent::_addForeignKeyColumn(self::ARTICLE_ID, Articles::getTable(), Articles::ID);
            parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
        }
    }

