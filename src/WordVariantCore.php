<?php namespace CupOfTea\WordVariant;

use CupOfTea\Package\Package;

class WordVariantCore
{
    use Package;
    
    /**
     * Package Name.
     *
     * @const string
     */
    const PACKAGE = 'CupOfTea/WordVariant';
    /**
     * Package Version.
     *
     * @const string
     */
    const VERSION = '1.0.0';
    
    /**
     * All characters that are allowed in the resulting variants.
     *
     * @protected null|Array
     */
    protected $allowed_characters;
    
    /**
     * Alternative symbol combinations to represent a letter.
     *
     * @protected Array
     */
    protected $variants = [
        'a' => [4, '/-\\', '@', '^', '/\\', '//-\\\\', '/=\\'],
        'b' => [8, ']3', ']8', '|3', '|8', ']]3', 13],
        'c' => ['(', '{', '[[', '<', '€'],
        'd' => [')', '[}', '|)', '|}', '|>', '[>', ']])', 'Ð'],
        'e' => [3, 'ii', '€'],
        'f' => ['|=', '(=', ']]=', 'ph'],
        'g' => [6, 9, '(_>', '[[6', '&', '('],
        'h' => ['#', '|-|', '(-)', ')-(', '}{', '}-{', '{-}', '/-/', '\\-\\', '|~|', '[]-[]', ']]-[[', '╫'],
        'i' => [1, '!', '|', '][', '[]'],
        'j' => ['_|', 'u|', ';_[]', ';_[['],
        'k' => ['|<', '|{', '][<', ']]<', '[]<'],
        'l' => ['|', 1, '|_', '[]_', '][_', '£'],
        'm' => ['/\\/\\', '|\\/|', '[\\/]', '(\\/)', '/V\\', '[]V[]', '\\\\\\', '(T)', '^^', '.\\\\', '//.', '][\\\\//][', 'JVL'],
        'n' => ['/\\/', '|\\|', '(\\)', '/|/', '[\\]', '{\\}', '][\\][', '[]\\[]', '~'],
        'o' => [0, '()', '[]', '<>', '*', '[[]]'],
        'p' => ['|d', '|*', '|>', '[]d', '][d'],
        'q' => [0, 'o', '(,)', 'O\\', '[]\\'],
        'r' => ['|2', '|?', '|-', ']]2', '[]2', '][2'],
        's' => [5, '$', 'š', 'zz'],
        't' => [7, '+', "']'", '7`', '~|~', '-|-', "']['", '"|"', '†'],
        'u' => ['l_l', '(_)', '|_|', '\\_\\', '/_/', '\\_/', '[]_[]', ']_[', 'μ'],
        'v' => ['\\/', '\\\\//', '√'],
        'w' => ['\\/\\/', '|/\\|', '[/\\]', '(/\\)', 'vv', '///', '\\^/', '\\\\/\\//', '1/\\/', '\\/1/', '1/1/'],
        'x' => ['><', '}{', ')(', ']['],
        'y' => ["'/", '%', '`/', '\\j', '``//', '¥', 'j', '\\|/', '-/'],
        'z' => [2, '7_', '`/_'],
        'ph' => ['f', '|>]-['],
        'cks' => ['xx'],
    ];
    
    /**
     * Create a WordVariant instance.
     *
     * @param string All characters that are allowed in the resulting variants.
     * @return void
     */
    public function __construct($allowed_characters = null)
    {
        $this->setAllowedCharacters($allowed_characters);
    }
    
    /**
     * Set the allowed characters for the resulting variants.
     *
     * @param null|string [$allowed_characters = null] All characters that are allowed in the resulting variants.
     * @return void
     */
    public function setAllowedCharacters($allowed_characters = null)
    {
        $this->allowed_characters = is_array($allowed_characters) ? implode($allowed_characters) : $allowed_characters;
        $this->filterVariants();
    }
    
    /**
     * Get variants for a specific letter.
     *
     * @param  string $letter Letter to get variants of
     * @return array Variants.
     */
    public function getVariant($letter)
    {
        return isset($this->variants[$letter]) ? $this->variants[$letter] : [];
    }
    
    /**
     * Add one or more variants for a letter.
     *
     * @param string   $letter   Letter to add variant(s) for.
     * @param string|array $variants Variant(s) to be added for the letter.
     * @return void
     */
    public function addVariant($letter, $variants)
    {
        if (! isset($this->variants[$letter])) {
            $this->variants[$letter] = [];
        }
        
        foreach ((array) $variants as $variant) {
            if (! preg_match('/[^' . preg_quote($this->allowed_characters) . ']/', $variant)) {
                $this->variants[$letter] = array_push($this->variants[$letter], $variant);
            }
        }
        
        $this->variants[$letter] = array_unique($this->variants[$letter]);
        if (! count($this->variants[$letter])) {
            unset($this->variants[$letter]);
        }
    }
    
    /**
     * Get all variants.
     *
     * @return array All letters and their variants.
     */
    public function getVariants()
    {
        return $this->variants;
    }
    
    /**
     * Set the varitants Array.
     *
     * @param array $variants All letters and their variants.
     * @return void
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
        $this->filterVariants();
    }
    
    /**
     * Get variants for a word.
     *
     * @param  string $word Word to find variants for.
     * @return array    All word variants.
     */
    public function get($word)
    {
        if ($word == '') {
            return [];
        }
        
        $word_length = strlen($word);
        $word_variants = [$word];
        
        for ($l = 1; $l <= $word_length; $l++) {
            for ($i = 0; $i < $word_length - ($l - 1); $i++) {
                $replace = substr($word, $i, $l);
                
                $left_bounds = [0, max($i - 1, 0)];
                $right_bounds = [min($i + $l + 1, $word_length), $word_length - min($i + $l + 1, $word_length)];
                
                $left = substr($word, $left_bounds[0], $left_bounds[1]);
                $right = substr($word, $right_bounds[0], $right_bounds[1]);
                
                $left_variants = $this->all($left);
                $right_variants = $this->all($right);
                
                foreach ($this->variants as $letter => $variants) {
                    foreach ($variants as $variant) {
                        $replace = str_replace($letter, $variant, $replace);
                    }
                }
                
                $word_variants[] = $word_variant = substr_replace($word, $replace, $i, $l);
                
                foreach ($left_variants as $left_variant) {
                    $word_variants[] = substr_replace($word_variant, $left_variant, $left_bounds[0], $left_bounds[1]);
                }
                foreach ($right_variants as $right_variant) {
                    $word_variants[] = substr_replace($word_variant, $right_variant, $right_bounds[0], $right_bounds[1]);
                }
            }
        }
        
        return array_unique($word_variants);
    }
    
    /**
     * Removes any variants that are not allowed because they contain forbidden characters.
     *
     * @return void
     */
    protected function filterVariants()
    {
        if ($this->allowed_characters !== null) {
            foreach ($this->variants as $letter => $variants) {
                $this->variants[$letter] = array_filter($variants, function ($variant) {
                    return ! preg_match('/' . preg_quote($this->allowed_characters) . '/', $variant);
                });
                
                if (! count($this->variants[$letter])) {
                    unset($this->variants[$letter]);
                }
            }
        }
    }
}
