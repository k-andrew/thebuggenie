<?php

    namespace thebuggenie\modules\vcs_integration\entities;

    /**
     * File class, vcs_integration
     *
     * @author Philip Kent <kentphilip@gmail.com>
     * @version 3.2
     * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
     * @package thebuggenie
     * @subpackage vcs_integration
     */

    /**
     * File class, vcs_integration
     *
     * @package thebuggenie
     * @subpackage vcs_integration
     *
     * @Table(name="\thebuggenie\modules\vcs_integration\entities\b2db\Files")
     */
    class File extends \TBGIdentifiableScopedClass
    {

        /**
         * File path
         * @var string
         * @Column(type="text")
         */
        protected $_file_name = null;

        /**
         * Action applied to file (Added, Updated or Deleted)
         * @var string
         * @Column(type="string", length=1)
         */
        protected $_action = null;

        /**
         * Associated commit
         * @var TBGVCSIntegrationCommit
         * @Column(type="integer", name="commit_id")
         * @Relates(class="\thebuggenie\modules\vcs_integration\entities\Commit")
         */
        protected $_commit = null;

        /**
         * Get the file path
         * @return string
         */
        public function getFile()
        {
            return $this->_file_name;
        }

        /**
         * Get the file action
         * @return string
         */
        public function getAction()
        {
            return $this->_action;
        }

        /**
         * Get the commit with this link
         * @return \thebuggenie\modules\vcs_integration\entities\Commit
         */
        public function getCommit()
        {
            return $this->_b2dbLazyload('_commit');
        }

        /**
         * Set the file path
         * @param string $file
         */
        public function setFile($file)
        {
            $this->_file_name = $file;
        }

        /**
         * Set the action applied (M/A/D)
         * @param string $action
         */
        public function setAction($action)
        {
            $this->_action = $action;
        }

        /**
         * Set the commit this change applies to
         * @param \thebuggenie\modules\vcs_integration\entities\Commit $commit
         */
        public function setCommit(Commit $commit)
        {
            $this->_commit = $commit;
        }

    }
