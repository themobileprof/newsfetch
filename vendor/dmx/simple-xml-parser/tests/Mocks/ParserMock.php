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

namespace DMX\SimpleXML\Tests\Mocks;

use DMX\SimpleXML\Parser;

class ParserMock extends Parser
{
    /**
     * ParserMock constructor.
     *
     * @param string $xmlContent
     */
    public function __construct(string $xmlContent = '<xml><foo bar="true"/></xml>')
    {
        parent::__construct($xmlContent);
    }

    /**
     * Call protected method simpleXMLOptions().
     *
     * @return int
     */
    public function callSimpleXMLOptions(): int
    {
        return $this->simpleXMLOptions();
    }

    /**
     * Call protected method simpleXMLElement().
     *
     * @return \SimpleXMLElement
     */
    public function callSimpleXMLElement(): \SimpleXMLElement
    {
        return $this->simpleXMLElement();
    }

    /**
     * Call protected method normalizeArray().
     *
     * @param array $array
     *
     * @return array
     */
    public function callNormalizeArray(array $array): array
    {
        return $this->normalizeArray($array);
    }
}
