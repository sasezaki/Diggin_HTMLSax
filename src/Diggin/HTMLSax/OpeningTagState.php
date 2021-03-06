<?php
/**
 * Dealing with opening Diggin tags
 * @package Diggin_HTMLSax
 * @access protected
 */

namespace Diggin\HTMLSax;

use Diggin\HTMLSax\StateInterface;



class OpeningTagState {
    /**
     * Handles attributes
     * @param string attribute name
     * @param string attribute value
     * @return void
     * @access protected
     * @see Diggin_HTMLSax_AttributeStartState
     */
    function parseAttributes($context) {
        $Attributes = array();

        $context->ignoreWhitespace();
        $attributename = $context->scanUntilCharacters("=/> \n\r\t");
        while ($attributename != '') {
            $attributevalue = NULL;
            $context->ignoreWhitespace();
            $char = $context->scanCharacter();
            if ($char == '=') {
                $context->ignoreWhitespace();
                $char = $context->ScanCharacter();
                if ($char == '"') {
                    $attributevalue= $context->scanUntilString('"');
                    $context->IgnoreCharacter();
                } else if ($char == "'") {
                    $attributevalue = $context->scanUntilString("'");
                    $context->IgnoreCharacter();
                } else {
                    $context->unscanCharacter();
                    $attributevalue =
                        $context->scanUntilCharacters("> \n\r\t");
                }
            } else if ($char !== NULL) {
                $attributevalue = NULL;
                $context->unscanCharacter();
            }
            $Attributes[$attributename] = $attributevalue;

            $context->ignoreWhitespace();
            $attributename = $context->scanUntilCharacters("=/> \n\r\t");
        }
        return $Attributes;
    }

    /**
     * @param Diggin_HTMLSax_StateParser subclass
     * @return constant Diggin_HTMLSax_StateInterface::STATE_START
     * @access protected
     */
    function parse($context) {
        $tag = $context->scanUntilCharacters("/> \n\r\t");
        if ($tag != '') {
            $this->attrs = array();
            $Attributes = $this->parseAttributes($context);
            $char = $context->scanCharacter();
            if ($char == '/') {
                $char = $context->scanCharacter();
                if ($char != '>') {
                    $context->unscanCharacter();
                }
                $context->handler_object_element->
                {$context->handler_method_opening}($context->htmlsax, $tag,
                    $Attributes, TRUE);
                $context->handler_object_element->
                {$context->handler_method_closing}($context->htmlsax, $tag,
                    TRUE);
            } else {
                $context->handler_object_element->
                {$context->handler_method_opening}($context->htmlsax, $tag,
                    $Attributes, FALSE);
            }
        }
        return StateInterface::STATE_START;
    }
}