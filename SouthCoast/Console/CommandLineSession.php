<?php

namespace SouthCoast\Console;

class CommandLineSession
{
    public function start()
    {
        system("stty -icanon");
        echo "input# ";

            
        while ($this->shouldRun()) {
            system("stty -icanon");
            echo "input# ";
            while ($c = fread(STDIN, 1)) {
                echo "Read from STDIN: " . $c . "\ninput# ";
            }
        }
    }

    public function startSession(callable $callback)
    {
        readline_callback_handler_install('', function () {
        });

        $input = '';
        
        while (true) {
            $r = array(STDIN);
            $w = null;
            $e = null;
            $n = stream_select($r, $w, $e, null);
            if ($n && in_array(STDIN, $r)) {
                $character = stream_get_contents(STDIN, 1);
                if (ord($character) === 13) {
                    break;
                }

                $input += $character;
            }
        }

        print($input);

        readline_callback_handler_remove();
    }
}
