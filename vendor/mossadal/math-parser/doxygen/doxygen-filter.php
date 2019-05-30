#!/usr/bin/php
<?php
/**
 * Simple input filter for using Doxygen with PHP code
 *
 * It optimize the documention like:
 * - rewrites php namespaces to use :: instead of \
 * - replaces \@retval with \@retval
 * - appends variable names to \@var commands
 * - remove unused annotations
 *   \@codeCoverageIgnore
 *
 * @see http://www.doxygen.org/
 * @see http://www.stack.nl/~dimitri/doxygen/config.html#cfg_input_filter
 */
define ('NSS', '::');

// read in the source file
$source = file_get_contents($_SERVER['argv'][1]);

// add automatic tags to const variables
$source = preg_replace_callback(
    '#(\*/\s+const (SESSION|REGISTRY|CACHE)_[A-Z_]+\s*=\s*["\']?(.*?)["\']?;)#s',
    function($matches) {
        return '* @'.strtolower($matches[2]).'{'.$matches[3].'}'.PHP_EOL.$matches[1];
    },
    $source
);

// use tokenizer to make context specific replacements using buffer

$tokens = token_get_all($source);
$buffer = null;
foreach ($tokens as $token) {
    if (is_string($token)) {
        if ((! empty($buffer)) && ($token == ';')) {
            echo $buffer;
            unset($buffer);
        }
        echo $token;
    } else {
        list($id, $text) = $token;
        switch ($id) {
            case T_DOC_COMMENT :
                // remove @codeCoverageIgnore
		$text = preg_replace('#\*\s*@(codeCoverageIgnore)(\s[^\n\r]*)?\s+#', '', $text);
                // replace @retval with @retval
		$text = preg_replace('#@retval\s#', '@retval ', $text);
                // replace starting namespace separator
		$text = preg_replace('#(\s)\\\\([A-Z]\w+)#ms', '$1$2', $text);
		do {
  		    // replace backslash in comment
		    $text = preg_replace('#(\*\s*[^*]*?\b\w+[^\n\r ]+)\\\\([A-Z])#ms', '$1'.NSS.'$2', $text, 1, $count);
		} while ($count);
                // optimize @var tags
                if (preg_match('#@var\s+[^\$]*\*/#ms', $text)) {
                    $buffer = preg_replace('#(@var\s+[^\n\r]+)(\n\r?.*\*/)#ms',
                        '$1 \$\$\$$2', $text);
                } else {
                    echo $text;
                }
                break;

            case T_VARIABLE :
                if ((! empty($buffer))) {
                    echo str_replace('$$$', $text, $buffer);
                    unset($buffer);
                }
                echo $text;
                break;

		case T_INLINE_HTML :
                // replace @namespace tags
		do {
               	    $text = preg_replace('#(\*\s*@namespace\s[^\n\r]+)\\\\#', '$1::', $text,1, $count);
		} while ($count);
		do {
		    // replace backslash
		    $text = preg_replace('#(\*\s*[^*]*?\b\w+[^\n\r ]+)\\\\([A-Z])#ms', '$1'.NSS.'$2', $text, 1, $count);
		} while ($count);
		do {
		    // remove starting backslash
		    $text = preg_replace('#(\*\s*[^*]*\s)\\\\([A-Z])#ms', '$1$2', $text, 1, $count);
		} while ($count);
                if ((! empty($buffer))) {
                    $buffer .= $text;
                } else {
                    echo $text;
                }
		break;

            default:
                if ((! empty($buffer))) {
                    $buffer .= $text;
                } else {
                    echo $text;
                }
                break;
        }
    }
}
