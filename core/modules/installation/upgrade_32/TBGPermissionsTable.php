<?php

    namespace thebuggenie\core\modules\installation\upgrade_32;

    /**
     * Permissions table
     *
     * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
     * @version 3.1
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage tables
     */

    /**
     * Permissions table
     *
     * @package thebuggenie
     * @subpackage tables
     *
     * @Table(name="permissions_32")
     */
    class TBGPermissionsTable extends \TBGB2DBTable
    {

        const B2DBNAME = 'permissions';
        const ID = 'permissions.id';
        const SCOPE = 'permissions.scope';
        const PERMISSION_TYPE = 'permissions.permission_type';
        const TARGET_ID = 'permissions.target_id';
        const UID = 'permissions.uid';
        const GID = 'permissions.gid';
        const TID = 'permissions.tid';
        const ALLOWED = 'permissions.allowed';
        const MODULE = 'permissions.module';

        protected function _initialize()
        {
            parent::_setup(self::B2DBNAME, self::ID);
            parent::_addVarchar(self::PERMISSION_TYPE, 100);
            parent::_addVarchar(self::TARGET_ID, 200, 0);
            parent::_addBoolean(self::ALLOWED);
            parent::_addVarchar(self::MODULE, 50);
            parent::_addInteger(self::UID, 10);
            parent::_addInteger(self::GID, 10);
            parent::_addInteger(self::TID, 10);
            parent::_addInteger(self::SCOPE, 10);
        }

    }
