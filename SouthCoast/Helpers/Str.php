<?php

namespace SouthCoast\Helpers;

class Str
{
    /**
     * Plural rules.
     *
     * @var string[][]
     */
    private static $plural = array(
        'rules' => array(
            '/(s)tatus$/i' => '\1\2tatuses',
            '/(quiz)$/i' => '\1zes',
            '/^(ox)$/i' => '\1\2en',
            '/([m|l])ouse$/i' => '\1ice',
            '/(matr|vert|ind)(ix|ex)$/i' => '\1ices',
            '/(x|ch|ss|sh)$/i' => '\1es',
            '/([^aeiouy]|qu)y$/i' => '\1ies',
            '/(hive|gulf)$/i' => '\1s',
            '/(?:([^f])fe|([lr])f)$/i' => '\1\2ves',
            '/sis$/i' => 'ses',
            '/([ti])um$/i' => '\1a',
            '/(c)riterion$/i' => '\1riteria',
            '/(p)erson$/i' => '\1eople',
            '/(m)an$/i' => '\1en',
            '/(c)hild$/i' => '\1hildren',
            '/(f)oot$/i' => '\1eet',
            '/(buffal|her|potat|tomat|volcan)o$/i' => '\1\2oes',
            '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|vir)us$/i' => '\1i',
            '/us$/i' => 'uses',
            '/(alias)$/i' => '\1es',
            '/(analys|ax|cris|test|thes)is$/i' => '\1es',
            '/s$/' => 's',
            '/^$/' => '',
            '/$/' => 's',
        ),
        'uninflected' => array(
            '.*[nrlm]ese',
            '.*deer',
            '.*fish',
            '.*measles',
            '.*ois',
            '.*pox',
            '.*sheep',
            'people',
            'cookie',
            'police',
        ),
        'irregular' => array(
            'atlas' => 'atlases',
            'axe' => 'axes',
            'beef' => 'beefs',
            'brother' => 'brothers',
            'cafe' => 'cafes',
            'canvas' => 'canvases',
            'chateau' => 'chateaux',
            'niveau' => 'niveaux',
            'child' => 'children',
            'cookie' => 'cookies',
            'corpus' => 'corpuses',
            'cow' => 'cows',
            'criterion' => 'criteria',
            'curriculum' => 'curricula',
            'demo' => 'demos',
            'domino' => 'dominoes',
            'echo' => 'echoes',
            'foot' => 'feet',
            'fungus' => 'fungi',
            'ganglion' => 'ganglions',
            'gas' => 'gases',
            'genie' => 'genies',
            'genus' => 'genera',
            'goose' => 'geese',
            'graffito' => 'graffiti',
            'hippopotamus' => 'hippopotami',
            'hoof' => 'hoofs',
            'human' => 'humans',
            'iris' => 'irises',
            'larva' => 'larvae',
            'leaf' => 'leaves',
            'loaf' => 'loaves',
            'man' => 'men',
            'medium' => 'media',
            'memorandum' => 'memoranda',
            'money' => 'monies',
            'mongoose' => 'mongooses',
            'motto' => 'mottoes',
            'move' => 'moves',
            'mythos' => 'mythoi',
            'niche' => 'niches',
            'nucleus' => 'nuclei',
            'numen' => 'numina',
            'occiput' => 'occiputs',
            'octopus' => 'octopuses',
            'opus' => 'opuses',
            'ox' => 'oxen',
            'passerby' => 'passersby',
            'penis' => 'penises',
            'person' => 'people',
            'plateau' => 'plateaux',
            'runner-up' => 'runners-up',
            'sex' => 'sexes',
            'soliloquy' => 'soliloquies',
            'son-in-law' => 'sons-in-law',
            'syllabus' => 'syllabi',
            'testis' => 'testes',
            'thief' => 'thieves',
            'tooth' => 'teeth',
            'tornado' => 'tornadoes',
            'trilby' => 'trilbys',
            'turf' => 'turfs',
            'valve' => 'valves',
            'volcano' => 'volcanoes',
        )
    );

    /**
     * Singular rules.
     *
     * @var string[][]
     */
    private static $singular = array(
        'rules' => array(
            '/(s)tatuses$/i' => '\1\2tatus',
            '/^(.*)(menu)s$/i' => '\1\2',
            '/(quiz)zes$/i' => '\\1',
            '/(matr)ices$/i' => '\1ix',
            '/(vert|ind)ices$/i' => '\1ex',
            '/^(ox)en/i' => '\1',
            '/(alias)(es)*$/i' => '\1',
            '/(buffal|her|potat|tomat|volcan)oes$/i' => '\1o',
            '/(alumn|bacill|cact|foc|fung|nucle|radi|stimul|syllab|termin|viri?)i$/i' => '\1us',
            '/([ftw]ax)es/i' => '\1',
            '/(analys|ax|cris|test|thes)es$/i' => '\1is',
            '/(shoe|slave)s$/i' => '\1',
            '/(o)es$/i' => '\1',
            '/ouses$/' => 'ouse',
            '/([^a])uses$/' => '\1us',
            '/([m|l])ice$/i' => '\1ouse',
            '/(x|ch|ss|sh)es$/i' => '\1',
            '/(m)ovies$/i' => '\1\2ovie',
            '/(s)eries$/i' => '\1\2eries',
            '/([^aeiouy]|qu)ies$/i' => '\1y',
            '/([lr])ves$/i' => '\1f',
            '/(tive)s$/i' => '\1',
            '/(hive)s$/i' => '\1',
            '/(drive)s$/i' => '\1',
            '/(dive)s$/i' => '\1',
            '/(olive)s$/i' => '\1',
            '/([^fo])ves$/i' => '\1fe',
            '/(^analy)ses$/i' => '\1sis',
            '/(analy|diagno|^ba|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
            '/(c)riteria$/i' => '\1riterion',
            '/([ti])a$/i' => '\1um',
            '/(p)eople$/i' => '\1\2erson',
            '/(m)en$/i' => '\1an',
            '/(c)hildren$/i' => '\1\2hild',
            '/(f)eet$/i' => '\1oot',
            '/(n)ews$/i' => '\1\2ews',
            '/eaus$/' => 'eau',
            '/^(.*us)$/' => '\\1',
            '/s$/i' => '',
        ),
        'uninflected' => array(
            '.*[nrlm]ese',
            '.*deer',
            '.*fish',
            '.*measles',
            '.*ois',
            '.*pox',
            '.*sheep',
            '.*ss',
            'data',
            'police',
            'pants',
            'clothes',
        ),
        'irregular' => array(
            'abuses'     => 'abuse',
            'avalanches' => 'avalanche',
            'caches'     => 'cache',
            'criteria'   => 'criterion',
            'curves'     => 'curve',
            'emphases'   => 'emphasis',
            'foes'       => 'foe',
            'geese'      => 'goose',
            'graves'     => 'grave',
            'hoaxes'     => 'hoax',
            'media'      => 'medium',
            'neuroses'   => 'neurosis',
            'waves'      => 'wave',
            'oases'      => 'oasis',
            'valves'     => 'valve',
        )
    );

    /**
     * Words that should not be inflected.
     *
     * @var array
     */
    private static $uninflected = array(
        '.*?media', 'Amoyese', 'audio', 'bison', 'Borghese', 'bream', 'breeches',
        'britches', 'buffalo', 'cantus', 'carp', 'chassis', 'clippers', 'cod', 'coitus', 'compensation', 'Congoese',
        'contretemps', 'coreopsis', 'corps', 'data', 'debris', 'deer', 'diabetes', 'djinn', 'education', 'eland',
        'elk', 'emoji', 'equipment', 'evidence', 'Faroese', 'feedback', 'fish', 'flounder', 'Foochowese',
        'Furniture', 'furniture', 'gallows', 'Genevese', 'Genoese', 'Gilbertese', 'gold',
        'headquarters', 'herpes', 'hijinks', 'Hottentotese', 'information', 'innings', 'jackanapes', 'jedi',
        'Kiplingese', 'knowledge', 'Kongoese', 'love', 'Lucchese', 'Luggage', 'mackerel', 'Maltese', 'metadata',
        'mews', 'moose', 'mumps', 'Nankingese', 'news', 'nexus', 'Niasese', 'nutrition', 'offspring',
        'Pekingese', 'Piedmontese', 'pincers', 'Pistoiese', 'plankton', 'pliers', 'pokemon', 'police', 'Portuguese',
        'proceedings', 'rabies', 'rain', 'rhinoceros', 'rice', 'salmon', 'Sarawakese', 'scissors', 'sea[- ]bass',
        'series', 'Shavese', 'shears', 'sheep', 'siemens', 'species', 'staff', 'swine', 'traffic',
        'trousers', 'trout', 'tuna', 'us', 'Vermontese', 'Wenchowese', 'wheat', 'whiting', 'wildebeest', 'Yengeese'
    );

    /**
     * Method cache array.
     *
     * @var array
     */
    private static $cache = array();

    /**
     * The initial state of Inflector so reset() works.
     *
     * @var array
     */
    private static $initialState = array();


    
    /* Check and verifiaction methods */


    /**
     * @param string $needle
     * @param string $string
     */
    public static function contains(string $needle, string $string)
    {
        return strpos($string, $needle) !== false ? true : false;
    }

    /**
     * @param string $needle
     * @param string $string
     * @return mixed
     */
    public static function startsWith(string $needle, string $string)
    {
        return $needle === substr($string, 0, strlen($needle)) ? true : false;
    }

    /**
     * @param string $needle
     * @param string $string
     * @return mixed
     */
    public static function endsWith(string $needle, string $string)
    {
        return $needle === substr($string, -strlen($needle), strlen($needle)) ? true : false;
    }





    /* Transformation methods */


    /**
     * @param $data
     */
    public static function stringify($data)
    {
        switch (gettype($data)) {
            case 'string':
                return (string) $data;
                break;

            case 'integer':
            case 'double':
                return '' . $data . '';
                break;

            case 'boolean':
                return (string) ($data) ? 'true' : false;
                break;

            case 'NULL':
                return 'NULL';
                break;

            case 'array':
            case 'object':
                return strval($data);
                break;

            default:
                throw new Error('Unsupported Type for to string conversion! Provided Type: ' . gettype($data), 1);
                break;
        }
    }
    
    /**
     * Pluralize Word
     *
     * @param string $word
     * @return string
     */
    public static function pluralize(string $word) : string
    {
        if (isset(self::$cache['pluralize'][$word])) {
            return self::$cache['pluralize'][$word];
        }

        if (!isset(self::$plural['merged']['irregular'])) {
            self::$plural['merged']['irregular'] = self::$plural['irregular'];
        }

        if (!isset(self::$plural['merged']['uninflected'])) {
            self::$plural['merged']['uninflected'] = array_merge(self::$plural['uninflected'], self::$uninflected);
        }

        if (!isset(self::$plural['cacheUninflected']) || !isset(self::$plural['cacheIrregular'])) {
            self::$plural['cacheUninflected'] = '(?:' . implode('|', self::$plural['merged']['uninflected']) . ')';
            self::$plural['cacheIrregular']   = '(?:' . implode('|', array_keys(self::$plural['merged']['irregular'])) . ')';
        }

        if (preg_match('/(.*)\\b(' . self::$plural['cacheIrregular'] . ')$/i', $word, $regs)) {
            self::$cache['pluralize'][$word] = $regs[1] . $word[0] . substr(self::$plural['merged']['irregular'][strtolower($regs[2])], 1);

            return self::$cache['pluralize'][$word];
        }

        if (preg_match('/^(' . self::$plural['cacheUninflected'] . ')$/i', $word, $regs)) {
            self::$cache['pluralize'][$word] = $word;

            return $word;
        }

        foreach (self::$plural['rules'] as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                self::$cache['pluralize'][$word] = preg_replace($rule, $replacement, $word);

                return self::$cache['pluralize'][$word];
            }
        }
    }


    /**
     * Singularize Word
     *
     * @param string $word
     * @return string
     */
    public static function singularize(string $word) : string
    {
        if (isset(self::$cache['singularize'][$word])) {
            return self::$cache['singularize'][$word];
        }

        if (!isset(self::$singular['merged']['uninflected'])) {
            self::$singular['merged']['uninflected'] = array_merge(
                self::$singular['uninflected'],
                self::$uninflected
            );
        }

        if (!isset(self::$singular['merged']['irregular'])) {
            self::$singular['merged']['irregular'] = array_merge(
                self::$singular['irregular'],
                array_flip(self::$plural['irregular'])
            );
        }

        if (!isset(self::$singular['cacheUninflected']) || !isset(self::$singular['cacheIrregular'])) {
            self::$singular['cacheUninflected'] = '(?:' . implode('|', self::$singular['merged']['uninflected']) . ')';
            self::$singular['cacheIrregular'] = '(?:' . implode('|', array_keys(self::$singular['merged']['irregular'])) . ')';
        }

        if (preg_match('/(.*)\\b(' . self::$singular['cacheIrregular'] . ')$/i', $word, $regs)) {
            self::$cache['singularize'][$word] = $regs[1] . $word[0] . substr(self::$singular['merged']['irregular'][strtolower($regs[2])], 1);

            return self::$cache['singularize'][$word];
        }

        if (preg_match('/^(' . self::$singular['cacheUninflected'] . ')$/i', $word, $regs)) {
            self::$cache['singularize'][$word] = $word;

            return $word;
        }

        foreach (self::$singular['rules'] as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                self::$cache['singularize'][$word] = preg_replace($rule, $replacement, $word);

                return self::$cache['singularize'][$word];
            }
        }

        self::$cache['singularize'][$word] = $word;

        return $word;
    }

    /**
     * Slugify string
     *
     * @param string $string
     * @return string
     */
    public static function slugify(string $string) : string
    {
        $string = Str::removeAccents($string);
        
        // replace non letter or digits by -
        $string = preg_replace('~[^\pL\d]+~u', '-', $string);

        // remove unwanted characters
        $string = preg_replace('~[^-\w]+~', '', $string);

        // remove duplicate -
        $string = preg_replace('~-+~', '-', trim($string, '-'));

        // lowercase
        $string = strtolower($string);

        return empty($string)
            ? null
            : $string;
    }

    /**
     * Converts a word into the format for a database table name.
     * Converts 'ModelName' to 'model_name'.
     */
    public static function tableize(string $word) : string
    {
        return strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $word));
    }

    /**
     * Converts a word into the format for a class name.
     * Converts 'table_name' to 'TableName'.
     */
    public static function classify(string $word) : string
    {
        return str_replace([' ', '_', '-'], '', ucwords($word, ' _-'));
    }

    /**
     * Camelizes a word. This uses the classify() method and turns the first character to lowercase.
     */
    public static function camelize(string $word) : string
    {
        return lcfirst(self::classify($word));
    }

    /**
     * removes all special characters from a given string
     *
     * @param string $text
     * @return string
     */
    public static function clean(string $string): string
    {
        $utf8 = [
            '/[áàâãªä]/u' => 'a',
            '/[ÁÀÂÃÄ]/u' => 'A',
            '/[ÍÌÎÏ]/u' => 'I',
            '/[íìîï]/u' => 'i',
            '/[éèêë]/u' => 'e',
            '/[ÉÈÊË]/u' => 'E',
            '/[óòôõºö]/u' => 'o',
            '/[ÓÒÔÕÖ]/u' => 'O',
            '/[úùûü]/u' => 'u',
            '/[ÚÙÛÜ]/u' => 'U',
            '/ç/' => 'c',
            '/Ç/' => 'C',
            '/ñ/' => 'n',
            '/Ñ/' => 'N',
            '/–/' => '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u' => ' ', // Literally a single quote
            '/[“”«»„]/u' => ' ', // Double quote
            '/ /' => ' ', // nonbreaking space (equiv. to 0x160)
        ];

        $result = preg_replace(array_keys($utf8), array_values($utf8), $string);

        return $result ?? $string;
    }

    public static function removeAccents(string $string) : string
    {
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        return str_replace($a, $b, $string);
    }

    public static function obfuscateEmail(string $email, int $minFill = 1) : string
    {
        return preg_replace_callback(
            '/^(.)(.*?)([^@]?)(?=@[^@]+$)/u',
            function ($m) use ($minFill) {
                return $m[1]
                            . str_repeat("*", max($minFill, mb_strlen($m[2], 'UTF-8')))
                            . ($m[3] ?: $m[1]);
            },
            $email
        );
    }


    /**
     * Converts a stringified value into its correct type
     *
     * @param string $string
     */
    public static function getRealType(string $string)
    {
        if (Number::isFloat($string)) {
            return (float) Number::convert2Float($string);
        }

        if (Number::isInteger($string)) {
            return (int) Number::convert2Integer($string);
        }

        if (strtolower($string) === 'true') {
            return true;
        }

        if (strtolower($string) === 'false') {
            return false;
        }

        if (strtolower($string) === 'null') {
            return null;
        }

        return $string;
    }

    public static function isSerialized(string $data)
    {
        $data = trim($data);
        
        if ($data == 'N;') {
            return true;
        }

        if (!preg_match('/^([adObis]):/', $data, $badions)) {
            return false;
        }
        
        switch ($badions[1]) {
            case 'a':
            case 'O':
            case 's':
                if (preg_match("/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data)) {
                    return true;
                }
            break;

            case 'b':
            case 'i':
            case 'd':
                if (preg_match("/^{$badions[1]}:[0-9.E-]+;\$/", $data)) {
                    return true;
                }
            break;
        }
        
        return false;
    }

    public static function unescapeUTF8EscapeSeq($str)
    {
        return preg_replace_callback(
            "/\\\u([0-9a-f]{4})/i",
            function ($matches) {
                return html_entity_decode('&#x' . $matches[1] . ';', ENT_QUOTES, 'UTF-8');
            },
            $str
        );
    }

    public static function replaceFirst($search, $replace, $subject)
    {
        $search = '/'.preg_quote($search, '/').'/';
        return preg_replace($search, $replace, $subject, 1);
    }

    public static function replaceLast($search, $replace, $str)
    {
        if (($pos = strrpos($str, $search)) !== false) {
            $search_length  = strlen($search);
            $str    = substr_replace($str, $replace, $pos, $search_length);
        }
        return $str;
    }
}
