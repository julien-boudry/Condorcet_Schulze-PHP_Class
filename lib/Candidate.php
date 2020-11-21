<?php
/*
    Condorcet PHP - Election manager and results calculator.
    Designed for the Condorcet method. Integrating a large number of algorithms extending Condorcet. Expandable for all types of voting systems.

    By Julien Boudry and contributors - MIT LICENSE (Please read LICENSE.txt)
    https://github.com/julien-boudry/Condorcet
*/
declare(strict_types=1);

namespace CondorcetPHP\Condorcet;

use CondorcetPHP\Condorcet\CondorcetDocAttributes\{PublicAPI};
use CondorcetPHP\Condorcet\Throwable\CondorcetException;

class Candidate implements \Stringable
{
    use Linkable, CondorcetVersion;

    private array $_name = [];
    private bool $_provisional = false;

        ///

    #[PublicAPI]
    public function __construct (string $name)
    {
        $this->setName($name);
    }

    public function __toString () : string
    {
        return $this->getName();
    }

        ///

    // SETTERS

    #[PublicAPI]
    public function setName (string $name) : bool
    {
        $name = \trim($name);

        if (\strlen($name) > Election::MAX_LENGTH_CANDIDATE_ID ) :
            throw new CondorcetException(1, $name);
        endif;

        if ( \preg_match('/<|>|\n|\t|\0|\^|\$|:|;|(\|\|)|"|#/mi',$name) === 1 ) :
            throw new CondorcetException(1, $name);
        endif;

        if (!$this->checkNameInElectionContext($name)) :
            throw new CondorcetException(19, $name);
        endif;

        $this->_name[] =  [ 'name' => $name, 'timestamp' => \microtime(true) ];

        return true;
    }

    public function setProvisionalState (bool $provisional) : bool
    {
        $this->_provisional = $provisional;
        return true;
    }

    // GETTERS

    #[PublicAPI]
    public function getName () : string
    {
        return \end($this->_name)['name'];
    }

    #[PublicAPI]
    public function getHistory () : array
    {
        return $this->_name;
    }

    #[PublicAPI]
    public function getCreateTimestamp () : float
    {
        return $this->_name[0]['timestamp'];
    }

    #[PublicAPI]
    public function getTimestamp () : float
    {
        return \end($this->_name)['timestamp'];
    }

    #[PublicAPI]
    public function getProvisionalState () : bool
    {
        return $this->_provisional;
    }

        ///

    // INTERNAL

    private function checkNameInElectionContext (string $name) : bool
    {
        foreach ($this->_link as $link) :
            if (!$link->canAddCandidate($name)) :
                return false;
            endif;
        endforeach;

        return true;
    }
}
