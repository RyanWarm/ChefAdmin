class DReader {
    private $first_source;
    private $second_source;

    public function __construct($first_source, $second_source) {
        $this->first_source = $first_source;
        $this->second_source = $second_source;
    }

    public function __get($key) {
        if (isset($this->first_source[$key])) {
            return $this->first_source($key);
        } elseif (!empty($second_source)) {
            return $second_source[$key];
        } else {
            return '';
        }
    }

    public function __set($key) {
        
    }
}
