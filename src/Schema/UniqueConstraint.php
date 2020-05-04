<?php

namespace Doctrine\DBAL\Schema;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use function array_keys;
use function array_map;
use function strtolower;

/**
 * Class for a unique constraint.
 */
class UniqueConstraint extends AbstractAsset implements Constraint
{
    /**
     * Asset identifier instances of the column names the unique constraint is associated with.
     * array($columnName => Identifier)
     *
     * @var Identifier[]
     */
    protected $columns = [];

    /**
     * Platform specific flags.
     * array($flagName => true)
     *
     * @var true[]
     */
    protected $flags = [];

    /**
     * Platform specific options.
     *
     * @var mixed[]
     */
    private $options = [];

    /**
     * @param string   $name
     * @param string[] $columns
     * @param string[] $flags
     * @param mixed[]  $options
     */
    public function __construct(string $name, array $columns, array $flags = [], array $options = [])
    {
        $this->_setName($name);

        $this->options = $options;

        foreach ($columns as $column) {
            $this->addColumn($column);
        }

        foreach ($flags as $flag) {
            $this->addFlag($flag);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return array_keys($this->columns);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuotedColumns(AbstractPlatform $platform)
    {
        $columns = [];

        foreach ($this->columns as $column) {
            $columns[] = $column->getQuotedName($platform);
        }

        return $columns;
    }

    /**
     * @return string[]
     */
    public function getUnquotedColumns() : array
    {
        return array_map([$this, 'trimQuotes'], $this->getColumns());
    }

    /**
     * Returns platform specific flags for unique constraint.
     *
     * @return string[]
     */
    public function getFlags() : array
    {
        return array_keys($this->flags);
    }

    /**
     * Adds flag for a unique constraint that translates to platform specific handling.
     *
     * @param string $flag
     *
     * @return self
     *
     * @example $uniqueConstraint->addFlag('CLUSTERED')
     */
    public function addFlag(string $flag) : UniqueConstraint
    {
        $this->flags[strtolower($flag)] = true;

        return $this;
    }

    /**
     * Does this unique constraint have a specific flag?
     *
     * @param string $flag
     *
     * @return bool
     */
    public function hasFlag(string $flag) : bool
    {
        return isset($this->flags[strtolower($flag)]);
    }

    /**
     * Removes a flag.
     *
     * @param string $flag
     *
     * @return void
     */
    public function removeFlag(string $flag) : void
    {
        unset($this->flags[strtolower($flag)]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasOption(string $name) : bool
    {
        return isset($this->options[strtolower($name)]);
    }

    /**
     * @return mixed
     */
    public function getOption(string $name)
    {
        return $this->options[strtolower($name)];
    }

    /**
     * @return mixed[]
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @return void
     */
    protected function addColumn(string $column) : void
    {
        $this->columns[$column] = new Identifier($column);
    }
}
