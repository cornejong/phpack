<?php

namespace SouthCoast\Console;

class Cursor
{
        /**
	 * Pointer to the stream where the data is sent
	 *
	 * @var resource
	 */
	public static $stream = STDERR;
    
    protected static $printList = [
        'home' => "\033[H",
        'save' => "\033[s",
        'restore' => "\033[u",
        'clearLine' => "\033[2K",
        'clearLineBefore' => "\033[1K",
        'clearLineAfter' => "\033[0K",
        'clearAllBefore' => "\033[1J",
        'clearAllAfters' => "\033[0J",
    ];

    public function __call($name, $arguments)
    {
        if (array_key_exists($name, self::$printList)) {
            echo self::$printList[$name];
        }

        return $this;
    }

    public static function moveUp(int $lines = 1)
    {
        if ($lines === 0) {
            return;
        }

        echo "\033[{$lines}A";
    }

    public static function moveDown(int $lines = 1)
    {
        if ($lines === 0) {
            return;
        }

        echo "\033[{$lines}B";
    }

    public static function moveForward(int $characters = 1)
    {
        if ($characters === 0) {
            return;
        }

        echo "\033[{$characters}C";
    }

    public static function moveBack(int $characters = 1)
    {
        if ($characters === 0) {
            return;
        }

        echo "\033[{$characters}D";
    }

    public static function moveLeft(int $characters = 1)
    {
        if ($characters === 0) {
            return;
        }

        return self::moveBack($characters);
    }

    public static function moveRight(int $characters = 1)
    {
        if ($characters === 0) {
            return;
        }

        return self::moveForward($characters);
    }

    public static function moveToStartOfLineBelow(int $lines = 1)
    {
        if ($lines === 0) {
            return;
        }

        /* Move to beginning of line below */
        echo "\033[{$lines}E";
    }

    public static function moveToStartOfLineAbove(int $lines = 1)
    {
        if ($lines === 0) {
            return;
        }

        /* Move to beginning of line above */
        echo "\033[{$lines}F";
    }

    public static function moveToLineStart()
    {
        self::moveToStartOfLineAbove();
        self::moveToStartOfLineBelow();
    }

	/**
	 * Move the cursor up by count
	 *
	 * @param int $count
	 */
	public static function up( $count = 1 ) {
		fwrite(self::$stream, "\033[{$count}A");
	}

	/**
	 * Move the cursor down by count
	 *
	 * @param int $count
	 */
	public static function down( $count = 1 ) {
		fwrite(self::$stream, "\033[{$count}B");
	}

	/**
	 * Move the cursor right by count
	 *
	 * @param int $count
	 */
	public static function forward( $count = 1 ) {
		fwrite(self::$stream, "\033[{$count}C");
	}

	/**
	 * Move the cursor left by count
	 *
	 * @param int $count
	 */
	public static function back( $count = 1 ) {
		fwrite(self::$stream, "\033[{$count}D");
	}

    /**
	 * Move the cursor to a specific row and column
	 *
	 * @param int $row
	 * @param int $col
	 */
	public static function rowcol( $row = 1, $col = 1 ) {
		$row = intval($row);
		$col = intval($col);
		if( $row < 0 ) {
			$row = Misc::rows() + $row + 1;
		}
		if( $col < 0 ) {
			$col = Misc::cols() + $col + 1;
		}
		fwrite(self::$stream, "\033[{$row};{$col}f");
	}

	/**
	 * Save the current cursor position
	 */
	public static function savepos() {
		fwrite(self::$stream, "\033[s");
	}

	/**
	 * Save the current cursor position and attributes
	 */
	public static function save() {
		fwrite(self::$stream, "\0337");
	}

	/**
	 * Delete the currently saved cursor data
	 */
	public static function forget() {
		fwrite(self::$stream, "\033[u");
	}

	/**
	 * Restore the previously saved cursor data
	 */
	public static function restore() {
		fwrite(self::$stream, "\0338");
	}

	/**
	 * Hides the cursors
	 */
	public static function hide() {
		fwrite(self::$stream, "\033[?25l");
	}

	/**
	 * Shows the cursor
	 */
	public static function show() {
		fwrite(self::$stream, "\033[?25h\033[?0c");
	}

	/**
	 * Enable/Disable Auto-Wrap
	 *
	 * @param bool $wrap
	 */
	public static function wrap( $wrap = true ) {
		if( $wrap ) {
			fwrite(self::$stream, "\033[?7h");
		} else {
			fwrite(self::$stream, "\033[?7l");
		}
	}
}
