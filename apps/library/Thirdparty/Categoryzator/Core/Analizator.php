<?php
/**
 * Class Analizator
 *
 * @author   Slava Basko <basko.slava@gmail.com>
 */

namespace Categoryzator\Core;


class Analizator {

    /**
     * @var string
     */
    private $Text = null;

    /**
     * Type - return single or multiple category
     *
     * @var null
     */
    private $analizType = null;

    /**
     * Array of categories
     *
     * @var array|mixed
     */
    private $categories = array();

    /**
     * Counted words in text
     *
     * @var array
     */
    private $countedWords = array();

    /**
     * Categories entriy holder
     *
     * @var array
     */
    private $entries = array();

    /**
     * Get categories and set analiz type
     *
     * @param $text
     * @param $analizType
     * @throws CategoryzatorException
     */
    public function __construct($text, $analizType)
    {
        if (!$text instanceof Text) {
            throw new CategoryzatorException('Param $text must be a instance of Categoryzator\Core\Text object');
        }
        $this->Text = $text;

        $this->analizType = $analizType;

        $this->Text->content = strtolower($this->Text->content);

        $cat = new Parser();
        $this->categories = $cat->getCategories();
    }

    /**
     * Category detect handler
     *
     * @return array|null
     */
    public function doAnaliz()
    {
        // WARNING --> keep order
        $this->countWords();
        $this->countEntry();
        $this->searchCategory();
        //
        return $this->Text;
    }

    /**
     * Split text and count words
     *
     * @return bool
     */
    private function countWords()
    {
        $words = explode(' ', $this->Text->content);

        foreach ($words as $word) {
            $word = preg_replace("/[^\w$]/", '', $word);

            if (!array_key_exists($word, $this->countedWords)) {
                $this->countedWords[$word] = 0;
            }

            if (array_key_exists($word, $this->countedWords)) {
                $this->countedWords[$word]++;
            }
        }

        $this->Text->countedWords = $this->countedWords;

        return true;
    }

    /**
     * Detect categories entry in text
     */
    private function countEntry()
    {

        $entries = array();
        foreach ($this->categories as $categoryKey => $category) {

            foreach ($category as $keyWord) {

                if (array_key_exists($keyWord, $this->countedWords)) {

                    if (!array_key_exists($keyWord, $entries)) {
                        $entries[$keyWord] = 0;
                    }

                    if (array_key_exists($keyWord, $entries)) {
                        $entries[$keyWord]++;
                    }

                }

            }

            foreach ($category as $keyWord) {
                if (array_key_exists($keyWord, $entries)) {

                    if (!array_key_exists($categoryKey, $this->entries)) {
                        //$this->entries[$categoryKey] = 0;
                        $this->entries[$categoryKey] = array(
                            'key' => $categoryKey,
                            'entry' => 0
                        );
                    }

                    if (array_key_exists($categoryKey, $this->entries)) {
                        //$this->entries[$categoryKey]++;
                        $this->entries[$categoryKey]['entry']++;
                    }

                }
            }

        }

        $this->Text->countEntry = $this->entries;
    }

    /**
     * Return STRING single category or ARRAY of categories
     *
     * @return array|null
     */
    private function searchCategory()
    {
        $categories = array_values($this->entries);
        $category = 'other';

        if ($this->analizType === 1) {
            $tmpIndex = 0;
            $tmpMax = 0;
            if (!empty($this->entries)) {
                foreach ($categories as $index => $node) {

                    if ($node['entry'] > $tmpMax) {
                        $tmpMax = $node['entry'];
                        $tmpIndex = $index;
                    }

                }
                $category = $categories[$tmpIndex]['key'];
            }
        }
        unset($tmpIndex);
        unset($tmpMax);
        unset($index);

        if ($this->analizType === 2) {
            $category = array();
            foreach ($categories as $node) {
                $category[] = $node['key'];
            }
        }
        unset($node);

        $this->Text->category = $category;
    }

} 