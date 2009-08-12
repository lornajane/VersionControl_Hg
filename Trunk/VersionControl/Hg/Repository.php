<?php

/**
 * Contains the Repository class.
 *
 * @category VersionControl
 * @package Hg
 * @subpackage Repository
 * @author Michael Gatto <mgatto@u.arizona.edu>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link
 */

/**
 * Represents the Repository as an entity.
 *
 * Usage:
 *
 * $repo = new Repository();
 * $repo->load( '/path/' )
 *
 * As a child of Hg.php:
 * $hg = new Hg();
 * $repo = $hg->getRepository( '/path/' );
 *
 * Or:
 * $repo = hg::instance()->load('/path/');
 * $repo = hg::instance()->create('/path/');
 *
 * @category VersionControl
 * @package Hg
 * @subpackage Repository
 * @author Michael Gatto <mgatto@u.arizona.edu>
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 * @link
 */
class VersionControl_Hg_Repository
{
    /**
     * The name of all Mercurial repository roots.
     *
     * Leading backslash is needed since _path may not have a trailing slash.
     *
     * @const string
     */
    const ROOT_NAME = '/.hg';

    /**
     * Holds the filesystem path of a Mercurial repository.
     *
     * @var string
     */
    private $_repository;

    /**
     * Holds the current command operating on the repository.
     *
     * @var string
     */
    private $_command;

    /**
     * Repository constructor which currently does nothing.
     *
     * @todo might be a good place to set the transport method?
     */
    public function __construct()
    {

    }

    /**
     * Sets the path of a Mercurial repository after validating it as a Hg repo.
     *
     * @param string $path as a local filesystem path.
     * @return mixed Repository to enable method chaining
     * @see $_repository
     */
    public function setRepository($path)
    {
        /*
         * Let's not guess that the user wants to create a repo if none exists;
         * Throw and exception and let them decide what to do next.
         * Maybe they just gave the wrong path.
         */
        if ( ! $this->isRepository($path)) {
            throw new Exception(
                'there is no Mercurial repository at: '
                . $this->getPath()
                . '. Use $hg->createRepository( \'/path/\' ) to create one and then use getRepository() to act upon it.' );
        }

        $this->_repository = $path;

        return $this; //for chainable methods.
    }

    /**
     * Returns the path of a Mercurial repository as set by the user.
     * It is validated before being set as a class member.
     *
     * @return string
     * @see $_path
     * @see $_path
     */
    public function getRepository()
    {
        if ( null === $this->_repository ) {
            throw new Exception(
                'There is no repository to return'
            );
        }

        return $this->_repository;
    }

    /**
     * Checks if $this is in fact a valid
     *
     * @param string $repo is the full repository path.
     * @return boolean
     */
    public function isRepository($path)
    {
        /*
         * @todo a valid repo has this structure, so test for this:
         * .hg
         *  |---store/
         *        |---data/ (directory)
         *  |---dirstate (file)
         */

        $is_repository = false;

        $repository = $path . self::ROOT_NAME;
        /*
         * both conditions must be satisfied.
         */
        if ( is_dir($repository) && ! empty($repository) ) {
            $is_repository = true;
        } else {
            $is_repository = false;
        }

        return $is_repository;
    }


    /**
     *
     * @return
     */
    public function create()
    {
        $this->_command = new Hg_Repository_Command_Init();
        //$this->_command = new Hg_Repository_Command_Init($this);
            //pass $this as dependency injection instead of having
            //Hg_Repository_Command inherit from Hg_Repository?


        //return it so we can chain it
        return $this->_command;
    }

    /**
     * Deletes the repository, effectively removing the working copy from SCM.
     *
     * @return boolean
     */
    public function delete()
    {
        if ( unlink(  ) ) {
            return true;
        } else {
            return false;
            //throw new Exception( 'The repository could not be deleted.' );
        }

    }

    /**
     *
     * @param $function
     * @param $options
     * @return unknown_type
     */
    public function __call($function, $options)
    {
        if( class_exists( $function ) ) {
            $cmd = new $function($this, $options);
        } else {
            throw new VersionControl_Hg_Exception(
                'Sorry, The command \'{$function}\' is not implemented.'
            );
        }
    }

    //this is a temporary alias for __call...
    public function command() {}

}
