<?php
/**
 * ----------------------------------------------------------------------------
 * This code is part of an application or library developed by Datamedrix and
 * is subject to the provisions of your License Agreement with
 * Datamedrix GmbH.
 *
 * @copyright (c) 2018 Datamedrix GmbH
 * ----------------------------------------------------------------------------
 * @author Christian Graf <c.graf@datamedrix.com>
 */

declare(strict_types=1);

namespace DMX\SimpleXML;

use SimpleXMLElement;

class Parser
{
    /**
     * @var string
     */
    protected $xmlContent;

    /**
     * @var bool
     */
    protected $validateDTD = false;

    /**
     * @var SimpleXMLElement|null
     */
    private $xml = null;

    /**
     * @var array|null
     */
    private $arrayContent = null;

    /**
     * Parser constructor.
     *
     * @param string $xmlContent
     */
    public function __construct(string $xmlContent)
    {
        $this->xmlContent = preg_replace('/<!--(.*|\n)-->/Uis', '', trim($xmlContent));
    }

    /**
     * @param bool|null $validate
     *
     * @return bool
     */
    public function validateDTD(?bool $validate = null): bool
    {
        if ($validate !== null) {
            $this->validateDTD = $validate;
        }

        return $this->validateDTD;
    }

    /**
     * @return int
     */
    protected function simpleXMLOptions(): int
    {
        $options = LIBXML_DTDATTR | LIBXML_NOBLANKS | LIBXML_NOCDATA;
        if ($this->validateDTD) {
            $options = $options | LIBXML_DTDVALID;
        }

        return $options;
    }

    /**
     * @return SimpleXMLElement
     */
    protected function simpleXMLElement(): SimpleXMLElement
    {
        if ($this->xml === null) {
            $this->xml = new SimpleXMLElement($this->xmlContent, $this->simpleXMLOptions(), false);

            /* --- remove empty tags ---
                Select the document element
                /*
                Any descendant of the document element
                /*//*
                ...with only whitespaces as text content (this includes descendants)
                /*//*[normalize-space(.) = ""]
                ...and no have attributes
                /*//*[normalize-space(.) = "" and not(@*)]
                ...or an descendants with attributes
                /*//*[normalize-space(.) = "" and not(@* or .//*[@*])]
                ...or a comment
                /*//*[normalize-space(.) = "" and not(@* or .//*[@*] or .//comment())]
                ...or a processing instruction
                /*//*[
                normalize-space(.) = "" and not(@* or .//*[@*] or .//comment() or .//processing-instruction())
                ]
            */
            $xpath = '/*//*[
                   normalize-space(.) = "" and
                   not (
                    @* or 
                    .//*[@*] or
                    .//processing-instruction()
                   )
            ]';
            foreach (array_reverse($this->xml->xpath($xpath)) as $remove) {
                unset($remove[0]);
            }
        }

        return $this->xml;
    }

    /**
     * @param array $array
     *
     * @return array
     */
    protected function normalizeArray(array $array): array
    {
        if (!isset($array['@attributes']) && count(array_filter(array_keys($array), 'is_string')) > 0) {
            $array['@attributes'] = ['__' => null];
        }

        foreach ($array as $i => $item) {
            if ($i === '@attributes') {
                continue;
            }

            if (is_array($item)) {
                $array[$i] = $this->normalizeArray($item);
            }
        }

        return $array;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        if ($this->arrayContent === null) {
            $json = json_encode($this->simpleXMLElement());
            $schema = json_decode($json, true);

            $schema = $this->normalizeArray($schema);
            $this->arrayContent = $schema;
        }

        return $this->arrayContent;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
