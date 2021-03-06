<?php

    namespace thebuggenie\modules\mailing\entities\b2db;

    use TBGContext,
        TBGScopesTable,
        b2db\Criteria;

    /**
     * @Table(name="mailing_queue")
     */
    class MailQueueTable extends \TBGB2DBTable
    {

        const B2DB_TABLE_VERSION = 1;
        const B2DBNAME = 'mailing_queue';
        const ID = 'mailing_queue.id';
        const MESSAGE = 'mailing_queue.headers';
        const DATE = 'mailing_queue.date';
        const SCOPE = 'mailing_queue.scope';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addText(self::MESSAGE);
            parent::_addInteger(self::DATE, 10);
            parent::_addForeignKeyColumn(self::SCOPE, TBGScopesTable::getTable(), TBGScopesTable::ID);
        }

        public function addMailToQueue(TBGMimemail $mail)
        {
            $message = serialize($mail);
            $crit = $this->getCriteria();
            $crit->addInsert(self::MESSAGE, $message);
            $crit->addInsert(self::DATE, NOW);
            $crit->addInsert(self::SCOPE, TBGContext::getScope()->getID());

            $res = $this->doInsert($crit);

            return $res->getInsertID();
        }

        public function getQueuedMessages($limit = null)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());
            if ($limit !== null)
            {
                $crit->setLimit($limit);
            }
            $crit->addOrderBy(self::DATE, Criteria::SORT_ASC);

            $messages = array();
            $res = $this->doSelect($crit);

            if ($res)
            {
                while ($row = $res->getNextRow())
                {
                    $message = $row->get(self::MESSAGE);
                    $messages[$row->get(self::ID)] = unserialize($message);
                }
            }

            return $messages;
        }

        public function deleteProcessedMessages($ids)
        {
            $crit = $this->getCriteria();
            $crit->addWhere(self::ID, (array) $ids, Criteria::DB_IN);
            $crit->addWhere(self::SCOPE, TBGContext::getScope()->getID());

            $res = $this->doDelete($crit);
        }

    }
